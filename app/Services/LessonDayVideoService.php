<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

use App\Models\CourseLevelPurchase;
use App\Models\LessonDayCompletion;
use App\Models\LessonDayVideo;
use App\Models\LessonDayVideoCompletion;
use App\Repositories\LessonDay\LessonDayRepositoryInterface;

use App\Services\ThirdParty\YoutubeService;

class LessonDayVideoService
{
    public function __construct(protected LessonDayRepositoryInterface $repo, protected YoutubeService $youtubeService){}

    public function attachVideoToLesson(int $lessonDayId, array $data)
    {
        foreach($data as $videoData){
            $videoMeta = $this->youtubeService->extractMeta($videoData['url']);

            $videoData['thumbnail_url'] = $videoMeta['thumbnail'];
            $videoData['duration'] = (isset($videoData['duration']))? $videoData['duration']: $this->secondsToTime($videoMeta['duration']);

            $videoData['name'] = (isset($videoData['name']))? $videoData['name']: $videoMeta['name'];
            $videoData['description'] = (isset($videoData['description']))? $videoData['description']: $videoMeta['description'];
            $this->repo->attachVideoToLessonDay($lessonDayId, $videoData);
        }
    }

    private function secondsToTime(int $seconds): string
    {
        return gmdate('H:i:s', $seconds);
    }

    public function update(int $id, array $data)
    {
        $lessonDayVideo = $this->repo->findLessonDayVideo($id);
        if (!$lessonDayVideo) {
            throw new \RuntimeException('Lesson day video not found');
        }
        if($lessonDayVideo->url == $data['url']){
            return $this->repo->updateLessonDayVideo($id, $data);
        }else{
            $videoMeta = $this->youtubeService->extractMeta($data['url']);
            $videoData = [
                'type' => $data['type'],
                'url' => $data['url']
            ];
            $videoData['thumbnail_url'] = $videoMeta['thumbnail'];
            $videoData['duration'] = (isset($videoData['duration']))? $videoData['duration']: $this->secondsToTime($videoMeta['duration']);

            $videoData['name'] = (isset($videoData['name']))? $videoData['name']: $videoMeta['name'];
            $videoData['description'] = (isset($videoData['description']))? $videoData['description']: $videoMeta['description'];
            return $this->repo->updateLessonDayVideo($id, $videoData);
        }

    }

    public function delete(int $id)
    {
        return DB::transaction(function () use ($id) {
            $lessonDayVideo = $this->repo->findLessonDayVideo($id);
            if (!$lessonDayVideo) {
                throw new \RuntimeException('Lesson day video not found');
            }

            $lessonDayId = (int) $lessonDayVideo->lesson_day_id;
            $courseLevelId = (int) $lessonDayVideo->lessonDay->course_level_id;

            $deleted = $this->repo->deleteLessonDayVideo($id);
            $this->syncLessonDayCompletions($lessonDayId, $courseLevelId);

            return $deleted;
        });
    }

    private function syncLessonDayCompletions(int $lessonDayId, int $courseLevelId): void
    {
        $remainingVideoIds = LessonDayVideo::where('lesson_day_id', $lessonDayId)
            ->pluck('id')
            ->all();

        if (empty($remainingVideoIds)) {
            LessonDayCompletion::where('lesson_day_id', $lessonDayId)->delete();
            return;
        }

        $requiredCompletionCount = count($remainingVideoIds);
        $courseLevelPurchaseIds = CourseLevelPurchase::where('course_level_id', $courseLevelId)
            ->pluck('id')
            ->all();

        if (empty($courseLevelPurchaseIds)) {
            return;
        }

        $completedByPurchase = LessonDayVideoCompletion::selectRaw('course_level_purchase_id, COUNT(*) as completed_count, MAX(completed_at) as completed_at')
            ->whereIn('course_level_purchase_id', $courseLevelPurchaseIds)
            ->whereIn('lesson_day_video_id', $remainingVideoIds)
            ->groupBy('course_level_purchase_id')
            ->get()
            ->keyBy('course_level_purchase_id');

        foreach ($courseLevelPurchaseIds as $purchaseId) {
            $completionSummary = $completedByPurchase->get($purchaseId);

            if ($completionSummary && (int) $completionSummary->completed_count === $requiredCompletionCount) {
                LessonDayCompletion::updateOrCreate(
                    [
                        'course_level_purchase_id' => $purchaseId,
                        'lesson_day_id' => $lessonDayId,
                    ],
                    [
                        'completed_at' => $completionSummary->completed_at,
                    ]
                );
            } else {
                LessonDayCompletion::where('course_level_purchase_id', $purchaseId)
                    ->where('lesson_day_id', $lessonDayId)
                    ->delete();
            }
        }
    }
}
