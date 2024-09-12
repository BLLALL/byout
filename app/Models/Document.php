<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function documentable()
    {
        return $this->morphTo();
    }

    public const ACCOMMODATION_DOCUMENT_TYPE = [
        'HOTELS_LICENSE' => 'hotel_license',
        'SIGNATORY_AUTHORIZATION' => 'signatory_authorization',
        'PROPERTY_OWNERSHIP' => 'property_ownership',
        'OFFICIAL_LETTER' => 'official_letter',
        'AGREEMENT_CONTRACT' => 'agreement_contract',
    ];

    public const TOUR_DOCUMENT_TYPE = [
        'COMMERCIAL_DOCUMENT' => 'commercial_document',
        'TRANSPORT_LICENSE' => 'transport_license',
        'DRIVER_LICENSE' => 'driver_license',
    ];
}
