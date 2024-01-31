<?php

namespace Database\Factories;

use App\Models\Recruit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Recruit>
 */
class RecruitFactory extends Factory
{

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Recruit::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'  => $this->faker->name,
            'phone' => $this->faker->numerify('##########'),
            'email' => $this->faker->unique()->safeEmail,
            'company_id'  => $this->faker->numberBetween(1, 10),
            'market_id'  => $this->faker->numberBetween(1, 10),
        ];
    }
}
