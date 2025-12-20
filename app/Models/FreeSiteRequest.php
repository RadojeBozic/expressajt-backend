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

        protected static function booted()
    {
        static::deleting(function ($site) {
            if ($site->logo_path) Storage::disk('public')->delete($site->logo_path);
            if ($site->hero_image) Storage::disk('public')->delete($site->hero_image);
            if ($site->about_image) Storage::disk('public')->delete($site->about_image);
            if ($site->pdf_file) Storage::disk('public')->delete($site->pdf_file);
            foreach ($site->offer_items ?? [] as $item) {
                if (isset($item['image'])) Storage::disk('public')->delete($item['image']);
            }
        });
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
