<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends ApiController
{
    public function __construct(private readonly AuthService $auth) {}

    /**
     * @OA\Get(
     *     path="/api/v1/profile",
     *     tags={"Profile"},
     *     summary="Ver perfil del usuario autenticado",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Perfil del usuario")
     * )
     */
    public function show(): JsonResponse
    {
        return $this->success(new UserResource(Auth::user()));
    }

    /**
     * @OA\Put(
     *     path="/api/v1/profile",
     *     tags={"Profile"},
     *     summary="Actualizar perfil",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name",  type="string", example="Juan Actualizado"),
     *             @OA\Property(property="email", type="string", example="nuevo@example.com")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Perfil actualizado"),
     *     @OA\Response(response=422, description="Error de validación")
     * )
     */
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = Auth::user();
        $data = $request->only(['name', 'email']);

        // If email changed, require re-verification
        if (isset($data['email']) && $data['email'] !== $user->email) {
            $data['email_verified_at'] = null;
        }

        $user->update($data);

        if (array_key_exists('email_verified_at', $data)) {
            $user->sendEmailVerificationNotification();
        }

        return $this->success(new UserResource($user->fresh()), 'Perfil actualizado.');
    }

    /**
     * @OA\Put(
     *     path="/api/v1/profile/password",
     *     tags={"Profile"},
     *     summary="Cambiar contraseña",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true,
     *         @OA\JsonContent(required={"current_password","password","password_confirmation"},
     *             @OA\Property(property="current_password", type="string"),
     *             @OA\Property(property="password",         type="string", example="Nueva123!"),
     *             @OA\Property(property="password_confirmation", type="string", example="Nueva123!")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Contraseña cambiada. Se revocan todos los tokens."),
     *     @OA\Response(response=422, description="Error de validación")
     * )
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $user = Auth::user();
        $user->update(['password' => Hash::make($request->password)]);

        // Revoke all tokens so other devices must re-login
        $this->auth->revokeAll($user);

        $tokens = $this->auth->issueTokens($user);

        return $this->success(['tokens' => $tokens], 'Contraseña actualizada. Se cerraron las demás sesiones.');
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/profile",
     *     tags={"Profile"},
     *     summary="Eliminar cuenta permanentemente",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Cuenta eliminada")
     * )
     */
    public function destroy(): JsonResponse
    {
        $user = Auth::user();

        $this->auth->revokeAll($user);
        $user->delete();

        return $this->success(null, 'Cuenta eliminada permanentemente.');
    }
}
