<?php

namespace App\Services;

use App\Repositories\Technique\TechniqueRepositoryInterface;
use App\Services\ThirdParty\Video\VideoMetadataService;

class TechniqueService
{
    public function __construct(
        protected TechniqueRepositoryInterface $repo,
        protected VideoMetadataService $videoMetadataService
    ) {}

    public function all(bool $onlyActive = true, ?array $filters = [], ?int $page = null, ?int $limit = null)
    {
        $query = $this->repo->all($onlyActive, $filters, $limit);

        return $page
            ? $query->paginate($limit ?? config('common.list_count'))
            : $query->get();
    }

    public function find($id)
    {
        return $this->repo->find($id);
    }

    public function create(array $data)
    {
        $data = $this->withVideoMeta($data);

        return $this->repo->create($data);
    }

    public function update($id, array $data)
    {
        $technique = $this->repo->find($id);
        if (!$technique) {
            throw new \RuntimeException('Technique not found');
        }

        if (isset($data['url']) && $data['url'] !== $technique->url) {
            $data = $this->withVideoMeta($data);
        }

        return $this->repo->update($id, $data);
    }

    public function delete($id)
    {
        return $this->repo->delete($id);
    }

    public function toggleActive($id)
    {
        return $this->repo->toggleActive($id);
    }

    private function withVideoMeta(array $data): array
    {
        $videoMeta = $this->videoMetadataService->extractMeta($data['url']);

        $data['provider'] = $videoMeta['provider'];
        $data['provider_video_id'] = $videoMeta['video_id'];
        $data['thumbnail_url'] = $videoMeta['thumbnail'];
        $data['duration'] = $data['duration'] ?? $this->secondsToTime($videoMeta['duration']);
        $data['name'] = $data['name'] ?? '';
        $data['description'] = $data['description'] ?? '';

        if (trim($data['name']) === '') {
            $data['name'] = $videoMeta['name'];
        }

        if (trim($data['description']) === '') {
            $data['description'] = $videoMeta['description'];
        }

        return $data;
    }

    private function secondsToTime(int $seconds): string
    {
        return gmdate('H:i:s', $seconds);
    }
}
