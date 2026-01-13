<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointTransaction extends Model
{
    use HasFactory;

    /**
     * Note: this model supports a polymorphic `referenceable` relation.
     * Ensure the DB has `referenceable_type` and `referenceable_id` columns.
     */
    protected $fillable = [
        'user_id',
        'change_points',
        'type',
        'referenceable_type',
        'referenceable_id',
        'before_points',
        'after_points',
        'note',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Polymorphic relation to the model that caused this transaction
     * (e.g. Deposit, Purchase, Adjustment).
     */
    public function referenceable()
    {
        return $this->morphTo();
    }
}
