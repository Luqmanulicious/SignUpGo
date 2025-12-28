<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Conference Author',
                'slug' => 'conference_author',
            ],
            [
                'name' => 'Conference Jury',
                'slug' => 'conference_jury',
            ],
            [
                'name' => 'Innovation Participant',
                'slug' => 'innovation_participant',
            ],
            [
                'name' => 'Innovation Jury',
                'slug' => 'innovation_jury',
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}