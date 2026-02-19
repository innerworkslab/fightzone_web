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

            foreach ($day->videos as $video) {
                $video->is_completed = false;
                $video->completed_at = null;
            }
        }

        // Find the latest course_level_purchase for this user & level
        $clp = CourseLevelPurchase::where('user_id', $userId)
            ->where('course_level_id', $levelId)
            ->orderByDesc('valid_until')
            ->first();

        if (! $clp) {
            // User has not purchased this level; return with defaults
            return $lessonDays;
        }

        $now = now();
        $isWithinValidity = false;
        if ($clp->valid_from && $clp->valid_until) {
            $isWithinValidity = $now->between($clp->valid_from, $clp->valid_until);
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

        foreach ($lessonDays as $day) {
            $day->is_within_validity = $isWithinValidity;

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

        foreach ($lessonDay->videos as $video) {
            $video->is_completed = false;
            $video->completed_at = null;
        }

        // Find the latest course_level_purchase for this user & level
        $clp = CourseLevelPurchase::where('user_id', $userId)
            ->where('course_level_id', $levelId)
            ->orderByDesc('valid_until')
            ->first();

        if (! $clp) {
            // User has not purchased this level; return with defaults
            return $lessonDay;
        }

        $now = now();
        if ($clp->valid_from && $clp->valid_until) {
            $lessonDay->is_within_validity = $now->between($clp->valid_from, $clp->valid_until);
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
}
