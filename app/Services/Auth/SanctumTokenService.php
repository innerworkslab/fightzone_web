<?php

namespace App\Services\Auth;

class SanctumTokenService
{
    /**
     * Generate a sanctum token for any model that uses HasApiTokens (e.g., User, Admin).
     *
     * @param  mixed  $model
     * @param  array|string[]  $abilities
     * @param  string|null  $name
     * @param  bool  $revokeExistingTokens
     * @return string
     */
    public function generateSanctumToken($model, array $abilities = ['*'], ?string $name = null, bool $revokeExistingTokens = false)
    {
        if ($revokeExistingTokens) {
            $model->tokens()->delete();
        }

        $baseName = $name ?? ('tokens.' . strtolower(class_basename($model)));
        return $model->createToken($baseName, $abilities, now()->addDays(365))->plainTextToken;
    }
}
