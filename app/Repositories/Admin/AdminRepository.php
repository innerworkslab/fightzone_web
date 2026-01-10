<?php

namespace App\Repositories\Admin;

use App\Models\Admin;

class AdminRepository implements AdminRepositoryInterface
{
    protected Admin $model;

    public function __construct(Admin $model)
    {
        $this->model = $model;
    }

    public function all(?array $filters=[],?int $limit = null)
    {
        $query = $this->model::with(['permissionType.group','permissions'])
        ->orderBy('id', 'desc');

        $arrayKeys = array_keys($filters);
        $index = 0;
        foreach($filters as $filter){
            if($arrayKeys[$index] == 'gem_id')
                continue;
            $query->where($arrayKeys[$index], $filter);
            $index++;
        }

        if ($limit) {
            $query->limit($limit);
        }

        return $query;
    }

    public function find($id)
    {
        return $this->model::with(['permissionType.group','permissions'])->find($id);
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

    public function toggleActive($id)
    {
        $item = $this->find($id);
        if (! $item) return null;
        $item->is_active = ! (bool) $item->is_active;
        $item->save();
        return $item;
    }
}
