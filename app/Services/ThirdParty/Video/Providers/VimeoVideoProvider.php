<?php

namespace App\Services\ThirdParty\Video\Providers;

use InvalidArgumentException;

use Illuminate\Support\Facades\Http;

use App\Services\ThirdParty\Video\Concerns\NormalizesVideoText;
use App\Services\ThirdParty\Video\Contracts\VideoProviderInterface;
use App\Services\ThirdParty\Video\VideoMeta;

class VimeoVideoProvider implements VideoProviderInterface
{
    use NormalizesVideoText;

    public function supports(string $url): bool
    {
        $host = $this->normalizeHost($url);

        return $host === 'vimeo.com'
            || str_ends_with($host, '.vimeo.com');
    }

    public function extractMeta(string $url): VideoMeta
    {
        $response = Http::get(
            'https://vimeo.com/api/oembed.json',
            [
                'url' => $url,
            ]
        );

        if (!$response->successful()) {
            throw new InvalidArgumentException('Vimeo video not found');
        }

        $video = $response->json();

        return new VideoMeta(
            provider: 'vimeo',
            videoId: (string) ($video['video_id'] ?? ''),
            name: $video['title'] ?? 'Vimeo video',
            description: $this->trimText(strip_tags($video['description'] ?? ''), 255),
            duration: (int) ($video['duration'] ?? 0),
            thumbnail: $video['thumbnail_url'] ?? null,
        );
    }
}
