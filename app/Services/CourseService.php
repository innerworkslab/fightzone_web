<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

use App\Repositories\Course\CourseRepositoryInterface;

class CourseService
{
    public function __construct(protected CourseRepositoryInterface $repo)
    {

    }

    public function all(bool $onlyActive = true, ?array $filters = [], ?int $page = null, ?int $limit = null)
    {
        $query = $this->repo->all($onlyActive, $filters, $limit);

        return $page
            ? $query->paginate($limit ?? config('common.list_count'))
            : $query->get();
    }

    public function find($id)
    {
        return $this->repo->find($id);
    }

    public function findWithDetails($id)
    {
        return $this->repo->findWithDetails($id);
    }

    public function findWithCourseLevelPurchaseStatus($courseId, $userId)
    {
        return $this->repo->findWithDetailsForUser($courseId, $userId);
    }

    public function create(array $data, ?UploadedFile $image=null)
    {
        $course = $this->repo->create($data);
        if($image){
            $path = $image->store("courses/{$course->id}", 'public');
            $course->image_path = $path;
            $course->save();
        }
        return $course;
    }

    public function update($id, array $data, ?UploadedFile $image=null)
    {
        $course = $this->repo->update($id, $data);
        if($image){
            if($course->image_path){
                DeleteFileFromServer($course->image_path);
            }
            $path = $image->store("courses/{$course->id}", 'public');
            $course->image_path = $path;
            $course->save();
        }
        return $course;
    }

    public function delete($id)
    {
        return $this->repo->delete($id);
    }

    public function toggleActive($id)
    {
        return $this->repo->toggleActive($id);
    }

    public function getByCategory($categoryId, bool $onlyActive = true, ?int $page = null, ?int $limit = null)
    {
        $query = $this->repo->getByCategory($categoryId, $onlyActive);

        return $page
            ? $query->paginate($limit ?? config('common.list_count'))
            : $query->get();
    }

    public function getByLevel($level, bool $onlyActive = true, ?int $page = null, ?int $limit = null)
    {
        $query = $this->repo->getByLevel($level, $onlyActive);

        return $page
            ? $query->paginate($limit ?? config('common.list_count'))
            : $query->get();
    }

    public function getLessonDays($courseId, $levelId)
    {

    }
}
