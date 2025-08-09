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
        $email = 'admin@eventkan.com';
        if(User::where('email', $email)->first() == null){
            User::factory()->create([
                'name' => 'Dinta Admin',
                'email' => $email,
                'password' => bcrypt('admin123'),
                'role' => 'admin',
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
