<?php

    namespace Database\Factories;

    use App\Models\Owner;
    use Illuminate\Database\Eloquent\Factories\Factory;

    /**
     * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehicle>
     */
    class VehicleFactory extends Factory
    {
        /**
         * Define the model's default state.
         *
         * @return array<string, mixed>
         */
        public function definition(): array
        {
            $vehicleTypes = [
                'car' => [3, 5],
                'van' => [7, 14],
                'bus' => [24, 30, 46, 49, 53, 67],
            ];
            $type = fake()->randomElement(array_keys($vehicleTypes));
            $seat = fake()->randomElement($vehicleTypes[$type]);
            return [
                'type' => $type,
                'model' => fake()->randomElement(['Toyota', 'Honda', 'Ford', 'Chevrolet', 'Mercedes-Benz', 'Volvo']),
                'registration_number' => fake()->unique()->regexify('[A-Z]{3}[0-9]{4}'),
                'vehicle_images' => [
                    "https://loremflickr.com/640/480/",
                    "https://loremflickr.com/640/480/",
                ],
                "seats_number" => $seat,
                'has_wifi' => $this->faker->boolean(),
                'has_air_conditioner' => $this->faker->boolean(90),
                'has_gps' => $this->faker->boolean(80),
                'has_movie_screens' => $this->faker->boolean(40),
                'has_entrance_camera' => function ($attributes) {
                    return $attributes['type'] === 'bus' ? $this->faker->boolean(60) : false;
                },
                'has_passenger_camera' => function ($attributes) {
                    return $attributes['type'] === 'bus' ? $this->faker->boolean(60) : false;
                },
                'has_bathroom' => function ($attributes) {
                    return $attributes['type'] === 'bus' ? $this->faker->boolean(60) : false;
                },
                'owner_id' => Owner::factory(),
            ];
        }

        public function car()
        {
            return $this->state(function (array $attributes)
            {
                return [
                    'type' => 'car',
                    'seats_number' => fake()->randomElement([3, 5])
                ];
            });
        }

        public function van()
        {
            return $this->state(function (array $attributes)
            {
                return [
                    'type' => 'van',
                    'seats_number' => fake()->randomElement([7, 14])
                ];
            });
        }

        public function bus()
        {
            return $this->state(function (array $attributes)
            {
                return [
                    'type' => 'bus',
                    'seats_number' => fake()->randomElement([24, 30, 46, 49, 53, 67])
                ];
            });
        }
    }
