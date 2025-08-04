<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIController extends Controller
{
    protected function getUserPlan(Request $request): string
    {
        return $request->user()?->plan ?? 'starter';
    }

    protected function getDailyLimit(string $plan, string $type): int
    {
        $limits = [
            'suggest' => [
                'starter' => 2,
                'basic' => 3,
                'pro' => 5,
                'premium' => 7,
                'business' => 10,
            ],
            'chat' => [
                'starter' => 5,
                'basic' => 10,
                'pro' => 20,
                'premium' => 40,
                'business' => 100,
            ],
        ];

        return $limits[$type][$plan] ?? 2;
    }

    protected function checkAndTrackLimit(Request $request, string $type): bool|string
    {
        $plan = $this->getUserPlan($request);
        $limit = $this->getDailyLimit($plan, $type);
        $ip = $request->ip();
        $cacheKey = "ai_{$type}_limit_{$ip}";
        $used = Cache::get($cacheKey, 0);

        if ($used >= $limit) {
            return "Dostigli ste dnevni limit za AI ({$type}) zahteve za vaš paket: {$limit}.";
        }

        Cache::put($cacheKey, $used + 1, now()->endOfDay());

        Log::info("🧠 AI ({$type}) iskorišćen", [
            'ip' => $ip,
            'plan' => $plan,
            'used' => $used + 1,
            'limit' => $limit,
        ]);

        return true;
    }

    public function suggest(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:1000',
        ]);

        // ✅ Proveri limit
       /*  $limitCheck = $this->checkAndTrackLimit($request, 'suggest');
        if ($limitCheck !== true) {
            return response()->json(['error' => $limitCheck], 429);
        } */

        $openaiKey = config('services.openai.key');
        if (!$openaiKey) {
            return response()->json(['error' => 'API ključ nije podešen.'], 500);
        }

        // 📡 Poziv ka OpenAI
        $response = Http::withToken($openaiKey)->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4',
            'messages' => [
                ['role' => 'user', 'content' => $request->input('prompt')],
            ],
            'temperature' => 0.7,
        ]);

        if (!$response->successful()) {
            Log::error('OpenAI API greška (suggest)', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return response()->json(['error' => 'Greška u komunikaciji sa OpenAI.'], 500);
        }

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

        // ✅ Proveri limit
       /*  $limitCheck = $this->checkAndTrackLimit($request, 'chat');
        if ($limitCheck !== true) {
            return response()->json(['error' => $limitCheck], 429);
        } */

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
            Log::error('OpenAI API greška (chat)', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return response()->json(['error' => 'Greška u komunikaciji sa AI serverom.'], 500);
        }

        $content = $response->json('choices.0.message.content') ?? 'Nema odgovora.';

        return response()->json(['reply' => $content]);
    }
}
