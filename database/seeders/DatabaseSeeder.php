<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if(User::where('email', 'dintasyaifuddin@admin.com')->first() == null){
            User::factory()->create([
                'name' => 'Dinta Syaifuddin',
                'email' => 'dintasyaifuddin@admin.com',
                'password' => bcrypt('admin123'),
                'roles' => 'admin',
            ]);
        }
        Category::firstOrCreate([
            'name' => 'Festival',
        ]);
        Category::firstOrCreate([
            'name' => 'Charity',
        ]);
    }
}
