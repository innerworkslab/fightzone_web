<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use Carbon\Carbon;

use App\Models\PointBalance;
use App\Models\Admin;
use App\Models\User;
use App\Models\Purchase;
use App\Models\CourseLevel;
use App\Models\CourseLevelPurchase;

use App\Repositories\Purchase\PurchaseRepositoryInterface;

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

        // Calculate total points needed (same logic as deposit conversion)
        $unitPrice = $this->getItemPrice($purchasable);
        $rate = (float) config('payments.points_per_unit', 1);
        $minor = (int) config('payments.currency_minor_unit', 100);
        $pointsPerUnit = (int) floor(($unitPrice / $minor) * $rate);
        $totalPoints = $pointsPerUnit * $quantity;

        return $this->repo->create([
            'user_id' => $userId,
            'purchasable_type' => $purchasableType,
            'purchasable_id' => $purchasableId,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_points' => $totalPoints,
        ]);
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

            // If the purchasable is a CourseLevel, record course_level_purchases for the user.
            if ($purchasable instanceof CourseLevel) {
                $confirmedAt = $purchase->confirmed_at ?? Carbon::now();

                // Validity period: confirmed_at + number of LESSON days (type='Lesson')
                $lessonDaysCount = (int) $purchasable->lessonDays()
                    ->where('type', 'Lesson')
                    ->count();

                $validFrom = $confirmedAt;
                $validUntil = (clone $confirmedAt)->addDays($lessonDaysCount);

                $courseLevelPurchase = CourseLevelPurchase::firstOrCreate(
                    ['purchase_id' => $purchase->id],
                    [
                        'user_id' => $purchase->user_id,
                        'course_level_id' => $purchasable->id,
                        'valid_from' => $validFrom,
                        'valid_until' => $validUntil,
                        'finished_lesson_days_count' => 0,
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
