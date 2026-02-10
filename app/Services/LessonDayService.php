<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

use App\Models\CourseLevel;

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
}
