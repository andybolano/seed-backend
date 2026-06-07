<?php

namespace App\Http\Controllers\Api;

use App\Traits\ApiResponse;
use App\Http\Controllers\Controller;

/**
 * @OA\Info(
 *     title="Seed Backend API",
 *     version="1.0.0",
 *     description="API base reutilizable con autenticación, roles y permisos.",
 *     @OA\Contact(email="admin@example.com")
 * )
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 * @OA\Server(url=L5_SWAGGER_CONST_HOST, description="Servidor principal")
 */
abstract class ApiController extends Controller
{
    use ApiResponse;
}
