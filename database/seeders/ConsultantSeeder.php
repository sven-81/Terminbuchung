<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

final class ConsultantSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('consultants')->insert([
            [
                'id' => '9d4e8c2a-1b3d-4f5e-9a8c-7b6e5d4c3a2b',
                'name' => 'Dr. Anna Müller',
                'email' => 'anna.mueller@example.com',
                'daily_capacity_minutes' => 480,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Dr. Thomas Schmidt',
                'email' => 'thomas.schmidt@example.com',
                'daily_capacity_minutes' => 420,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

