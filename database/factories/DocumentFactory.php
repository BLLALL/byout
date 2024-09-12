<?php

namespace Database\Factories;

use App\Models\Chalet;
use App\Models\Document;
use App\Models\Home;
use App\Models\Hotel;
use App\Models\Owner;
use App\Models\Tour;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Document>
 */
class DocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */


    public function definition(): array
    {
        $documentableTypes = [
            Home::class,
            Hotel::class,
            Chalet::class,
            Tour::class,
        ];
        $documentableType = fake()->randomElement($documentableTypes);

        $documentableId = $documentableType::factory()->create()->id;

        if ($documentableType == Tour::class) {
            $documentType = fake()->randomElement(Document::TOUR_DOCUMENT_TYPE);
        } else {
            $documentType = fake()->randomElement(Document::ACCOMMODATION_DOCUMENT_TYPE);
        }
        return [
            'documentable_id' => $documentableId,
            'documentable_type' => $documentableType,
            'document_type' => $documentType,
            'file_path' => 'https://fayroz97.com/real-estate/storage/file',
            'owner_id' => Owner::factory(),
        ];
    }
}
