<?php

namespace Database\Seeders;

use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Follower;
use App\Models\Post;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->faker = Faker::create();

        $this->command->info('Creating Admin Account...');

        $admin = User::create([
            'username' => 'admin',
            'name' => 'Admin',
            'password' => app('hash')->make('password'),
            'email' => 'admin@fake.com',
            'location' => 'Cape Town',
            'gender' => ['male', 'female'][rand(0, 1)],
            'birth_date' => Carbon::now(),
            'bio' => 'This is the default account',
        ]);

        $this->command->info('Admin Account Created.');

        $this->command->info('Seeding Users...');

        User::factory()
            ->count(2000)
            ->create();

        $this->command->info('Seeding Users done.');

        $users = User::all();

        $this->command->info('Seeding Followers And Posts...');

        foreach ($users as $user) {
            Follower::firstOrCreate(
                ['follower' => $user->id],
                ['following' => $admin->id]
            );

            Post::create(
                ['content' => $this->faker->sentence(rand(5, 15))],
                ['user_id' => $user->id]
            );
        };

        $this->command->info('Seeding done.');
    }
}
