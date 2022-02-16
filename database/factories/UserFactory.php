<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'username' => $this->faker->unique()->userName(),
            'name' => $this->faker->name,
            'password' => app('hash')->make('password'),
            'email' => $this->faker->unique()->safeEmail,
            'location' => $this->faker->address(),
            'gender' => ['male', 'female'][rand(0, 1)],
            'birth_date' => $this->faker->dateTimeBetween('-50 years', 'now'),
            'bio' => $this->faker->sentence(rand(5, 15)),
        ];
    }
}
