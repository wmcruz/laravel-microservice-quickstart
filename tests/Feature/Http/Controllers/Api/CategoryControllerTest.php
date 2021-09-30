<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCase;

/** Teste de Integração HTTP */
class CategoryControllerTest extends TestCase
{
    use DatabaseMigrations;
    
    /** Cria e realiza uma listagem de categorias */
    public function testIndex() {
        $category = factory(Category::class)->create();

        $response = $this->get(route('categories.index'));

        $response
            ->assertStatus(200)
            ->assertJson([$category->toArray()]);
    }

    /** Cria e pesquisa uma categoria */
    public function testShow() {
        $category = factory(Category::class)->create();

        $response = $this->get(route('categories.show', ['category' => $category->id]));

        $response
            ->assertStatus(200)
            ->assertJson($category->toArray());
    }
    
    /** Teste de Validação de dados de categoria */
    public function testInvalidationData() {
        $response = $this->json('POST', route('categories.store', []));
        $this->assertInvalidationRequired($response);

        $response = $this->json('POST', route('categories.store', [
            'name' => str_repeat('a', 256),
            'is_active' => 'a'
        ]));

        $this->assertInvalidationMax($response);
        $this->assertInvalidationBoolean($response);

        $category = factory(Category::class)->create();
        $response = $this->json('PUT', route('categories.update', ['category' => $category->id]), []);
        $this->assertInvalidationRequired($response);

        $response = $this->json(
            'PUT', 
            route('categories.update', ['category' => $category->id]), 
            [
                'name' => str_repeat('a', 256),
                'is_active' => 'a'
            ]
        );
        
        $this->assertInvalidationMax($response);
        $this->assertInvalidationBoolean($response);
    }

    /** Teste de Criação de Categoria */
    public function testStore() {
        $response = $this->json('POST', route('categories.store'), ['name' => 'test']);

        $id = $response->json('id');
        $category = Category::find($id);

        $response
            ->assertStatus(201)
            ->assertJson($category->toArray());
        $this->assertTrue($response->json('is_active'));
        $this->assertNull($response->json('description'));

        $response = $this->json('POST', route('categories.store'), [
            'name' => 'test',
            'description' => 'description',
            'is_active' => false
        ]);

        $response
            ->assertJsonFragment([
                'description' => 'description',
                'is_active' => false
            ]);
    }

    /** Teste de Criação e Alteração de Categoria */
    public function testUpdate() {
        $category = factory(Category::class)->create([
            'description' => 'description',
            'is_active' => false
        ]);

        $response = $this->json(
            'PUT', 
            route('categories.update', ['category' => $category->id]),
            [
                'name' => 'test',
                'description' => 'teste',
                'is_active' => true
            ]);

        $id = $response->json('id');
        $category = Category::find($id);

        $response
            ->assertStatus(200)
            ->assertJson($category->toArray())
            ->assertJsonFragment([
                'description' => 'teste',
                'is_active' => true
            ]);

        $response = $this->json(
            'PUT', 
            route('categories.update', ['category' => $category->id]),
            [
                'name' => 'test',
                'description' => ''
            ]);
        
        $response
            ->assertJsonFragment([
                'description' => null
            ]);
    }
    
    /** Teste para deletar uma categoria */
    public function testDelete() {
        // Teste para deletar categoria inexistente.
        $response = $this->json(
            'DELETE',
            route('categories.destroy', ['category' => 100]));

        $response
            ->assertStatus(404);
        
        // Criando e deletando uma categoria
        $category = factory(Category::class)->create([
            'description' => 'Teste de descrição',
            'is_active' => false
        ]);

        $response = $this->json(
            'DELETE',
            route('categories.destroy', ['category' => $category->id]));

        $response
            ->assertStatus(204);
    }

    protected function assertInvalidationRequired(TestResponse $response){
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonMissingValidationErrors(['is_active'])
            ->assertJsonFragment([
                \Lang::get('validation.required', ['attribute' => 'name'])
            ]);
    }

    protected function assertInvalidationMax(TestResponse $response){
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonFragment([
                \Lang::get('validation.max.string', ['attribute' => 'name', 'max' => 255])
            ]);
    }

    protected function assertInvalidationBoolean(TestResponse $response){
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['is_active'])
            ->assertJsonFragment([
                \Lang::get('validation.boolean', ['attribute' => 'is active'])
            ]);
    }
}