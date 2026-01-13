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
     * Atomically add points for a user and record a point transaction.
     *
     * @param int $userId
     * @param int $points
     * @param int|null $referenceId
     * @param string $type
     * @param string|null $note
     * @return self
     */
    public static function addPointsForUser(int $userId, int $points, $referenceable = null, string $type = 'deposit', ?string $note = null): self
    {
        return DB::transaction(function () use ($userId, $points, $referenceable, $type, $note) {
            $balance = self::where('user_id', $userId)->lockForUpdate()->first();

            if (! $balance) {
                $balance = new self(['user_id' => $userId, 'points' => 0]);
            }

            $before = (int) $balance->points;
            $balance->points = $before + $points;
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
                } elseif (is_int($referenceable)) {
                    // Leave referenceable_type null; caller should provide type when needed.
                    $txData['referenceable_id'] = $referenceable;
                }
            }

            PointTransaction::create($txData);

            return $balance;
        });
    }
}
