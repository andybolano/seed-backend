<?php

namespace App\Services;

use App\Models\User;
use Laravel\Sanctum\NewAccessToken;

class AuthService
{
    private const ACCESS_ABILITY  = 'access';
    private const REFRESH_ABILITY = 'refresh';
    private const ACCESS_TTL      = 60;    // minutes
    private const REFRESH_TTL     = 43200; // 30 days

    public function issueTokens(User $user): array
    {
        $access  = $user->createToken('access_token',  [self::ACCESS_ABILITY],  now()->addMinutes(self::ACCESS_TTL));
        $refresh = $user->createToken('refresh_token', [self::REFRESH_ABILITY], now()->addMinutes(self::REFRESH_TTL));

        return $this->tokenResponse($access, $refresh);
    }

    public function rotateTokens(User $user): array
    {
        // Revoke current refresh token only; existing access tokens expire naturally
        $user->currentAccessToken()->delete();

        return $this->issueTokens($user);
    }

    public function revokeAll(User $user): void
    {
        $user->tokens()->delete();
    }

    private function tokenResponse(NewAccessToken $access, NewAccessToken $refresh): array
    {
        return [
            'access_token'  => $access->plainTextToken,
            'refresh_token' => $refresh->plainTextToken,
            'token_type'    => 'Bearer',
            'expires_in'    => self::ACCESS_TTL * 60,
        ];
    }
}
