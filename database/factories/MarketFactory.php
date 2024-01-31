<?php

namespace Database\Factories;

use App\Models\Market;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class MarketFactory extends Factory
{

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Market::class;    

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $address = "{$this->faker->buildingNumber} {$this->faker->streetName}";
        return [
            "company_id" => $this->faker->numberBetween(1, 10),
            "market_name" => $this->faker->company,
            'email' => $this->faker->unique()->safeEmail,            
            "phone" => $this->faker->numerify('##########'),
            "address_1" => $address,
            "address_2" => "",
            "city" => $this->faker->city,
            "state" => $this->faker->stateAbbr,
            "zip" => $this->faker->postcode 
        ];
    }
}
