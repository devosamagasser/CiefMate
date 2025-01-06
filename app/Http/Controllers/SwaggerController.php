<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
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
 *  * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *     @OA\Property(property="avatar", type="string", example="avatars/avatar.jpg"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class SwaggerController extends Controller
{
    //
}