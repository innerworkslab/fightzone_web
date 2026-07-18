<?php

namespace App\Services\ThirdParty\Video\Providers;

use DateInterval;
use InvalidArgumentException;

use Illuminate\Support\Facades\Http;

use App\Services\ThirdParty\Video\Concerns\NormalizesVideoText;
use App\Services\ThirdParty\Video\Contracts\VideoProviderInterface;
use App\Services\ThirdParty\Video\VideoMeta;

class YoutubeVideoProvider implements VideoProviderInterface
{
    use NormalizesVideoText;

    public function supports(string $url): bool
    {
        return in_array($this->normalizeHost($url), [
            'youtube.com',
            'www.youtube.com',
            'm.youtube.com',
            'youtu.be',
            'www.youtu.be',
        ], true);
    }

    public function extractMeta(string $url): VideoMeta
    {
        $videoId = $this->extractVideoId($url);

        if (!$videoId) {
            throw new InvalidArgumentException('Invalid YouTube URL');
        }

        $response = Http::get(
            'https://www.googleapis.com/youtube/v3/videos',
            [
                'part' => 'snippet,contentDetails',
                'id' => $videoId,
                'key' => config('services.google.youtube.api_key'),
            ]
        )->json();

        if (empty($response['items'][0])) {
            throw new InvalidArgumentException('YouTube video not found');
        }

        $video = $response['items'][0];

        return new VideoMeta(
            provider: 'youtube',
            videoId: $videoId,
            name: $video['snippet']['title'],
            description: $this->trimText($video['snippet']['description'], 255),
            duration: $this->iso8601ToSeconds($video['contentDetails']['duration']),
            thumbnail: $video['snippet']['thumbnails']['high']['url']
                ?? $video['snippet']['thumbnails']['default']['url']
                ?? null,
        );
    }

    private function extractVideoId(string $url): ?string
    {
        $path = parse_url($url, PHP_URL_PATH);
        $host = $this->normalizeHost($url);

        if (in_array($host, ['youtu.be', 'www.youtu.be'], true)) {
            $videoId = trim((string) $path, '/');

            return $this->isValidVideoId($videoId) ? $videoId : null;
        }

        parse_str((string) parse_url($url, PHP_URL_QUERY), $query);

        if (
            in_array($host, ['youtube.com', 'www.youtube.com', 'm.youtube.com'], true)
            && isset($query['v'])
            && $this->isValidVideoId($query['v'])
        ) {
            return $query['v'];
        }

        if (
            in_array($host, ['youtube.com', 'www.youtube.com', 'm.youtube.com'], true)
            && is_string($path)
            && preg_match('#^/(embed|shorts)/([A-Za-z0-9_-]{11})$#', $path, $matches)
        ) {
            return $matches[2];
        }

        return null;
    }

    private function isValidVideoId(string $videoId): bool
    {
        return preg_match('/^[A-Za-z0-9_-]{11}$/', $videoId) === 1;
    }

    private function iso8601ToSeconds(string $duration): int
    {
        $interval = new DateInterval($duration);

        return ($interval->h * 3600)
            + ($interval->i * 60)
            + $interval->s;
    }
}
