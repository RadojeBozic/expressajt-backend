<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // Za SPA/API: ne radi redirect, nego pusti 401 JSON
        if ($request->expectsJson() || $request->is('api/*')) {
            return null;
        }

        // Za klasične web rute — ako ti treba:
        return route('login');
    }
}
