<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends ApiController
{
    public function __construct(private readonly AuthService $auth) {}

    /**
     * @OA\Post(
     *     path="/api/v1/auth/register",
     *     tags={"Auth"},
     *     summary="Registrar nuevo usuario",
     *     @OA\RequestBody(required=true,
     *         @OA\JsonContent(required={"name","email","password","password_confirmation"},
     *             @OA\Property(property="name",     type="string",  example="Juan Pérez"),
     *             @OA\Property(property="email",    type="string",  example="juan@example.com"),
     *             @OA\Property(property="password", type="string",  example="Secret123!"),
     *             @OA\Property(property="password_confirmation", type="string", example="Secret123!")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Usuario registrado. Se envía email de verificación."),
     *     @OA\Response(response=422, description="Error de validación")
     * )
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole('user');
        $user->sendEmailVerificationNotification();

        $tokens = $this->auth->issueTokens($user);

        return $this->created([
            'user'   => new UserResource($user),
            'tokens' => $tokens,
        ], 'Usuario registrado. Revisa tu correo para verificar tu cuenta.');
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/login",
     *     tags={"Auth"},
     *     summary="Iniciar sesión",
     *     @OA\RequestBody(required=true,
     *         @OA\JsonContent(required={"email","password"},
     *             @OA\Property(property="email",    type="string", example="juan@example.com"),
     *             @OA\Property(property="password", type="string", example="Secret123!")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Login exitoso. Devuelve access_token y refresh_token."),
     *     @OA\Response(response=401, description="Credenciales inválidas"),
     *     @OA\Response(response=403, description="Email no verificado")
     * )
     */
    public function login(LoginRequest $request): JsonResponse
    {
        if (! Auth::attempt($request->only('email', 'password'))) {
            return $this->unauthorized('Credenciales inválidas.');
        }

        $user = Auth::user();

        if (! $user->hasVerifiedEmail()) {
            Auth::logout();
            return $this->forbidden('Debes verificar tu correo electrónico antes de iniciar sesión.');
        }

        // Revoke old tokens and issue fresh pair
        $this->auth->revokeAll($user);
        $tokens = $this->auth->issueTokens($user);

        return $this->success([
            'user'   => new UserResource($user),
            'tokens' => $tokens,
        ], 'Sesión iniciada.');
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/logout",
     *     tags={"Auth"},
     *     summary="Cerrar sesión (revoca todos los tokens)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Sesión cerrada")
     * )
     */
    public function logout(): JsonResponse
    {
        $this->auth->revokeAll(Auth::user());

        return $this->success(null, 'Sesión cerrada.');
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/refresh",
     *     tags={"Auth"},
     *     summary="Refrescar access token usando el refresh token",
     *     description="Envía el **refresh_token** como Bearer. Devuelve un nuevo par de tokens.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Tokens renovados"),
     *     @OA\Response(response=401, description="Refresh token inválido o expirado")
     * )
     */
    public function refresh(): JsonResponse
    {
        $tokens = $this->auth->rotateTokens(Auth::user());

        return $this->success(['tokens' => $tokens], 'Tokens renovados.');
    }

    /**
     * @OA\Get(
     *     path="/api/v1/auth/me",
     *     tags={"Auth"},
     *     summary="Obtener usuario autenticado",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Perfil del usuario autenticado")
     * )
     */
    public function me(): JsonResponse
    {
        return $this->success(new UserResource(Auth::user()));
    }
}
