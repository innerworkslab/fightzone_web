<?php

namespace App\Repositories\Course;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

use App\Models\Course;
use App\Models\Purchase;
use App\Models\LessonDay;

class CourseRepository implements CourseRepositoryInterface
{
    public function all(bool $onlyActive = true, ?array $filters = [], ?int $limit = null)
    {
        $query = Course::query()
            ->with('category')
            ->orderBy('name');

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
            if ($key === 'category_name') {
                $query->whereHas('category', function ($q) use ($value) {
                    $q->where('name', 'like', "%{$value}%");
                });
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
        return Course::find($id);
    }

    public function findWithDetails($id)
    {
        return Course::with(['category', 'courseLevels'])->find($id);
    }

    public function findWithDetailsForUser($id, $userId)
    {
        // Get the course with its levels
        $course = Course::with([
            'category',
            'courseLevels' => function ($q) {
                $q->withCount('lessonDays');
            }
        ])
        ->find($id);

        if (!$course) {
            return null;
        }

        // Get course level IDs for this course
        $courseLevelIds = $course->courseLevels->pluck('id')->toArray();

        if (empty($courseLevelIds)) {
            // No levels found, return course with empty levels
            return $course;
        }

        // Get purchases for this user and these course levels
        $purchases = Purchase::where('user_id', $userId)
            ->where('purchasable_type', 'App\\Models\\CourseLevel')
            ->orWhere('purchasable_type', 'course_level')
            ->whereIn('purchasable_id', $courseLevelIds)
            ->get(['purchasable_id', 'status'])
            ->keyBy('purchasable_id');

        // Add purchase status to each course level
        $course->courseLevels->transform(function ($level) use ($purchases) {
            $purchase = $purchases->get($level->id);

            if (!$purchase) {
                // Level not purchased
                $level->purchase_status = 'not_purchased';
            }else{
                $level->purchase_status = $purchase->status;
            }

            return $level;
        });

        return $course;
    }

    public function create(array $data): Course
    {
        return Course::create($data);
    }

    public function update($id, array $data): Course
    {
        $course = $this->find($id);
        if (!$course) {
            throw new \RuntimeException('Course not found');
        }

        $course->fill($data);
        $course->save();
        return $course;
    }

    public function delete($id)
    {
        $course = $this->find($id);
        if (!$course) {
            throw new \RuntimeException('Course not found');
        }

        return $course->delete();
    }

    public function toggleActive($id): Course
    {
        $course = $this->find($id);
        if (!$course) {
            throw new \RuntimeException('Course not found');
        }

        $course->is_active = !$course->is_active;
        $course->save();
        return $course;
    }

    public function getByCategory($categoryId, bool $onlyActive = true)
    {
        $query = Course::where('course_category_id', $categoryId)
            ->with('category')
            ->orderBy('name');

        if ($onlyActive) {
            $query->where('is_active', true);
        }

        return $query;
    }

    public function getByLevel($level, bool $onlyActive = true)
    {
        $query = Course::where('level', $level)
            ->with('category')
            ->orderBy('name');

        if ($onlyActive) {
            $query->where('is_active', true);
        }

        return $query;
    }
}
