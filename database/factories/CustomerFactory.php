<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use Illuminate\Support\Str;
use Faker\Generator as Faker;
use Illuminate\Support\Facades\Hash;

class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = User::class;

    public function definition()
    {
        $factory->define(User::class, function (Faker $faker) {
            return [
                'name' => $faker->name,
                'email' => $faker->unique()->email,
                'password' => Hash::make($faker->password),
            ];
        });
    }
}
