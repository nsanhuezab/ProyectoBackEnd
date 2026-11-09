<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            CareerSeeder::class,
            UserSeeder::class,
            CoordinatorSeeder::class,
            CompanySeeder::class,
            StudentSeeder::class,
            OfferSeeder::class,
            ApplicationSeeder::class,
            DemoCvSeeder::class,
            PracticeSeeder::class,
            PracticeLogSeeder::class,
            PracticeEvaluationSeeder::class,
            CompanyReviewSeeder::class,
            ApplicationMessageSeeder::class,
        ]);
    }
}
