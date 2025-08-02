<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIController extends Controller
{
    public function suggest(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:1000',
        ]);

        $ip = $request->ip();
        $cacheKey = 'ai_limit_' . $ip;

        // 🚦 Rate limit: max 2 puta dnevno po IP
        $used = Cache::get($cacheKey, 0);
        if ($used >= 2) {
            return response()->json(['error' => 'Dostigli ste dnevni limit od 2 AI zahteva.'], 429);
        }

        // 🔐 Opciona logika za korisnike (ako je autentifikacija aktivna)
        $user = $request->user();
        if ($user && $user->plan === 'starter') {
            return response()->json(['error' => 'Ova funkcija nije dostupna u vašem paketu.'], 403);
        }

        Cache::put($cacheKey, $used + 1, now()->endOfDay());

        // 🔑 OpenAI ključ
        $openaiKey = config('services.openai.key');
        if (!$openaiKey) {
            return response()->json(['error' => 'API ključ nije podešen.'], 500);
        }

        // 📡 API poziv ka OpenAI
        $response = Http::withToken($openaiKey)->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4',
            'messages' => [
                ['role' => 'user', 'content' => $request->input('prompt')],
            ],
            'temperature' => 0.7,
        ]);

        if (!$response->successful()) {
            Log::error('OpenAI API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return response()->json(['error' => 'Greška u komunikaciji sa OpenAI.'], 500);
        }

        // ✅ Fallback ako nema sadržaja
        $content = $response->json('choices.0.message.content') ?? 'Nema odgovora.';

        return response()->json([
            'suggestion' => $content,
        ]);
    }

    public function chat(Request $request)
{
    $request->validate([
        'message' => 'required|string|max:1000',
    ]);

    $ip = $request->ip();
    $cacheKey = 'chat_limit_' . $ip;
    $used = Cache::get($cacheKey, 0);

    if ($used >= 10) {
        return response()->json(['error' => 'Dnevni limit poruka je dostignut.'], 429);
    }

    Cache::put($cacheKey, $used + 1, now()->endOfDay());

    $openaiKey = config('services.openai.key');
    if (!$openaiKey) {
        return response()->json(['error' => 'API ključ nije podešen.'], 500);
    }

    $response = Http::withToken($openaiKey)->post('https://api.openai.com/v1/chat/completions', [
        'model' => 'gpt-4',
        'messages' => [
            ['role' => 'system', 'content' => 'Ti si ljubazan virtuelni asistent firme Express Web. Odgovaraj kratko, jasno i korisno.'],
            ['role' => 'user', 'content' => $request->message],
        ],
        'temperature' => 0.7,
    ]);

    if (!$response->successful()) {
        \Log::error('OpenAI Chat API greška', [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);
        return response()->json(['error' => 'Greška u komunikaciji sa AI serverom.'], 500);
    }

    $content = $response->json('choices.0.message.content') ?? 'Nema odgovora.';
    return response()->json(['reply' => $content]);
}

}
