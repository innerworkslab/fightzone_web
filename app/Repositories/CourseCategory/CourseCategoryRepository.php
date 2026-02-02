<?php

namespace App\Repositories\CourseCategory;

use App\Models\CourseCategory;

class CourseCategoryRepository implements CourseCategoryRepositoryInterface
{
    public function all(bool $onlyActive = true, ?array $filters = [], ?int $limit = null)
    {
        $query = CourseCategory::query()->orderBy('name');

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
        return CourseCategory::find($id);
    }

    public function create(array $data): CourseCategory
    {
        return CourseCategory::create($data);
    }

    public function update($id, array $data): CourseCategory
    {
        $category = $this->find($id);
        if (!$category) {
            throw new \RuntimeException('Course category not found');
        }

        $category->fill($data);
        $category->save();
        return $category;
    }

    public function delete($id)
    {
        $category = $this->find($id);
        if (!$category) {
            throw new \RuntimeException('Course category not found');
        }

        return $category->delete();
    }

    public function toggleActive($id): CourseCategory
    {
        $category = $this->find($id);
        if (!$category) {
            throw new \RuntimeException('Course category not found');
        }

        $category->is_active = !$category->is_active;
        $category->save();
        return $category;
    }
}