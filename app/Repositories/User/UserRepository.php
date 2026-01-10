<?php

namespace App\Repositories\User;

use App\Models\User;

class UserRepository implements UserRepositoryInterface
{
    protected User $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function all(?array $filters = [], ?int $limit = null)
    {
        $query = $this->model::orderBy('id', 'desc');

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
            $query->where($key,'like', "%{$value}%");
        }

        if ($limit) {
            $query->limit($limit);
        }

        return $query;
    }

    public function find($id)
    {
        return $this->model->find($id);
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
