<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

use App\Repositories\CourseCategory\CourseCategoryRepositoryInterface;

class CourseCategoryService
{
    public function __construct(protected CourseCategoryRepositoryInterface $repo)
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

    public function create(array $data, ?UploadedFile $image=null)
    {
        $cat = $this->repo->create($data);
        if($image){
            $path = $image->store("course_categories/{$cat->id}", 'public');
            $cat->image_path = $path;
            $cat->save();
        }
        return $cat;
    }

    public function update($id, array $data, ?UploadedFile $image=null)
    {
        $cat = $this->repo->update($id, $data);
        if($image){
            if($cat->image_path){
                DeleteFileFromServer($cat->image_path);
            }
            $path = $image->store("course_categories/{$cat->id}", 'public');
            $cat->image_path = $path;
            $cat->save();
        }
        return $cat;
    }

    public function delete($id)
    {
        return $this->repo->delete($id);
    }

    public function toggleActive($id)
    {
        return $this->repo->toggleActive($id);
    }
}
