<?php

namespace App\Repositories\Technique;

use App\Models\Technique;

class TechniqueRepository implements TechniqueRepositoryInterface
{
    public function all(bool $onlyActive = true, ?array $filters = [], ?int $limit = null)
    {
        $query = Technique::query()
            ->with('category')
            ->orderBy('name');

        if ($onlyActive) {
            $query->where('is_active', true);
        }

        foreach ($this->normalizeFilters($filters) as $key => $value) {
            if ($key === 'technique_category_id') {
                $query->where($key, $value);
            } elseif ($key === 'is_active') {
                $query->where($key, (bool) $value);
            } else {
                $query->where($key, 'like', "%{$value}%");
            }
        }

        if ($limit) {
            $query->limit($limit);
        }

        return $query;
    }

    public function find($id)
    {
        return Technique::with('category')->find($id);
    }

    public function create(array $data): Technique
    {
        return Technique::create($data);
    }

    public function update($id, array $data): Technique
    {
        $technique = $this->find($id);
        if (!$technique) {
            throw new \RuntimeException('Technique not found');
        }

        $technique->fill($data);
        $technique->save();

        return $technique;
    }

    public function delete($id)
    {
        $technique = $this->find($id);
        if (!$technique) {
            throw new \RuntimeException('Technique not found');
        }

        return $technique->delete();
    }

    public function toggleActive($id): Technique
    {
        $technique = $this->find($id);
        if (!$technique) {
            throw new \RuntimeException('Technique not found');
        }

        $technique->is_active = !$technique->is_active;
        $technique->save();

        return $technique;
    }

    private function normalizeFilters(?array $filters = []): array
    {
        $normalized = [];
        foreach ($filters ?? [] as $key => $value) {
            if (is_int($key) && is_array($value)) {
                foreach ($value as $filterKey => $filterValue) {
                    $normalized[$filterKey] = $filterValue;
                }
            } elseif (is_string($key)) {
                $normalized[$key] = $value;
            }
        }

        return $normalized;
    }
}
