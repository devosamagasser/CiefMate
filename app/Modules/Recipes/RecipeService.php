<?php

namespace App\Modules\Recipes;

use App\Facades\FileHandeler;
use App\Modules\Recipes\Models\Instruction;
use App\Modules\Recipes\Models\Recipe;

class RecipeService
{
    public function prepareRecipeData($request, $cover)
    {
        return [
            'title' => $request->title,
            'description' => $request->description,
            'cover' => $cover,
            'preparation_time' => $request->preparation_time,
            'calories' => $request->calories,
            'protein' => $request->protein,
            'fats' => $request->fats,
            'carbs' => $request->carbs,
            'status' => $request->status,
            'category_id' => $request->category_id
        ];
    }

    public function storeRecipeMainInfo($request)
    {
        $cover = FileHandeler::storeFile($request->cover, null, $request->cover->getClientOriginalExtension());
        return Recipe::create($this->prepareRecipeData($request, $cover));
    }

    public function updateRecipeMainInfo($request, $id)
    {
        $recipe = Recipe::findOrFail($id);
        if($recipe->cover){
            $cover = FileHandeler::updateFile($request->cover, $recipe->cover, null, $request->cover->getClientOriginalExtension());
        }else{
            $cover = FileHandeler::storeFile($request->cover, null, $request->cover->getClientOriginalExtension());
        }
            $recipe->update($this->prepareRecipeData($request, $cover));
        return $recipe;
    }


    public function storeRecipeIngredients($recipe, $request)
    {
        $data = collect($request->ingredients ?? [])->map(fn($ingredient, $key) => [
            'ingredent_id' => $ingredient,
            'quantity' => $request->ingredients_quantities[$key] ?? null,
            'unit' => $request->ingredients_units[$key] ?? null,
        ])->toArray();

        $recipe->ingredients()->sync($data);
    }

    public function storeRecipeEquipments($recipe, $request)
    {
        $data = collect($request->equipments ?? [])->map(fn($equipment, $key) => [
            'equipment_id' => $equipment,
            'quantity' => $request->equipments_quantities[$key] ?? null,
        ])->toArray();

        $recipe->equipments()->sync($data);
    }

    public function storeRecipeInstructions($recipe, $request)
    {
        $instructions = collect($request->instructions_order ?? [])
            ->map(fn($step, $key) => [
                'recipe_id' => $recipe->id,
                'timer' => $request->instructions_timer[$key] ?? null,
                'media' => isset($request->instructions_media[$key])
                    ? FileHandeler::storeFile($request->instructions_media[$key], null, $request->instructions_media[$key]->getClientOriginalExtension())
                    : null,
                'step_no' => $step,
                'description' => $request->instructions_descriptions[$key] ?? null,
            ])
            ->toArray();

        return Instruction::upsert($instructions, ['recipe_id', 'step_no'], ['timer', 'media', 'description']);
    }

    public function updateRecipeInstructions($recipe, $request)
    {
        $instructions = Instruction::where('recipe_id', $recipe->id);

        $instructions->pluck('media')->each(function($media){
            if($media) FileHandeler::deleteFile($media);
        });
        $instructions->delete();

        return $this->storeRecipeInstructions($recipe, $request);
    }
}
