<?php

namespace Tests\Feature;

use App\Models\Recipe;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class RecipesHubTest extends TestCase
{
    use RefreshDatabase;

    public function test_recipe_show_includes_servings_tips_and_related_recipes(): void
    {
        $recipe = Recipe::factory()->create([
            'category' => 'Swallow Recipes',
            'is_published' => true,
            'servings' => 4,
            'tips' => 'Stir continuously for a lump-free swallow.',
        ]);
        Recipe::factory()->create(['category' => 'Swallow Recipes', 'is_published' => true]);
        Recipe::factory()->draft()->create(['category' => 'Swallow Recipes']);

        $this->get(route('recipes.show', $recipe))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('recipes/show')
                ->where('recipe.servings', 4)
                ->where('recipe.tips', 'Stir continuously for a lump-free swallow.')
                ->has('related', 1)
                ->where('related.0.id', fn ($id) => $id !== $recipe->id));
    }

    public function test_recipe_show_returns_404_for_draft(): void
    {
        $recipe = Recipe::factory()->draft()->create();

        $this->get(route('recipes.show', $recipe))->assertNotFound();
    }

    public function test_recipes_index_lists_published_recipes_with_category_filter(): void
    {
        Recipe::factory()->create(['category' => 'Baking', 'is_published' => true]);
        Recipe::factory()->create(['category' => 'Breakfast Recipes', 'is_published' => true]);

        $this->get(route('recipes.index', ['category' => 'Baking']))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('recipes/index')
                ->has('recipes', 1)
                ->where('activeCategory', 'Baking'));
    }
}
