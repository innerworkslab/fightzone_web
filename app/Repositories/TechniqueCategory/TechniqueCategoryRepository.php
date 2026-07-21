<?php

namespace App\Repositories\TechniqueCategory;

use App\Models\TechniqueCategory;

class TechniqueCategoryRepository implements TechniqueCategoryRepositoryInterface
{
    public function all(bool $onlyActive = true, ?array $filters = [], ?int $limit = null)
    {
        $query = TechniqueCategory::query()
            ->withCount('techniques')
            ->orderBy('name');

        if ($onlyActive) {
            $query->where('is_active', true);
        }

        foreach ($this->normalizeFilters($filters) as $key => $value) {
            if ($key === 'is_active') {
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
        return TechniqueCategory::withCount('techniques')->find($id);
    }

    public function create(array $data): TechniqueCategory
    {
        return TechniqueCategory::create($data);
    }

    public function update($id, array $data): TechniqueCategory
    {
        $category = $this->find($id);
        if (!$category) {
            throw new \RuntimeException('Technique category not found');
        }

        $category->fill($data);
        $category->save();

        return $category;
    }

    public function delete($id)
    {
        $category = $this->find($id);
        if (!$category) {
            throw new \RuntimeException('Technique category not found');
        }

        return $category->delete();
    }

    public function toggleActive($id): TechniqueCategory
    {
        $category = $this->find($id);
        if (!$category) {
            throw new \RuntimeException('Technique category not found');
        }

        $category->is_active = !$category->is_active;
        $category->save();

        return $category;
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
