<?php

namespace App\Services\ThirdParty\Video;

use InvalidArgumentException;

class VideoMetadataService
{
    /**
     * @param array<int, \App\Services\ThirdParty\Video\Contracts\VideoProviderInterface> $providers
     */
    public function __construct(private readonly array $providers) {}

    public function extractMeta(string $url): array
    {
        foreach ($this->providers as $provider) {
            if ($provider->supports($url)) {
                return $provider->extractMeta($url)->toArray();
            }
        }

        throw new InvalidArgumentException('Invalid video URL. Please use a YouTube or Vimeo URL.');
    }
}
