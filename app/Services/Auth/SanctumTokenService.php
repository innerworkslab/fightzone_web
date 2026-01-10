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
     * @return string
     */
    public function generateSanctumToken($model, array $abilities = ['*'], ?string $name = null)
    {
        $baseName = $name ?? ('tokens.' . strtolower(class_basename($model)));
        return $model->createToken($baseName, $abilities, now()->addDays(365))->plainTextToken;
    }
}
