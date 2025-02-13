<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 *
 * @OA\Server(
 *     url="https://ciefmate-production.up.railway.app",
 *     description="HTTPS Server"
 * )
 *
 * @OA\Server(
 *     url="http://127.0.0.1:8000",
 *     description="HTTP Server"
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
 * @OA\Schema(
 *     schema="Categories",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="category title"),
 *     @OA\Property(property="worksapce_id", type="integer", example="2"),
 * )
 *
 * @OA\Schema(
 *     schema="Sections",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="section title"),
 *     @OA\Property(property="worksapce_id", type="integer", example="2"),
 * )
 *
 * @OA\Schema(
 *     schema="Warehouse",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="section title"),
 *     @OA\Property(property="type", type="integer", example="equipment || ingredient"),
 * )
 *
 * @OA\Schema(
 *     schema="Ingredients",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="ingriedent name"),
 *     @OA\Property(property="cover", type="sting", example="cover.jpg"),
 *     @OA\Property(property="description", type="sting", example="some description to descripe ingredient"),
 *     @OA\Property(property="unit", type="sting", example="ml || l || gm || kg || unit"),
 *     @OA\Property(property="quantity", type="integer", example="12.5 || 11"),
 * )
 *
 * @OA\Schema(
 *     schema="Equipments",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="ingriedent name"),
 *     @OA\Property(property="cover", type="sting", example="cover.jpg"),
 *     @OA\Property(property="description", type="sting", example="some description to descripe equipment"),
 *     @OA\Property(property="unit", type="sting", example="unit"),
 *     @OA\Property(property="quantity", type="integer", example="11"),
 * )
 *
 *
 *
 * @OA\Schema(
 *     schema="RecipeResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Recipe 1"),
 *     @OA\Property(property="description", type="string", example="A delicious recipe"),
 *     @OA\Property(property="cover", type="string", example="cover.jpg"),
 *     @OA\Property(property="preparation_time", type="integer", example=30, description="Preparation time in minutes"),
 *     @OA\Property(property="calories", type="integer", example=200, description="Calories per serving"),
 *     @OA\Property(property="protein", type="integer", example=15, description="Protein content in grams"),
 *     @OA\Property(property="fats", type="integer", example=10, description="Fat content in grams"),
 *     @OA\Property(property="carbs", type="integer", example=30, description="Carbohydrate content in grams"),
 *     @OA\Property(property="status", type="string", enum={"completed", "draft"}, example="completed"),
 *     @OA\Property(property="category_id", type="integer", example=2, description="ID of the associated category"),
 *     @OA\Property(property="workspace_id", type="integer", example=1, description="ID of the workspace"),
 *     @OA\Property(property="ingredients", type="array", @OA\Items(ref="#/components/schemas/IngredientResource")),
 *     @OA\Property(property="equipments", type="array", @OA\Items(ref="#/components/schemas/EquipmentResource")),
 *     @OA\Property(property="instructions", type="array", @OA\Items(ref="#/components/schemas/InstructionResource"))
 * )
 * @OA\Schema(
 *     schema="IngredientResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Flour"),
 *     @OA\Property(property="unit", type="string", example="grams"),
 *     @OA\Property(property="quantity", type="integer", example=200)
 * )
 * @OA\Schema(
 *     schema="EquipmentResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Oven"),
 *     @OA\Property(property="cover", type="string", example="oven.jpg"),
 *     @OA\Property(property="description", type="string", example="Electric oven for baking"),
 *     @OA\Property(property="unit", type="string", example="piece"),
 *     @OA\Property(property="quantity", type="integer", example=1)
 * )
 * @OA\Schema(
 *     schema="InstructionResource",
 *     type="object",
 *     @OA\Property(property="order", type="integer", example=1, description="Step order in the recipe"),
 *     @OA\Property(property="description", type="string", example="Mix the ingredients thoroughly"),
 *     @OA\Property(property="media", type="string", example="step1.jpg", description="Image or video file for the instruction"),
 *     @OA\Property(property="timer", type="string", example="10 min", description="Suggested time for this step")
 * )
 * @OA\Schema(
 *     schema="Comment",
 *     type="object",
 *     @OA\Property(property="comment", type="string", example="Mix the ingredients thoroughly"),
 *     @OA\Property(property="user", type="array", @OA\Items(ref="#/components/schemas/User")),
 * )
 */

class SwaggerController extends Controller
{
    public function index(){
        Http::
    }
}
