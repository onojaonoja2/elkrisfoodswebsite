<?php

namespace Database\Seeders;

use App\Models\User;
use App\Support\UserRole;
use Illuminate\Database\Seeder;
use RuntimeException;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(ContentSeeder::class);

        $password = (string) config('admin.password');

        if (app()->isProduction() && in_array($password, ['', 'password'], true)) {
            throw new RuntimeException('Set a strong ADMIN_PASSWORD before seeding production.');
        }

        User::query()->updateOrCreate(
            ['email' => config('admin.email')],
            [
                'name' => config('admin.name'),
                'password' => $password,
                'role' => UserRole::Admin,
                'email_verified_at' => now(),
            ],
        );
    }
}
