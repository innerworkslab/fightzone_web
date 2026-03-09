<?php

namespace App\Repositories\RestVideo;

use App\Models\RestVideo;

class RestVideoRepository implements RestVideoRepositoryInterface
{
    public function all(bool $onlyActive = true, ?array $filters = [], ?int $limit = null)
    {
        $query = RestVideo::query()->orderBy('name');

        if ($onlyActive) {
            $query->where('is_active', true);
        }

        $normalized = [];
        foreach ($filters ?? [] as $k => $v) {
            if (is_int($k) && is_array($v)) {
                foreach ($v as $fk => $fv) {
                    $normalized[$fk] = $fv;
                }
            } elseif (is_string($k)) {
                $normalized[$k] = $v;
            }
        }

        foreach ($normalized as $key => $value) {
            $query->where($key, 'like', "%{$value}%");
        }

        if ($limit) {
            $query->limit($limit);
        }

        return $query;
    }

    public function find($id)
    {
        return RestVideo::find($id);
    }

    public function create(array $data): RestVideo
    {
        return RestVideo::create($data);
    }

    public function update($id, array $data): RestVideo
    {
        $restVideo = $this->find($id);
        if (!$restVideo) {
            throw new \RuntimeException('Course category not found');
        }

        $restVideo->fill($data);
        $restVideo->save();
        return $restVideo;
    }

    public function delete($id)
    {
        $restVideo = $this->find($id);
        if (!$restVideo) {
            throw new \RuntimeException('Course category not found');
        }

        return $restVideo->delete();
    }

    public function toggleActive($id): RestVideo
    {
        $restVideo = $this->find($id);
        if (!$restVideo) {
            throw new \RuntimeException('Course category not found');
        }

        $restVideo->is_active = !$restVideo->is_active;
        $restVideo->save();
        return $restVideo;
    }
}
