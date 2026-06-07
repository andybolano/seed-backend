<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Api\ApiController;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailVerificationController extends ApiController
{
    /**
     * @OA\Get(
     *     path="/api/v1/auth/email/verify/{id}/{hash}",
     *     tags={"Auth - Email"},
     *     summary="Verificar correo electrónico",
     *     description="Enlace enviado por email. Incluye firma temporal.",
     *     @OA\Parameter(name="id",   in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="hash", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Email verificado"),
     *     @OA\Response(response=400, description="Enlace inválido o expirado"),
     *     @OA\Response(response=404, description="Usuario no encontrado")
     * )
     */
    public function verify(Request $request, int $id, string $hash): JsonResponse
    {
        if (! $request->hasValidSignature()) {
            return $this->error('El enlace de verificación es inválido o ha expirado.', 400);
        }

        $user = User::find($id);

        if (! $user) {
            return $this->notFound('Usuario no encontrado.');
        }

        if (! hash_equals($hash, sha1($user->getEmailForVerification()))) {
            return $this->error('El hash de verificación no coincide.', 400);
        }

        if ($user->hasVerifiedEmail()) {
            return $this->success(null, 'El correo ya estaba verificado.');
        }

        $user->markEmailAsVerified();

        return $this->success(null, 'Correo verificado exitosamente. Ya puedes iniciar sesión.');
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/email/resend",
     *     tags={"Auth - Email"},
     *     summary="Reenviar correo de verificación",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Correo reenviado"),
     *     @OA\Response(response=400, description="El correo ya está verificado")
     * )
     */
    public function resend(): JsonResponse
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            return $this->error('Tu correo ya está verificado.', 400);
        }

        $user->sendEmailVerificationNotification();

        return $this->success(null, 'Correo de verificación reenviado.');
    }
}
