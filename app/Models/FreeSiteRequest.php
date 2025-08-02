<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FreeSiteRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        // Osnovni podaci
        'user_id',
        'name',
        'description',
        'email',
        'phone',
        'facebook',
        'instagram',
        'logo_path',
        'template',
        'slug',
        'type',
        'plan',

        // Hero sekcija
        'hero_title',
        'hero_subtitle',
        'hero_image',

        // O nama
        'about_title',
        'about_text',
        'about_image',

        // Ponuda
        'offer_title',
        'offer_items',

        // Pro dodatno
        'google_map_link',
        'pdf_file',
        'video_url',
        'address',
        'phone2',
        'phone3',
        'email2',
        'email3',
    ];

    protected $casts = [
        'offer_items' => 'array',
    ];
}
