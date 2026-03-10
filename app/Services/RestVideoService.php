<?php

namespace App\Services;

use App\Repositories\RestVideo\RestVideoRepositoryInterface;

use App\Services\ThirdParty\YoutubeService;

class RestVideoService
{
    public function __construct(protected RestVideoRepositoryInterface $repo, protected YoutubeService $youtubeService)
    {

    }

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
        $videoMeta = $this->youtubeService->extractMeta($data['url']);
        $data['thumbnail_url'] = $videoMeta['thumbnail'];
        $data['duration'] = (isset($data['duration']))? $data['duration']: $this->secondsToTime($videoMeta['duration']);

        $data['name'] = (isset($data['name']))? $data['name']: $videoMeta['name'];
        $data['description'] = (isset($data['description']))? $data['description']: $videoMeta['description'];
        return $this->repo->create($data);
    }

    public function update(int $id, array $data)
    {
        $restVideo = $this->repo->find($id);
        if($restVideo->url == $data['url']){
            return $this->repo->update($id, $data);
        }else{
            $videoMeta = $this->youtubeService->extractMeta($data['url']);
            $data['thumbnail_url'] = $videoMeta['thumbnail'];
            $data['duration'] = (isset($data['duration']))? $data['duration']: $this->secondsToTime($videoMeta['duration']);

            $data['name'] = (isset($data['name']))? $data['name']: $videoMeta['name'];
            $data['description'] = (isset($data['description']))? $data['description']: $videoMeta['description'];
            return $this->repo->update($id, $data);
        }
        $videoMeta = $this->youtubeService->extractMeta($data['url']);
        $data['thumbnail_url'] = $videoMeta['thumbnail'];
        $data['duration'] = (isset($data['duration']))? $data['duration']: $this->secondsToTime($videoMeta['duration']);

        $data['name'] = (isset($data['name']))? $data['name']: $videoMeta['name'];
        $data['description'] = (isset($data['description']))? $data['description']: $videoMeta['description'];
    }

    public function delete($id)
    {
        return $this->repo->delete($id);
    }

    public function toggleActive($id)
    {
        return $this->repo->toggleActive($id);
    }

    private function secondsToTime(int $seconds): string
    {
        return gmdate('H:i:s', $seconds);
    }
}
