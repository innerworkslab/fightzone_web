<?php

namespace App\Services\ThirdParty;

use DateInterval;
use InvalidArgumentException;

use Illuminate\Support\Facades\Http;

class YoutubeService
{
    public function extractMeta(string $youtubeUrl): array
    {
        $videoId = $this->extractVideoId($youtubeUrl);

        if (!$videoId) {
            throw new InvalidArgumentException('Invalid YouTube URL');
        }

        $response = Http::get(
            'https://www.googleapis.com/youtube/v3/videos',
            [
                'part' => 'snippet,contentDetails',
                'id'   => $videoId,
                'key'  => config('services.google.youtube.api_key'),
            ]
        )->json();

        if (empty($response['items'][0])) {
            throw new InvalidArgumentException('YouTube video not found');
        }

        $video = $response['items'][0];

        return [
            'video_id'     => $videoId,
            'name'         => $video['snippet']['title'],
            'description'  => $this->trim($video['snippet']['description'], 255),
            'duration'     => $this->iso8601ToSeconds(
                $video['contentDetails']['duration']
            ),
            'thumbnail'    => $video['snippet']['thumbnails']['high']['url']
                ?? $video['snippet']['thumbnails']['default']['url']
                ?? null,
        ];
    }

    private function extractVideoId(string $url): ?string
    {
        preg_match(
            '/(?:youtube\.com\/watch\?v=|youtu\.be\/)([A-Za-z0-9_-]{11})/',
            $url,
            $matches
        );

        return $matches[1] ?? null;
    }

    private function iso8601ToSeconds(string $duration): int
    {
        $interval = new DateInterval($duration);

        return ($interval->h * 3600)
            + ($interval->i * 60)
            + $interval->s;
    }

    private function trim(string $value, int $limit): string
    {
        return mb_strlen($value) > $limit
            ? mb_substr($value, 0, $limit)
            : $value;
    }
}
