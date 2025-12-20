<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFreeSiteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Provera da li je template "pro"
        $template = $this->input('template');
        $isPro = str_contains($template, '-pro');

        // Osnovno pravilo za slike – sve su opcione jer je ovo "update"
        $imageRule = 'nullable|image|max:4096';

        // Osnovna pravila za free i pro prezentacije
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'email' => 'required|email',
            'phone' => 'required|string|max:50',
            'facebook' => 'required|url',
            'instagram' => 'required|url',
            'logo' => 'nullable|image|max:2048',

            // Hero sekcija
            'heroTitle' => 'required|string|max:255',
            'heroSubtitle' => 'required|string|max:250',
            'heroImage' => $imageRule,

            // O nama
            'aboutTitle' => 'required|string|max:255',
            'aboutText' => 'required|string|max:1000',
            'aboutImage' => $imageRule,

            // Ponuda
            'offerTitle' => 'required|string|max:255',
            'offerItems' => 'required|array|min:1|max:12',
            'offerItems.*.title' => 'required|string|max:255',
            'offerItems.*.image' => $imageRule,

            // Šablon
            'template' => 'required|string|in:klasicni,moderni,galerija,biznis,dark,klasicni-pro,moderni-pro,galerija-pro,biznis-pro,dark-pro',
        ];

        // Ako je pro verzija, dodaj dodatna pravila
        if ($isPro) {
            $rules += [
                'pdf_file' => 'nullable|file|mimes:pdf|max:5120',
                'video_url' => 'nullable|url',
                'google_map_link' => 'nullable|string|max:255',
                'address_city' => 'nullable|string|max:100',
                'address_street' => 'nullable|string|max:155',
                'phone2' => 'nullable|string|max:50',
                'phone3' => 'nullable|string|max:50',
                'email2' => 'nullable|email',
                'email3' => 'nullable|email',
            ];
        }

        return $rules;
    }
}
