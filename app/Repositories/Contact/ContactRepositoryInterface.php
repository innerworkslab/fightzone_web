<?php

namespace App\Repositories\Contact;

use App\Models\Contact;

interface ContactRepositoryInterface
{
    public function all(bool $onlyActive = true, ?array $filters = [], ?int $limit = null);
    public function find($id);
    public function create(array $data): Contact;
    public function update($id, array $data): Contact;
    public function delete($id);
    public function toggleActive($id): Contact;
}
