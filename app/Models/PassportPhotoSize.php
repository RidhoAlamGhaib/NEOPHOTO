<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PassportPhotoSize extends Model
{
    protected $table = 'passport_photo_sizes';

    protected $fillable = [
        'country',
        'document_type',
        'width_mm',
        'height_mm',
        'width_px',
        'height_px',
        'min_copies',
        'background',
        'notes',
    ];
}
