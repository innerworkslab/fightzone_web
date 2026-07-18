<?php

namespace App\Services\ThirdParty\Video;

class VideoMeta
{
    public function __construct(
        public readonly string $provider,
        public readonly string $videoId,
        public readonly string $name,
        public readonly string $description,
        public readonly int $duration,
        public readonly ?string $thumbnail,
    ) {}

    public function toArray(): array
    {
        return [
            'provider' => $this->provider,
            'video_id' => $this->videoId,
            'name' => $this->name,
            'description' => $this->description,
            'duration' => $this->duration,
            'thumbnail' => $this->thumbnail,
        ];
    }
}
