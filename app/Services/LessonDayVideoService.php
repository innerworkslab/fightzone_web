<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

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
}
