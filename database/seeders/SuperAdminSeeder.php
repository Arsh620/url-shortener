<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insert company first
        DB::statement("INSERT INTO companies (name, created_at, updated_at) VALUES ('Main Company', NOW(), NOW())");

        // Insert SuperAdmin using raw SQL
        DB::statement("INSERT INTO users (name, email, password, role, company_id, created_at, updated_at) VALUES ('Super Admin', 'superadmin@example.com', '" . bcrypt('password') . "', 'superadmin', NULL, NOW(), NOW())");
    }
}
