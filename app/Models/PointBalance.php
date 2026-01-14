<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PointBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'points',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Atomically adjust points for a user and record a point transaction.
     * Points can be positive (add) or negative (deduct).
     * For deductions, validates sufficient balance.
     *
     * @param int $userId
     * @param int $points Positive for adding, negative for deducting
     * @param mixed $referenceable Model instance, [type,id] array, or int ID
     * @param string $type Transaction type (deposit, purchase, etc.)
     * @param string|null $note
     * @return self
     * @throws \RuntimeException If insufficient balance for deduction
     */
    public static function adjustPointsForUser(int $userId, int $points, $referenceable = null, ?int $referenceableId=null, string $type = 'adjustment', ?string $note = null): self
    {
        if(gettype($referenceable) === 'string' && !$referenceableId){
            throw new \RuntimeException('Referenceable type id must be present when specifying morph-mapped referenceable type');
        }
        return DB::transaction(function () use ($userId, $points, $referenceable, $referenceableId, $type, $note) {
            $balance = self::where('user_id', $userId)->lockForUpdate()->first();

            if (! $balance) {
                $balance = new self(['user_id' => $userId, 'points' => 0]);
            }

            $before = (int) $balance->points;
            $newBalance = $before + $points;

            // Validate sufficient balance for deductions
            if ($points < 0 && $newBalance < 0) {
                throw new \RuntimeException("User doesn't have sufficient point balance for this transaction");
            }

            $balance->points = $newBalance;
            $balance->save();

            $txData = [
                'user_id' => $userId,
                'change_points' => $points,
                'type' => $type,
                'before_points' => $before,
                'after_points' => $balance->points,
                'note' => $note,
            ];

            if ($referenceable) {
                // Accept either a model instance or an array [type,id] or an int (id)
                if (is_object($referenceable) && method_exists($referenceable, 'getKey')) {
                    $txData['referenceable_type'] = get_class($referenceable);
                    $txData['referenceable_id'] = $referenceable->getKey();
                } elseif (is_array($referenceable) && count($referenceable) === 2) {
                    [$refType, $refId] = $referenceable;
                    $txData['referenceable_type'] = $refType;
                    $txData['referenceable_id'] = $refId;
                }elseif (gettype($referenceable) === 'string') {
                    $txData['referenceable_type'] = $referenceable;
                    $txData['referenceable_id'] = $referenceableId;
                } elseif (is_int($referenceable)) {
                    // Leave referenceable_type null; caller should provide type when needed.
                    $txData['referenceable_id'] = $referenceable;
                }
            }

            PointTransaction::create($txData);

            return $balance;
        });
    }

    /**
     * Legacy method for backward compatibility.
     * @deprecated Use adjustPointsForUser instead
     */
    public static function addPointsForUser(int $userId, int $points, $referenceable = null, string $type = 'deposit', ?string $note = null): self
    {
        return self::adjustPointsForUser($userId, $points, $referenceable, $type, $note);
    }
}
