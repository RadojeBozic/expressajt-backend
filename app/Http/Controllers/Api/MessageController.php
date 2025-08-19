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

    public function destroy($id)
{
    $message = Message::findOrFail($id);

    $message->delete();

    return response()->json(['message' => 'Poruka obrisana.']);
}

public function mine(Request $request)
    {
        $user = $request->user();
        if (!$user) return response()->json([], 200);

        $q = Message::query()->latest();

        if (Schema::hasColumn('messages', 'user_id')) {
            // primarno user_id
            $q->where('user_id', $user->id);
            // fallback – ako su stare poruke bez user_id, pokupi ih po email-u
            $q->orWhere(function ($qq) use ($user) {
                $qq->whereNull('user_id')->where('email', $user->email);
            });
        } else {
            // nema user_id kolone – filter po email-u
            $q->where('email', $user->email);
        }

        return response()->json($q->get());
    }
}

