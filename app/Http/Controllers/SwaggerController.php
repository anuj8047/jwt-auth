<?php
namespace App\Http\Controllers;
/**
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="Base API Server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     in="header",
 *     name="Authorization",
 *     description="Enter JWT Bearer token"
 * )
 */
 use Illuminate\Http\Request;
class SwaggerController extends Controller
{
    // Bas annotation ke liye hai ye class, koi code nahi chahiye
}
