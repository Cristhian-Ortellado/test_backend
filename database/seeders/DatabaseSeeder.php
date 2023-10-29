<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Utilities\RoleUtility;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        \App\Models\User::factory()->create([
            'username' => 'agent',
            'email' => 'agent@example.com',
            'role' => RoleUtility::AGENT
        ]);

        \App\Models\User::factory()->create([
            'username' => 'manager',
            'email' => 'manager@example.com',
            'role' => RoleUtility::MANAGER
        ]);
    }
}
