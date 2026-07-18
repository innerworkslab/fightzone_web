<?php

namespace App\Services\ThirdParty\Video\Contracts;

use App\Services\ThirdParty\Video\VideoMeta;

interface VideoProviderInterface
{
    public function supports(string $url): bool;

    public function extractMeta(string $url): VideoMeta;
}
