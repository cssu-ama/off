<?php

namespace Database\Seeders\V1\User;

use App\Models\V1\User\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::query()->firstOrCreate([
            'name' => config('admin.name'),
            'user_name' => config('admin.user_name'),
            'phone' => config('admin.phone'),
        ])->syncRoles(config('admin.role'));
    }
}
