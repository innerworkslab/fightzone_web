<?php

namespace App\Http\Controllers\API\v1\User\Profile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\UserPhysicalProfileService;

class PhysicalProfileController extends Controller
{
    protected UserPhysicalProfileService $service;

    public function __construct(UserPhysicalProfileService $service)
    {
        $this->service = $service;
    }

    public function show(Request $request)
    {
        $userId = ApiUser()->id;
        $profile = $this->service->findByUserId($userId);

        ResponseData($profile);
    }

    public function store(Request $request)
    {
        $userId = ApiUser()->id;

        $existing = $this->service->findByUserId($userId);

        if (!$existing) {
            $request->validate([
                'stance' => 'required|string|max:255',
                'rope_jump_level' => 'required|in:beginner,intermediate,expert',
                'fitness_level' => 'required|in:beginner,intermediate,expert',
                'boxing_level' => 'required|in:beginner,intermediate,expert',
                'weight' => 'required|numeric',
                'gender' => 'required|in:male,female'
            ]);

            $data = $request->only([
                'stance', 'rope_jump_level', 'fitness_level', 'boxing_level', 'weight', 'gender'
            ]);
            $data['user_id'] = $userId;

            $profile = $this->service->create($data);
            ResponseData($profile);
            return;
        }

        // Update: only provided attributes will be updated
        $request->validate([
            'stance' => 'sometimes|string|max:255',
            'rope_jump_level' => 'sometimes|in:beginner,intermediate,expert',
            'fitness_level' => 'sometimes|in:beginner,intermediate,expert',
            'boxing_level' => 'sometimes|in:beginner,intermediate,expert',
            'weight' => 'sometimes|numeric',
            'gender' => 'sometimes|in:male,female'
        ]);

        $data = $request->only([
            'stance', 'rope_jump_level', 'fitness_level', 'boxing_level', 'weight', 'gender'
        ]);

        $profile = $this->service->update($existing->id, $data);

        ResponseData($profile);
    }
}
