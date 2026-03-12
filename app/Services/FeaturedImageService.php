<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

use App\Repositories\FeaturedImage\FeaturedImageRepositoryInterface;

class FeaturedImageService
{
    public function __construct(protected FeaturedImageRepositoryInterface $repo){}

    public function all(?int $page, ?int $limit)
    {
        $query = $this->repo->all($limit);

        return $page
            ? $query->paginate($limit ?? config('common.list_count'))
            : $query->get();
    }

    public function find($id)
    {
        return $this->repo->find($id);
    }

    public function create(UploadedFile $file)
    {
        $path = $file->store("/featured_images",'public');
        $data = [
            'image_path' => $path
        ];
        return $this->repo->create($data);
    }

    public function update($id, UploadedFile $file)
    {
        $existing = $this->repo->find($id);
        if(!$existing){
            return null;
        }
        DeleteFileFromServer($existing->image_path);
        $path = $file->store("/featured_images",'public');
        $data = [
            'image_path' => $path
        ];
        return $this->repo->update($id, $data);
    }

    public function delete($id)
    {
        $existing = $this->repo->find($id);
        if(!$existing){
            return null;
        }
        DeleteFileFromServer($existing->image_path);
        return $this->repo->delete($id);
    }
}
