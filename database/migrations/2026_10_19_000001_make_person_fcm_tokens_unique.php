<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $seenTokens = [];
        $duplicateIds = [];

        foreach (
            DB::table('person_fcm_tokens')
                ->select(['id', 'fcm_token'])
                ->orderByDesc('updated_at')
                ->orderByDesc('id')
                ->cursor() as $tokenRow
        ) {
            if (isset($seenTokens[$tokenRow->fcm_token])) {
                $duplicateIds[] = $tokenRow->id;
                continue;
            }

            $seenTokens[$tokenRow->fcm_token] = true;
        }

        foreach (array_chunk($duplicateIds, 500) as $ids) {
            DB::table('person_fcm_tokens')->whereIn('id', $ids)->delete();
        }

        Schema::table('person_fcm_tokens', function (Blueprint $table) {
            $table->unique('fcm_token', 'person_fcm_tokens_fcm_token_unique');
        });
    }

    public function down(): void
    {
        Schema::table('person_fcm_tokens', function (Blueprint $table) {
            $table->dropUnique('person_fcm_tokens_fcm_token_unique');
        });
    }
};
