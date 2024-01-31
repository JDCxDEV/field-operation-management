<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class CompanyFactory extends Factory
{

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Company::class;    

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $address = "{$this->faker->buildingNumber} {$this->faker->streetName}";
        return [
            "company" => $this->faker->company,
            "phone" => $this->faker->numerify('##########'),
            "address_1" => $address,
            "address_2" => "",
            "city" => $this->faker->city,
            "state" => $this->faker->stateAbbr,
            "zip" => $this->faker->postcode
        ];
    }
}
