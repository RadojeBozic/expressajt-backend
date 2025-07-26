<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    /**
     * Obrada kontakt forme (API endpoint).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function submit(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email'],
            'message'    => ['required', 'string', 'min:5'],
            'newsletter' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validacija neuspešna.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // TODO: Snimi podatke u bazu ili pošalji mejl

        return response()->json([
            'message' => 'Poruka je uspešno poslata. Kontakt forma radi!',
            'data' => $request->only(['name', 'email', 'message', 'newsletter']),
        ], 200);
    }
}
