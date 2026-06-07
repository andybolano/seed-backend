<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\UserResource;

class UserController extends ApiController
{
    /**
     * @OA\Get(
     *     path="/api/v1/users",
     *     tags={"Users"},
     *     summary="Listar todos los usuarios",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Lista de usuarios"),
     *     @OA\Response(response=401, description="No autorizado"),
     *     @OA\Response(response=403, description="Prohibido")
     * )
     */
    public function index(): JsonResponse
    {
        $users = User::with('roles')->paginate(15);

        return $this->success([
            'users' => UserResource::collection($users->items()),
            'meta'  => [
                'current_page' => $users->currentPage(),
                'last_page'    => $users->lastPage(),
                'per_page'     => $users->perPage(),
                'total'        => $users->total(),
            ],
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/users/{id}",
     *     tags={"Users"},
     *     summary="Obtener un usuario por ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Usuario encontrado"),
     *     @OA\Response(response=404, description="No encontrado")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $user = User::with('roles')->find($id);

        if (! $user) {
            return $this->notFound('Usuario no encontrado.');
        }

        return $this->success(new UserResource($user));
    }
}
