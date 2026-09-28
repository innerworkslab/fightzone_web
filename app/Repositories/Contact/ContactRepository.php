<?php

namespace App\Repositories\Contact;

use App\Models\Contact;

class ContactRepository implements ContactRepositoryInterface
{
    public function all(bool $onlyActive = true, ?array $filters = [], ?int $limit = null)
    {
        $query = Contact::query()->latest();

        if ($onlyActive) {
            $query->where('is_active', true);
        }

        foreach ($this->normalizeFilters($filters) as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            if ($key === 'is_active') {
                $query->where($key, (bool) $value);
            } elseif ($key === 'type') {
                $query->where($key, $value);
            } elseif ($key === 'search') {
                $query->where(function ($q) use ($value) {
                    $q->where('name', 'like', "%{$value}%")
                        ->orWhere('contact', 'like', "%{$value}%");
                });
            } else {
                $query->where($key, 'like', "%{$value}%");
            }
        }

        if ($limit) {
            $query->limit($limit);
        }

        return $query;
    }

    public function find($id)
    {
        return Contact::find($id);
    }

    public function create(array $data): Contact
    {
        return Contact::create($data);
    }

    public function update($id, array $data): Contact
    {
        $contact = $this->find($id);
        if (!$contact) {
            throw new \RuntimeException('Contact not found');
        }

        $contact->fill($data);
        $contact->save();

        return $contact;
    }

    public function delete($id)
    {
        $contact = $this->find($id);
        if (!$contact) {
            throw new \RuntimeException('Contact not found');
        }

        return $contact->delete();
    }

    public function toggleActive($id): Contact
    {
        $contact = $this->find($id);
        if (!$contact) {
            throw new \RuntimeException('Contact not found');
        }

        $contact->is_active = !$contact->is_active;
        $contact->save();

        return $contact;
    }

    private function normalizeFilters(?array $filters = []): array
    {
        $normalized = [];
        foreach ($filters ?? [] as $key => $value) {
            if (is_int($key) && is_array($value)) {
                foreach ($value as $filterKey => $filterValue) {
                    $normalized[$filterKey] = $filterValue;
                }
            } elseif (is_string($key)) {
                $normalized[$key] = $value;
            }
        }

        return $normalized;
    }
}
