<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class PasswordResetController extends ApiController
{
    /**
     * @OA\Post(
     *     path="/api/v1/auth/password/forgot",
     *     tags={"Auth - Password"},
     *     summary="Solicitar enlace de restablecimiento de contraseña",
     *     @OA\RequestBody(required=true,
     *         @OA\JsonContent(required={"email"},
     *             @OA\Property(property="email", type="string", example="juan@example.com")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Enlace enviado (si el correo existe)"),
     *     @OA\Response(response=422, description="Error de validación")
     * )
     */
    public function forgot(ForgotPasswordRequest $request): JsonResponse
    {
        // Always return success to avoid email enumeration
        Password::sendResetLink($request->only('email'));

        return $this->success(null, 'Si el correo existe, recibirás un enlace para restablecer tu contraseña.');
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/password/reset",
     *     tags={"Auth - Password"},
     *     summary="Restablecer contraseña con token",
     *     @OA\RequestBody(required=true,
     *         @OA\JsonContent(required={"token","email","password","password_confirmation"},
     *             @OA\Property(property="token",                 type="string"),
     *             @OA\Property(property="email",                 type="string", example="juan@example.com"),
     *             @OA\Property(property="password",              type="string", example="NuevaPass123!"),
     *             @OA\Property(property="password_confirmation", type="string", example="NuevaPass123!")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Contraseña restablecida"),
     *     @OA\Response(response=400, description="Token inválido o expirado"),
     *     @OA\Response(response=422, description="Error de validación")
     * )
     */
    public function reset(ResetPasswordRequest $request): JsonResponse
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password): void {
                $user->forceFill(['password' => Hash::make($password)])->save();
                $user->tokens()->delete(); // revoke all sessions after reset
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return $this->error(__($status), 400);
        }

        return $this->success(null, 'Contraseña restablecida exitosamente. Ya puedes iniciar sesión.');
    }
}
