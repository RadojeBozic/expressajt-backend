<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FreeSiteRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Http\Requests\UpdateFreeSiteRequest;

class FreeSiteRequestController extends Controller
{
    // 1. STORE – Kreiranje prezentacije
    public function store(Request $request)
    {
        $type = $request->input('type', 'basic');
        $upload = fn($file, $folder) => $file ? $file->store("prezentacije/{$folder}", 'public') : null;

        $validated = $request->validate($this->rules($type));
        $data = $this->extractData($validated, $request, $upload);

        $data['slug'] = Str::slug($validated['name']) . '-' . uniqid();
        $data['user_id'] = auth()->id();
        $data['status'] = 'pending';

        $record = FreeSiteRequest::create($data);

        return response()->json([
            'user_id' => auth()->id() ?? null,
            'message' => '✅ Prezentacija sačuvana!',
            'slug' => $record->slug,
        ], 201);
    }

    // 2. SHOW – Prikaz jedne prezentacije
    public function show($slug)
    {
        $site = FreeSiteRequest::where('slug', $slug)->firstOrFail();
        return response()->json($site);
    }

    // 3. UPDATE – Ažuriranje prezentacije
    public function update(UpdateFreeSiteRequest $request, $slug)
    {
        $validated = $request->validated();
        $site = FreeSiteRequest::where('slug', $slug)->firstOrFail();
        $user = auth()->user();

        if ($user->id !== $site->user_id && !in_array($user->role, ['admin', 'superadmin'])) {
            return response()->json(['message' => '⛔ Nemaš dozvolu.'], 403);
        }

        $type = $site->type;
        $upload = fn($file, $folder) => $file ? $file->store("prezentacije/{$folder}", 'public') : null;

        $validated = $request->validate($this->rules($type, true));

        $data = $this->extractData($validated, $request, $upload, $site);
        $site->fill($data);
        $site->save();

        return response()->json([
            'message' => '✅ Prezentacija ažurirana.',
            'slug' => $site->slug
        ]);
    }

    // 4. DESTROY – Brisanje prezentacije
    public function destroy($slug)
    {
        $site = FreeSiteRequest::where('slug', $slug)->firstOrFail();
        $user = auth()->user();

        if ($user->id !== $site->user_id && !in_array($user->role, ['admin', 'superadmin'])) {
            return response()->json(['message' => '⛔ Nemaš dozvolu.'], 403);
        }

        $site->delete();
        return response()->json(['message' => '🗑 Prezentacija obrisana.']);
    }

    // 5. INDEX – Lista svih prezentacija
    public function index()
    {
        return FreeSiteRequest::latest()->get();
    }

    // 6. CHANGE STATUS – Promena statusa prezentacije
    public function changeStatus(Request $request, $slug)
    {
        $site = FreeSiteRequest::where('slug', $slug)->firstOrFail();
        $user = auth()->user();

        if (!in_array($user->role, ['admin', 'superadmin'])) {
            return response()->json(['message' => 'Nemaš dozvolu.'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,active,rejected'
        ]);

        $site->status = $validated['status'];
        $site->save();

        return response()->json(['message' => 'Status ažuriran.']);
    }

    // VALIDACIJA
   private function rules($type, $isUpdate = false)
{
    // 👇 Ako je update, slike su opcione; ako je store, obavezne
    $requiredImage = $isUpdate ? 'nullable|image|max:4096' : 'required|image|max:4096';
    $optionalImage = 'nullable|image|max:4096';

    $rules = [
        'name' => 'required|string|max:255',
        'description' => 'required|string|max:1000',
        'email' => 'required|email',
        'phone' => 'required|string|max:50',
        'facebook' => 'required|url',
        'instagram' => 'required|url',
        'logo' => 'nullable|image|max:2048', // logo uvek opcioni

        // HERO
        'heroTitle' => 'required|string|max:255',
        'heroSubtitle' => 'required|string|max:250',
        'heroImage' => $requiredImage,

        // O NAMA
        'aboutTitle' => 'required|string|max:255',
        'aboutText' => 'required|string|max:1000',
        'aboutImage' => $requiredImage,

        // PONUDA
        'offerTitle' => 'required|string|max:255',
        'offerItems' => 'required|array|min:1|max:12',
        'offerItems.*.title' => 'required|string|max:255',
        'offerItems.*.image' => $requiredImage,

        'template' => 'required|string|in:klasicni,moderni,galerija,biznis,dark,klasicni-pro,moderni-pro,galerija-pro,biznis-pro,dark-pro',
    ];

    if ($type === 'pro') {
        $rules = array_merge($rules, [
            'pdf_file' => 'nullable|file|mimes:pdf|max:5120',
            'video_url' => 'nullable|url',
            'google_map_link' => 'nullable|string|max:255',
            'address_city' => 'nullable|string|max:100',
            'address_street' => 'nullable|string|max:155',
            'phone2' => 'nullable|string|max:50',
            'phone3' => 'nullable|string|max:50',
            'email2' => 'nullable|email',
            'email3' => 'nullable|email',
        ]);
    }

    return $rules;
}


    // EKSTRAKCIJA PODATAKA
    private function extractData($validated, Request $request, $upload, $existing = null)
    {
        $type = str_contains($validated['template'], '-pro') ? 'pro' : 'free';

        $data = [
            'name' => $validated['name'],
            'description' => $validated['description'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'facebook' => $validated['facebook'],
            'instagram' => $validated['instagram'],
            'template' => $validated['template'],
            'type' => $type,

            'logo_path' => $request->file('logo') ? $upload($request->file('logo'), 'logo') : ($existing->logo_path ?? null),
            'hero_title' => $validated['heroTitle'],
            'hero_subtitle' => $validated['heroSubtitle'],
            'hero_image' => $request->file('heroImage') ? $upload($request->file('heroImage'), 'hero') : ($existing->hero_image ?? null),
            'about_title' => $validated['aboutTitle'],
            'about_text' => $validated['aboutText'],
            'about_image' => $request->file('aboutImage') ? $upload($request->file('aboutImage'), 'about') : ($existing->about_image ?? null),
            'offer_title' => $validated['offerTitle'],
        ];

        $offerItems = [];
        foreach ($validated['offerItems'] as $index => $item) {
            $image = $request->file("offerItems.$index.image");
            $offerItems[] = [
                'title' => $item['title'],
                'image' => $image ? $upload($image, 'offer') : ($existing->offer_items[$index]['image'] ?? null),
            ];
        }
        $data['offer_items'] = $offerItems;

        if ($type === 'pro') {
            $data['pdf_file'] = $request->file('pdf_file') ? $upload($request->file('pdf_file'), 'docs') : ($existing->pdf_file ?? null);
            $data['video_url'] = $request->input('video_url');
            $data['google_map_link'] = $request->input('google_map_link');
            $data['address'] = trim(($request->input('address_street') ?? '') . ', ' . ($request->input('address_city') ?? ''));
            $data['phone2'] = $request->input('phone2');
            $data['phone3'] = $request->input('phone3');
            $data['email2'] = $request->input('email2');
            $data['email3'] = $request->input('email3');
        }

        return $data;
    }
}
