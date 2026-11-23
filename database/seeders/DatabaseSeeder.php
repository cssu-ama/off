<?php

namespace Database\Seeders;

use Database\Seeders\V1\Role\RoleSeeder;
use Database\Seeders\V1\User\AdminUserSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            AdminUserSeeder::class,
        ]);
    }
}
