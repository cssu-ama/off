<?php

namespace Database\Seeders\V1\Role;

use App\Enums\V1\Role\UserRole;
use App\Models\V1\Role\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (UserRole::cases() as $role) {
            Role::query()->firstOrCreate([
                'name' => $role->value,
                'guard_name' => config('auth.defaults.guard'),
            ]);
        }
    }
}
