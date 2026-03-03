<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

use App\Models\Course;

use App\Repositories\CourseLevel\CourseLevelRepositoryInterface;

use App\Services\LessonDayService;
use App\Services\LessonDayVideoService;

class CourseLevelService
{
    public function __construct(
        protected CourseLevelRepositoryInterface $levelRepo,
        protected LessonDayService $lessonDayService,
        protected LessonDayVideoService $videoService
    )
    {

    }

    public function all(bool $onlyActive = true, ?array $filters = [], ?int $page = null, ?int $limit = null)
    {
        $query = $this->levelRepo->all($onlyActive, $filters, $limit);

        return $page
            ? $query->paginate($limit ?? config('common.list_count'))
            : $query->get();
    }

    public function find($id)
    {
        return $this->levelRepo->find($id);
    }

    public function create(array $data, array $lessonDays=[])
    {
        $course = Course::find($data['course_id']);
        if (!$course) {
            throw new \RuntimeException('Course not found');
        }

        if($course->courseLevels()->where('level', $data['level'])->exists()) {
            throw new \RuntimeException('Course level already exists');
        }

        try{
            DB::beginTransaction();
            $data['name'] = "{$course->name} course {$data['level']} level";
            $courseLevel = $this->levelRepo->create($data);

            foreach ($lessonDays as $lessonDay) {
                $createdLessonDay = $this->lessonDayService->create($courseLevel->id, [
                    'name' => isset($lessonDay['name']) ? $lessonDay['name'] : "Day " . $lessonDay['day_number'],
                    'course_level_id' => $courseLevel->id,
                    'day_number' => $lessonDay['day_number'],
                    'type' => $lessonDay['type'],
                    'duration' => isset($lessonDay['duration']) ? $lessonDay['duration'] : null,
                ]);

                if(count($lessonDay['videos']) > 0){
                    $this->videoService->attachVideoToLesson($createdLessonDay->id, $lessonDay['videos']);
                }
            }

            DB::commit();
            return $courseLevel;
        }catch(\Exception $e){
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function update($id, array $data, array $lessonDays=[])
    {
        $course = Course::find($data['course_id']);
        if (!$course) {
            throw new \RuntimeException('Course not found');
        }

        if($course->courseLevels()->where('id', '<>', $id)->where('level', $data['level'])->exists()) {
            throw new \RuntimeException('Course level already exists');
        }

        $item = $this->find($id);
        if (!$item) {
            throw new \RuntimeException('Course level not found');
        }

        try{
            DB::beginTransaction();
            $data['name'] = "{$course->name} course {$data['level']} level";
            $courseLevel = $this->levelRepo->update($id, $data);

            if(count($lessonDays) > 0){
                foreach($lessonDays as $lessonDay){
                    if(isset($lessonDay['id'])){
                        $updatedLessonDay = $this->lessonDayService->update($lessonDay['id'], [
                            'name' => isset($lessonDay['name']) ? $lessonDay['name'] : null,
                            'type' => $lessonDay['type'],
                            'duration' => isset($lessonDay['duration']) ? $lessonDay['duration'] : null,
                        ]);

                        if(isset($lessonDay['videos'])){
                            $this->videoService->attachVideoToLesson($updatedLessonDay->id, $lessonDay['videos']);
                        }
                    }else{
                        $createdLessonDay = $this->lessonDayService->create($courseLevel->id, [
                            'name' => isset($lessonDay['name']) ? $lessonDay['name'] : "Day " . $lessonDay['day_number'],
                            'course_level_id' => $item->id,
                            'day_number' => $lessonDay['day_number'],
                            'type' => $lessonDay['type'],
                            'duration' => isset($lessonDay['duration']) ? $lessonDay['duration'] : null,
                        ]);

                        if(isset($lessonDay['videos'])){
                            $this->videoService->attachVideoToLesson($createdLessonDay->id, $lessonDay['videos']);
                        }
                    }
                }
            }

            DB::commit();
            return $courseLevel;
        }catch(\Exception $e){
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function delete($id)
    {
        return $this->levelRepo->delete($id);
    }

    public function toggleActive($id)
    {
        return $this->levelRepo->toggleActive($id);
    }

    public function getLessons(int $courseId, $levelId, int $userId = null)
    {
        // When a user ID is provided, include completion info for that user.
        if ($userId !== null) {
            return $this->lessonDayService->allWithCompletionForUser($courseId, $levelId, $userId);
        }

        // Fallback: original behaviour (no completion info)
        return $this->levelRepo->getLessonDays($courseId, $levelId);
    }
}
