<?php

namespace App\Repositories\FeaturedImage;

interface FeaturedImageRepositoryInterface
{
    public function all(?int $limit = null);

    public function find($id);

    public function create(array $data);

    public function update($id, array $data);

    public function delete($id);
}
