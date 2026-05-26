<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;

use App\Models\Admin;
use App\Models\CourseLevel;
use App\Models\CourseLevelPurchase;
use App\Models\Package;
use App\Models\PackagePurchase;
use App\Models\PointBalance;
use App\Models\Purchase;

use App\Repositories\Purchase\PurchaseRepositoryInterface;

use App\Services\ThirdParty\Firebase\FirebaseNotificationService;

class PurchaseService
{

    public function __construct(protected PurchaseRepositoryInterface $repo)
    {

    }

    public function all(?array $filters = [], ?string $status = null, ?int $page = null, ?int $limit = null)
    {
        $query = $this->repo->all(
            $filters,
            $status,
            $limit
        );

        return $page
            ? $query->paginate($limit ?? config('common.list_count'))
            : $query->get();
    }

    public function listForUser(int $userId, ?int $page = null, ?int $limit = null)
    {
        $query = $this->repo->listForUser(
            $userId,
            $limit
        );

        return $page
            ? $query->paginate($limit ?? config('common.list_count'))
            : $query->get();
    }

    /**
     * Create a purchase request for a user
     *
     * @param int $userId
     * @param string $purchasableType
     * @param int $purchasableId
     * @param int $quantity
     * @return Purchase
     * @throws \RuntimeException If purchasable item not found or invalid
     */
    public function createPurchase(int $userId, string $purchasableType, int $purchasableId, int $quantity = 1): Purchase
    {
        // Validate purchasable type
        $this->validatePurchasableType($purchasableType);

        // Validate the purchasable item exists and get its price
        $purchasable = $this->getPurchasableItem($purchasableType, $purchasableId);

        if (!$purchasable) {
            throw new \RuntimeException('Purchasable item not found');
        }

        // Validate that courses cannot be purchased directly (must purchase course levels)
        if ($purchasable instanceof \App\Models\Course) {
            throw new \RuntimeException('Courses cannot be purchased directly. Please purchase a specific course level.');
        }

        // Prevent buying the same Course Level while the user has a same course level purchased and it's in validity period
        if ($purchasable instanceof CourseLevel) {
            $hasValidQuery = CourseLevelPurchase::where('user_id', $userId)
                ->where('course_level_id', $purchasable->id)
                ->valid();
                // ->exists();
            $hasValid = $hasValidQuery->exists();
            if ($hasValid) {
                $existingCourseLevelPurchase = $hasValidQuery->first();
                (new FirebaseNotificationService($existingCourseLevelPurchase, $existingCourseLevelPurchase->user, $existingCourseLevelPurchase->user_id, 'user'))
                ->send([
                    'title' => 'Course level already bought',
                    'preview' => "You already bought the ({$existingCourseLevelPurchase->courseLevel->course->name}) class"
                ]);
                throw new \RuntimeException('You already have an active course level with remaining lesson days. Use it up or wait until it is completed before buying the same course level again.');
            }
        }

        // Prevent buying the same Package while the user has a valid, un-completed package purchase
        if ($purchasable instanceof Package) {
            $hasValidQuery = PackagePurchase::where('user_id', $userId)
                ->where('package_id', $purchasable->id)
                ->valid();
                // ->exists();
            $hasValid = $hasValidQuery->exists();
            if ($hasValid) {
                $existingPackagePurchase = $hasValidQuery->first();
                (new FirebaseNotificationService($existingPackagePurchase, $existingPackagePurchase->user, $existingPackagePurchase->user_id, 'user'))
                ->send([
                    'title' => 'Package already bought',
                    'preview' => "You already bought the ({$existingPackagePurchase->package->name}) package"
                ]);
                throw new \RuntimeException('You already have an active package with remaining walk-in days. Use it up or wait until it is completed before buying the same package again.');
            }
        }

        // Calculate total points needed (same logic as deposit conversion)
        $unitPrice = $this->getItemPrice($purchasable);
        $rate = (float) config('payments.points_per_unit', 1);
        $minor = (int) config('payments.currency_minor_unit', 100);
        $pointsPerUnit = (int) floor(($unitPrice / $minor) * $rate);
        $totalPoints = $pointsPerUnit * $quantity;

        try{
            DB::beginTransaction();
            $purchase = $this->repo->create([
                'user_id' => $userId,
                'purchasable_type' => $purchasableType,
                'purchasable_id' => $purchasableId,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_points' => $totalPoints,
            ]);

            $this->confirmPurchase(Admin::first(), $purchase->id);

            $type = ucfirst($purchase->type);
            (new FirebaseNotificationService($purchase, \App\Models\Admin::all(), $purchase->user_id, 'user'))
            ->send([
                'title' => "{$type} purchase by user",
                'preview' => "{$purchase->user->name} has purchased {$type}: {$purchase->purchasable->name}"
            ]);

            DB::commit();
            return $purchase;
        }catch(\Exception $e){
            DB::rollBack();

            throw new \RuntimeException($e->getMessage(), 402);
        }
    }

    public function updatePurchase(int $id, array $data)
    {
        $purchase = $this->repo->findById($id);
        if(!$purchase){
            throw new \RuntimeException('Purchase not found');
        }
        return $this->repo->update($purchase, $data);
    }

    public function detail(int $id)
    {
        $purchase = $this->repo->findById($id);
        if(!$purchase){
            throw new \RuntimeException('Purchase not found');
        }
        return $purchase;
    }

    /**
     * Confirm a purchase and deduct points from user's balance
     *
     * @param Admin $admin
     * @param int $purchaseId
     * @return Purchase
     * @throws \RuntimeException
     */
    public function confirmPurchase(Admin $admin, int $purchaseId): Purchase
    {
        return DB::transaction(function () use ($admin, $purchaseId) {
            $purchase = $this->repo->findForUpdate($purchaseId);

            if (! $purchase) {
                throw new \RuntimeException('Purchase not found');
            }

            if ($purchase->status !== 'pending') {
                throw new \RuntimeException('Purchase is not pending');
            }

            // Verify the purchasable item still exists and price hasn't changed
            $purchasable = $this->getPurchasableItem($purchase->purchasable_type, $purchase->purchasable_id);
            if (!$purchasable) {
                throw new \RuntimeException('Purchasable item no longer available');
            }

            $currentPrice = $this->getItemPrice($purchasable);
            if ($currentPrice != $purchase->unit_price) {
                throw new \RuntimeException('Item price has changed since purchase request');
            }

            // Recalculate points to ensure consistency
            $rate = (float) config('payments.points_per_unit', 1);
            $minor = (int) config('payments.currency_minor_unit', 100);
            $expectedPoints = (int) floor(($currentPrice / $minor) * $rate) * $purchase->quantity;

            if ($expectedPoints != $purchase->total_points) {
                throw new \RuntimeException('Point calculation mismatch');
            }

            $purchase = $this->repo->update($purchase, [
                'status' => 'confirmed',
                'admin_id' => $admin->id,
                'confirmed_at' => Carbon::now(),
            ]);

            if ($purchasable instanceof Package) {
                $totalDays = (int) $purchasable->days * $purchase->quantity;
                PackagePurchase::create([
                    'user_id' => $purchase->user_id,
                    'package_id' => $purchasable->id,
                    'purchase_id' => $purchase->id,
                    'total_days' => $totalDays,
                    'remaining_days' => $totalDays,
                    'completed' => false,
                ]);
            }

            // If the purchasable is a CourseLevel, record course_level_purchases for the user.
            if ($purchasable instanceof CourseLevel) {
                $confirmedAt = $purchase->confirmed_at ?? Carbon::now();

                // Validity period: confirmed_at + number of LESSON days (type='Lesson')
                $lessonDaysCount = (int) $purchasable->lessonDays()
                    ->count();

                $validFrom = $confirmedAt;
                $validUntil = (clone $confirmedAt)->addDays($lessonDaysCount);

                $courseLevelPurchase = CourseLevelPurchase::firstOrCreate(
                    ['purchase_id' => $purchase->id],
                    [
                        'user_id' => $purchase->user_id,
                        'course_level_id' => $purchasable->id,
                        'valid_from' => $validFrom,
                        'valid_until' => $validUntil
                    ]
                );

                // If already exists (e.g. confirm retried), keep progress but refresh validity fields.
                if (! $courseLevelPurchase->wasRecentlyCreated) {
                    $courseLevelPurchase->update([
                        'user_id' => $purchase->user_id,
                        'course_level_id' => $purchasable->id,
                        'valid_from' => $validFrom,
                        'valid_until' => $validUntil,
                    ]);
                }
            }

            // Deduct points from user's balance (negative value)
            $itemName = $purchasable->name ?? 'Item';
            $note = "Purchase confirmed: {$itemName} x{$purchase->quantity}";
            PointBalance::adjustPointsForUser(
                $purchase->user_id,
                -$purchase->total_points, // Negative for deduction
                'purchase',
                $purchase->id,
                'purchase',
                $note
            );

            return $purchase;
        });
    }

    /**
     * Reject a purchase request
     *
     * @param Admin $admin
     * @param int $purchaseId
     * @param string|null $note
     * @return Purchase
     * @throws \RuntimeException
     */
    public function rejectPurchase(Admin $admin, int $purchaseId, ?string $note = null): Purchase
    {
        return DB::transaction(function () use ($admin, $purchaseId, $note) {
            $purchase = $this->repo->findForUpdate($purchaseId);

            if (! $purchase) {
                throw new \RuntimeException('Purchase not found');
            }

            if ($purchase->status !== 'pending') {
                throw new \RuntimeException('Purchase is not pending');
            }

            $purchase = $this->repo->update($purchase, [
                'status' => 'rejected',
                'admin_id' => $admin->id,
                'admin_note' => $note,
                'confirmed_at' => Carbon::now(),
            ]);

            return $purchase;
        });
    }

    /**
     * Get a purchasable item by type and ID
     *
     * @param string $type
     * @param int $id
     * @return mixed|null
     */
    protected function getPurchasableItem(string $type, int $id)
    {
        // Get the morph map and reverse it to find the alias for the given type
        $morphMap = Relation::morphMap();
        $reverseMorphMap = array_flip($morphMap);

        if(isset($morphMap[$type])){
            $eloquentModelClass = $morphMap[$type];
            return $eloquentModelClass::find($id);
        }

        // If the type exists in our morph map, use the alias to resolve the model
        if (isset($reverseMorphMap[$type])) {
            $alias = $reverseMorphMap[$type];
            return $morphMap[$alias]::find($id);
        }

        // Fallback: try to use the class directly if it's a valid model
        if (class_exists($type) && is_subclass_of($type, 'Illuminate\Database\Eloquent\Model')) {
            return $type::find($id);
        }

        return null;
    }

    /**
     * Validate that the purchasable type is allowed
     *
     * @param string $purchasableType
     * @throws \InvalidArgumentException If type is not allowed
     */
    protected function validatePurchasableType(string $purchasableType): void
    {
        if (!in_array($purchasableType, config('common.purchasable_types'))) {
            throw new \InvalidArgumentException("Invalid purchasable type: {$purchasableType}");
        }
    }

    /**
     * Get the price of a purchasable item
     *
     * @param mixed $item
     * @return float
     */
    protected function getItemPrice($item): float
    {
        // Assuming all purchasable items have a 'price' or 'points_required' field
        return $item->points_required ?? $item->price ?? 0;
    }
}
