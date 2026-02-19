<?php

namespace App\Repositories\Course;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

use App\Models\Course;
use App\Models\Purchase;
use App\Models\CourseLevelPurchase;
use App\Models\LessonDayCompletion;

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
        // Get the course with its levels and lesson day counts
        $course = Course::with([
            'category',
            'courseLevels' => function ($q) {
                $q->withCount('lessonDays');
            }
        ])->find($id);

        if (! $course) {
            return null;
        }

        $courseLevelIds = $course->courseLevels->pluck('id')->all();

        if (empty($courseLevelIds)) {
            return $course;
        }

        // Get latest purchases per course level for this user (pending/confirmed/rejected)
        $purchaseRows = Purchase::where('user_id', $userId)
            ->whereIn('purchasable_id', $courseLevelIds)
            ->whereIn('purchasable_type', ['App\\Models\\CourseLevel', 'course_level'])
            ->orderByDesc('id')
            ->get(['purchasable_id', 'status', 'id']);

        $purchasesByLevel = $purchaseRows
            ->groupBy('purchasable_id')
            ->map(function ($group) {
                // latest by id
                return $group->first();
            });

        // Get course_level_purchases for validity & finished days
        $clpCollection = CourseLevelPurchase::where('user_id', $userId)
            ->whereIn('course_level_id', $courseLevelIds)
            ->get();

        $clpByLevel = $clpCollection->keyBy('course_level_id');

        // Aggregate completed lesson-day tasks per course_level_purchase
        $clpIds = $clpCollection->pluck('id')->all();
        $completedCountsByClp = [];
        if (! empty($clpIds)) {
            $completedCountsByClp = LessonDayCompletion::selectRaw('course_level_purchase_id, COUNT(*) as completed_tasks')
                ->whereIn('course_level_purchase_id', $clpIds)
                ->groupBy('course_level_purchase_id')
                ->get()
                ->keyBy('course_level_purchase_id');
        }

        $now = now();

        $course->courseLevels->transform(function ($level) use ($purchasesByLevel, $clpByLevel, $completedCountsByClp, $now) {
            $purchase = $purchasesByLevel->get($level->id);
            $clp = $clpByLevel->get($level->id);

            // Default values
            $level->purchase_status = $purchase->status ?? 'not_purchased';
            $level->valid_from = $clp?->valid_from;
            $level->valid_until = $clp?->valid_until;
            $level->is_within_validity = false;

            // Summary: tasks = lesson days for this level
            $totalTasks = (int) ($level->lesson_days_count ?? 0);
            $completedTasks = 0;
            $remainingTasks = $totalTasks;
            $completionPercent = 0.0;

            if ($clp) {
                $cc = $completedCountsByClp[$clp->id] ?? null;
                if ($cc) {
                    $completedTasks = (int) $cc->completed_tasks;
                    $remainingTasks = max($totalTasks - $completedTasks, 0);
                    $completionPercent = $totalTasks > 0
                        ? round(($completedTasks / $totalTasks) * 100, 2)
                        : 0.0;
                }
            }

            $level->total_tasks = $totalTasks;
            $level->completed_tasks = $completedTasks;
            $level->remaining_tasks = $remainingTasks;
            $level->completion_percent = $completionPercent;

            if ($clp && $clp->valid_from && $clp->valid_until) {
                $level->is_within_validity = $now->between($clp->valid_from, $clp->valid_until);
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
