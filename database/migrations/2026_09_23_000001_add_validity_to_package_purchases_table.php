<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('package_purchases', function (Blueprint $table) {
            $table->timestamp('valid_from')->nullable()->after('remaining_days');
            $table->timestamp('valid_until')->nullable()->after('valid_from');
            $table->index(['user_id', 'valid_until']);
        });

        DB::table('package_purchases')
            ->orderBy('id')
            ->select(['id', 'created_at', 'total_days'])
            ->chunkById(100, function ($packagePurchases): void {
                foreach ($packagePurchases as $packagePurchase) {
                    if (! $packagePurchase->created_at) {
                        continue;
                    }

                    $validFrom = \Carbon\Carbon::parse($packagePurchase->created_at);
                    $validUntil = (clone $validFrom)->addDays((int) $packagePurchase->total_days);

                    DB::table('package_purchases')
                        ->where('id', $packagePurchase->id)
                        ->update([
                            'valid_from' => $validFrom,
                            'valid_until' => $validUntil,
                        ]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('package_purchases', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'valid_until']);
            $table->dropColumn(['valid_from', 'valid_until']);
        });
    }
};