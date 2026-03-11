<?php

namespace App\Repositories\FeaturedImage;

use App\Models\FeaturedImage;

class FeaturedImageRepository implements FeaturedImageRepositoryInterface
{
    public function __construct(protected FeaturedImage $model)
    {

    }

    public function all(?int $limit = null)
    {
        $query = $this->model::orderBy('id', 'desc');

        if ($limit) {
            $query->limit($limit);
        }

        return $query;
    }

    public function find($id)
    {
        return $this->model::find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $item = $this->find($id);
        if (! $item) return null;
        $item->fill($data);
        $item->save();
        return $item;
    }

    public function delete($id)
    {
        $item = $this->find($id);
        if (! $item) return false;
        return $item->delete();
    }
}
