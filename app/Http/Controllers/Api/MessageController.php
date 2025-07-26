<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Message;

class MessageController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'message' => 'required|string',
            'newsletter' => 'boolean'
        ]);

        $message = Message::create($validated);

        return response()->json([
            'message' => 'Poruka uspešno sačuvana.',
            'data' => $message
        ], 201);
    }

    public function index()
    {
        return Message::orderBy('created_at', 'desc')->get();
    }
}

