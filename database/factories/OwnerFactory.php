<?php

namespace Database\Factories;

use App\Models\Owner;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Owner>
 */
class OwnerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organization' => fake()->company,
            'identification_card' => UploadedFile::fake()->create('identification_card.pdf', 100),
            'licensing' => UploadedFile::fake()->create('identification_card.pdf', 100),
            'affiliation_certificate' => UploadedFile::fake()->create('affiliation_certificate.pdf', 100),
            'commercial_register' => UploadedFile::fake()->create('commercial_register.pdf', 100),
            'user_id' => User::factory(),
        ];
    }

    public function asHomeOwner()
    {
        return $this->afterCreating(function (Owner $owner) {
            $owner->user->assignRole('Home Owner');
        });
    }
    public function asChaletOwner()
    {
        return $this->afterCreating(function (Owner $owner) {
            $owner->user->assignRole('Chalet Owner');
        });
    }
    public function asHotelOwner()
    {
        return $this->afterCreating(function (Owner $owner) {
            $owner->user->assignRole('Hotel Owner');
        });
    }

    public function asTourCompanyOwner()
    {
        return $this->afterCreating(function (Owner $owner) {
            $owner->user->assignRole('Tour Company Owner');
        });
    }
}
