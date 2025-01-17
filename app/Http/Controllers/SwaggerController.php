<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * 
 * @OA\Server(
 *     url="http://ciefmate-production.up.railway.app",
 *     description="HTTP Server"
 * )
 * @OA\Server(
 *     url="https://ciefmate-production.up.railway.app",
 *     description="HTTPS Server"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="Bearer",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     name="Authorization",
 *     in="header",
 * )
 *
 * @OA\Info(
 *     title="Chief-Mate",
 *     version="1.0.0",
 *     description="API documentation for Chief-Mate Application",
 *     @OA\Contact(
 *         name="Osama Gasser",
 *         email="devosamagasser@gmail.com"
 *     ),
 *     @OA\License(
 *         name="Developed by Osama Gasser",
 *         url="https://example.com"
 *     )
 * )
 * 
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *     @OA\Property(property="phone", type="string", example="099 2899 634 34"),
 *     @OA\Property(property="avatar", type="string", example="avatars/avatar.jpg"),
 * )
 * 
 * @OA\Schema(
 *     schema="Workspace",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Workspace name"),
 *     @OA\Property(property="color", type="string", example="blue"),
 *     @OA\Property(property="color_code", type="string", example="#0000FF"),
 * )
 * 
 */
class SwaggerController extends Controller
{
    //
}