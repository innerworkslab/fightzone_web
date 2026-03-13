<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

use App\Models\CourseLevel;
use App\Models\LessonDay;
use App\Models\LessonDayVideo;
use App\Models\CourseLevelPurchase;
use App\Models\LessonDayCompletion;
use App\Models\LessonDayVideoCompletion;

use App\Repositories\LessonDay\LessonDayRepositoryInterface;

use App\Services\LessonDayVideoService;

class LessonDayService
{
    public function __construct(
        protected LessonDayRepositoryInterface $repo,
        protected LessonDayVideoService $videoService
    ){}

    public function all($levelId)
    {
        $query = $this->repo->all(['course_level_id'=>$levelId], null);

        return $query->get();
    }

    public function find($lessonDayId)
    {
        return $this->repo->find($lessonDayId);
    }

    public function create($levelId, array $data, array $lessonDayVideos=[])
    {
        $courseLevel = CourseLevel::find($levelId);
        if (!$courseLevel) {
            throw new \RuntimeException('Course level not found');
        }

        if($courseLevel->lessonDays()->where('day_number', $data['day_number'])->exists()) {
            throw new \RuntimeException('Lesson day already exists');
        }

        try{
            DB::beginTransaction();
            $createdLessonDay = $this->repo->create($data);

            if(count($lessonDayVideos) > 0){
                $this->videoService->attachVideoToLesson($createdLessonDay->id, $lessonDayVideos);
            }

            DB::commit();

            return $createdLessonDay;
        }catch(\Exception $e){
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function update($id, array $data, array $lessonDayVideos=[])
    {
        try{
            DB::beginTransaction();
            $updatedLessonDay = $this->repo->update($id, $data);

            if(count($lessonDayVideos) > 0){
                $this->videoService->attachVideoToLesson($updatedLessonDay->id, $lessonDayVideos);
            }

            DB::commit();

            return $updatedLessonDay;
        }catch(\Exception $e){
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function delete($id)
    {
        return $this->repo->delete($id);
    }

    /**
     * List all lesson days in a level with completion info for a specific user.
     */
    public function allWithCompletionForUser(int $courseId, int $levelId, int $userId)
    {
        $lessonDays = LessonDay::with('videos')
            ->where('course_level_id', $levelId)
            ->orderBy('day_number')
            ->get();

        if ($lessonDays->isEmpty()) {
            return $lessonDays;
        }

        // Initialize flags
        foreach ($lessonDays as $day) {
            $day->is_completed = false;
            $day->completed_at = null;
            $day->is_within_validity = false;
            $day->accessible = false;

            foreach ($day->videos as $video) {
                $video->is_completed = false;
                $video->completed_at = null;
            }
        }

        // Find the latest course_level_purchase for this user & level
        $clp = $this->getCourseLevelPurchase($levelId, $userId);

        if (! $clp) {
            // User has not purchased this level; return with defaults
            return $lessonDays;
        }

        $now = now();
        $isWithinValidity = false;
        if ($clp->valid_from && $clp->valid_until) {
            $isWithinValidity = $now->between($clp->valid_from, $clp->valid_until);
        }

        // Determine how many lesson days should be accessible based on
        // how many days have passed since the subscription effectively started.
        $accessibleCount = 0;
        if ($clp->valid_from) {
            // Use calendar-day difference, but subtract one so that:
            // - On the first subscription day, only the first lesson day is accessible
            // - Each subsequent day unlocks exactly one more lesson day
            $daysSinceStart = $clp->valid_from->startOfDay()->diffInDays($now->startOfDay()) ; // - 1 is removed after diffInDays()
            if ($daysSinceStart < 0) {
                $daysSinceStart = 0;
            }

            // On the first subscription day, only the first lesson day is accessible.
            // Each subsequent day unlocks the next lesson day, until all are accessible.
            $accessibleCount = min($daysSinceStart + 1, $lessonDays->count());
        }

        $dayIds = $lessonDays->pluck('id')->all();

        // Fetch all day completions in one query
        $dayCompletions = LessonDayCompletion::where('course_level_purchase_id', $clp->id)
            ->whereIn('lesson_day_id', $dayIds)
            ->get()
            ->keyBy('lesson_day_id');

        // Fetch all video completions in one query
        $videoIds = $lessonDays->flatMap(function ($day) {
            return $day->videos->pluck('id');
        })->all();

        $videoCompletions = [];
        if (! empty($videoIds)) {
            $videoCompletions = LessonDayVideoCompletion::where('course_level_purchase_id', $clp->id)
                ->whereIn('lesson_day_video_id', $videoIds)
                ->get()
                ->keyBy('lesson_day_video_id');
        }

        foreach ($lessonDays as $index => $day) {
            $day->is_within_validity = $isWithinValidity;

            // Accessibility is driven by subscription age:
            // - Day 0: only first lesson day accessible
            // - Day 1: first two, etc.
            $day->accessible = ($index < $accessibleCount);

            $dc = $dayCompletions->get($day->id);
            if ($dc) {
                $day->is_completed = true;
                $day->completed_at = $dc->completed_at;
            }

            foreach ($day->videos as $video) {
                $vc = $videoCompletions[$video->id] ?? null;
                if ($vc) {
                    $video->is_completed = true;
                    $video->completed_at = $vc->completed_at;
                }
            }
        }

        return $lessonDays;
    }

    /**
     * Get a lesson day (with videos) including completion info for a specific user.
     */
    public function findWithCompletionForUser(int $courseId, int $levelId, int $lessonDayId, int $userId)
    {
        $lessonDay = LessonDay::with('videos')
            ->where('id', $lessonDayId)
            ->where('course_level_id', $levelId)
            ->first();

        if (! $lessonDay) {
            throw new \RuntimeException('Lesson day not found');
        }

        // Default completion flags
        $lessonDay->is_completed = false;
        $lessonDay->completed_at = null;
        $lessonDay->is_within_validity = false;
        $lessonDay->accessible = false;

        foreach ($lessonDay->videos as $video) {
            $video->is_completed = false;
            $video->completed_at = null;
        }

        // Find the latest course_level_purchase for this user & level
        $clp = $this->getCourseLevelPurchase($levelId, $userId);

        if (! $clp) {
            // User has not purchased this level; return with defaults
            return $lessonDay;
        }

        $now = now();
        if ($clp->valid_from && $clp->valid_until) {
            $lessonDay->is_within_validity = $now->between($clp->valid_from, $clp->valid_until);
        }

        // Determine accessibility for this specific lesson day using the
        // same rules as the lesson-day list endpoint.
        $allDays = LessonDay::where('course_level_id', $levelId)
            ->orderBy('day_number')
            ->get(['id']);

        $accessibleCount = 0;
        if ($clp->valid_from) {
            $daysSinceStart = $clp->valid_from->startOfDay()->diffInDays($now->startOfDay()) - 1;
            if ($daysSinceStart < 0) {
                $daysSinceStart = 0;
            }

            $accessibleCount = min($daysSinceStart + 1, $allDays->count());
        }

        $position = $allDays->search(function ($d) use ($lessonDay) {
            return $d->id === $lessonDay->id;
        });

        if ($position !== false) {
            $lessonDay->accessible = ($position < $accessibleCount);
        }

        // Lesson day completion
        $dayCompletion = LessonDayCompletion::where('course_level_purchase_id', $clp->id)
            ->where('lesson_day_id', $lessonDay->id)
            ->first();

        if ($dayCompletion) {
            $lessonDay->is_completed = true;
            $lessonDay->completed_at = $dayCompletion->completed_at;
        }

        // Videos completion in one query
        $videoIds = $lessonDay->videos->pluck('id')->all();
        if (! empty($videoIds)) {
            $videoCompletions = LessonDayVideoCompletion::where('course_level_purchase_id', $clp->id)
                ->whereIn('lesson_day_video_id', $videoIds)
                ->get()
                ->keyBy('lesson_day_video_id');

            foreach ($lessonDay->videos as $video) {
                $vc = $videoCompletions->get($video->id);
                if ($vc) {
                    $video->is_completed = true;
                    $video->completed_at = $vc->completed_at;
                }
            }
        }

        // Per-lesson summary: tasks are lesson videos for this day
        $totalTasks = $lessonDay->videos->count();
        $completedTasks = $lessonDay->videos->filter(fn ($v) => $v->is_completed)->count();
        $remainingTasks = max($totalTasks - $completedTasks, 0);
        $completionPercent = $totalTasks > 0
            ? round(($completedTasks / $totalTasks) * 100, 2)
            : 0.0;

        $lessonDay->total_tasks = $totalTasks;
        $lessonDay->completed_tasks = $completedTasks;
        $lessonDay->remaining_tasks = $remainingTasks;
        $lessonDay->completion_percent = $completionPercent;

        return $lessonDay;
    }

    /**
     * Mark a lesson video as completed for a user.
     * Automatically marks the lesson day as completed if all videos are completed.
     *
     * @param int $userId
     * @param int $lessonDayVideoId
     * @return array
     * @throws \RuntimeException
     */
    public function markVideoCompletion(int $userId, int $lessonDayVideoId)
    {
        // Get the lesson day video
        $video = LessonDayVideo::with('lessonDay.courseLevel')->find($lessonDayVideoId);

        if (!$video) {
            throw new \RuntimeException('Lesson video not found');
        }

        $lessonDay = $video->lessonDay;
        if (!$lessonDay) {
            throw new \RuntimeException('Lesson day not found');
        }

        $courseLevel = $lessonDay->courseLevel;
        if (!$courseLevel) {
            throw new \RuntimeException('Course level not found');
        }

        // Find the course level purchase for this user and course level
        $clp = $this->getCourseLevelPurchase($courseLevel->id, $userId);

        if (!$clp) {
            throw new \RuntimeException('Course level purchase not found. Please purchase this course level first.');
        }

        // Check if purchase is still valid
        $now = now();
        if ($clp->valid_from && $clp->valid_until) {
            if (!$now->between($clp->valid_from, $clp->valid_until)) {
                throw new \RuntimeException('Your course access has expired. Please renew your purchase.');
            }
        }

        try {
            DB::beginTransaction();

            // Create or update video completion
            $videoCompletion = LessonDayVideoCompletion::updateOrCreate(
                [
                    'course_level_purchase_id' => $clp->id,
                    'lesson_day_video_id' => $lessonDayVideoId,
                ],
                [
                    'completed_at' => $now,
                ]
            );

            // Check if all videos in this lesson day are completed
            $allVideos = LessonDayVideo::where('lesson_day_id', $lessonDay->id)->get();
            $completedVideos = LessonDayVideoCompletion::where('course_level_purchase_id', $clp->id)
                ->whereIn('lesson_day_video_id', $allVideos->pluck('id'))
                ->get();

            $allCompleted = $allVideos->count() > 0 && $allVideos->count() === $completedVideos->count();

            // If all videos are completed, mark the lesson day as completed
            if ($allCompleted) {
                LessonDayCompletion::updateOrCreate(
                    [
                        'course_level_purchase_id' => $clp->id,
                        'lesson_day_id' => $lessonDay->id,
                    ],
                    [
                        'completed_at' => $now,
                    ]
                );
            }

            DB::commit();

            return [
                'video_completion' => $videoCompletion,
                'lesson_day_completed' => $allCompleted,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('Failed to mark video completion: ' . $e->getMessage());
        }
    }

    /**
     * Get previous/next lesson-day video for the user, based on the currently playing
     * lesson day video id. Ordering is by lesson day (day_number asc) then video id asc.
     *
     * Returns null prev/next at boundaries.
     */
    public function getPrevNextVideoForUser(int $userId, int $lessonDayVideoId): array
    {
        $current = LessonDayVideo::with('lessonDay.courseLevel')->find($lessonDayVideoId);

        if (! $current) {
            throw new \RuntimeException('Lesson video not found');
        }

        $lessonDay = $current->lessonDay;
        if (! $lessonDay) {
            throw new \RuntimeException('Lesson day not found');
        }

        $courseLevel = $lessonDay->courseLevel;
        if (! $courseLevel) {
            throw new \RuntimeException('Course level not found');
        }

        $clp = $this->getCourseLevelPurchase((int) $courseLevel->id, (int) $userId);
        if (! $clp) {
            throw new \RuntimeException('Course level purchase not found. Please purchase this course level first.');
        }

        $now = now();
        if ($clp->valid_from && $clp->valid_until) {
            if (! $now->between($clp->valid_from, $clp->valid_until)) {
                throw new \RuntimeException('Your course access has expired. Please renew your purchase.');
            }
        }

        $currentDayNumber = (int) $lessonDay->day_number;

        // Previous: latest video before current in (day_number, video_id) ordering
        $prev = LessonDayVideo::query()
            ->select('lesson_day_videos.*')
            ->join('lesson_days', 'lesson_days.id', '=', 'lesson_day_videos.lesson_day_id')
            ->where('lesson_days.course_level_id', (int) $courseLevel->id)
            ->where(function ($q) use ($currentDayNumber, $lessonDayVideoId) {
                $q->where('lesson_days.day_number', '<', $currentDayNumber)
                    ->orWhere(function ($q2) use ($currentDayNumber, $lessonDayVideoId) {
                        $q2->where('lesson_days.day_number', '=', $currentDayNumber)
                            ->where('lesson_day_videos.id', '<', (int) $lessonDayVideoId);
                    });
            })
            ->orderByDesc('lesson_days.day_number')
            ->orderByDesc('lesson_day_videos.id')
            ->first();

        // Next: earliest video after current in (day_number, video_id) ordering
        $next = LessonDayVideo::query()
            ->select('lesson_day_videos.*')
            ->join('lesson_days', 'lesson_days.id', '=', 'lesson_day_videos.lesson_day_id')
            ->where('lesson_days.course_level_id', (int) $courseLevel->id)
            ->where(function ($q) use ($currentDayNumber, $lessonDayVideoId) {
                $q->where('lesson_days.day_number', '>', $currentDayNumber)
                    ->orWhere(function ($q2) use ($currentDayNumber, $lessonDayVideoId) {
                        $q2->where('lesson_days.day_number', '=', $currentDayNumber)
                            ->where('lesson_day_videos.id', '>', (int) $lessonDayVideoId);
                    });
            })
            ->orderBy('lesson_days.day_number')
            ->orderBy('lesson_day_videos.id')
            ->first();

        return [
            'current' => $current,
            'previous' => $prev,
            'next' => $next,
        ];
    }

    public function getCourseLevelPurchase(int $courseLevelId, int $userId)
    {
        // Find the course level purchase for this user and course level
        return CourseLevelPurchase::where('user_id', $userId)
            ->where('course_level_id', $courseLevelId)
            ->orderByDesc('valid_until')
            ->first();
    }
}
