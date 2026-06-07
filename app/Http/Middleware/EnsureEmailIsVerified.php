<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->hasVerifiedEmail()) {
            return response()->json([
                'success' => false,
                'message' => 'Debes verificar tu correo electrónico para acceder a este recurso.',
            ], 403);
        }

        return $next($request);
    }
}
