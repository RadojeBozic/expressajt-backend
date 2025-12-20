<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Models\Message;

class ContactController extends Controller
{
    /**
     * 📩 Snimanje poruke iz kontakt forme
     */
    public function submit(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'max:150'],
            'message'    => ['required', 'string', 'min:5'],
            'newsletter' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => '❌ Validacija neuspešna.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $message = Message::create([
            'name'       => $request->input('name'),
            'email'      => $request->input('email'),
            'message'    => $request->input('message'),
            'newsletter' => $request->boolean('newsletter'),
        ]);

        return response()->json([
            'message' => '✅ Poruka je uspešno poslata.',
            'data'    => $message
        ], 201);
    }
}
