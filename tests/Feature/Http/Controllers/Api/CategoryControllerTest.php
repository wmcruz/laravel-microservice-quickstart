<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

/** Teste de Integração HTTP */
class CategoryControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves;
    private $category;

    protected function setUp(): void {
        parent::setUp();
        $this->category = factory(Category::class)->create();
    }
    
    /** Cria e realiza uma listagem de categorias */
    public function testIndex() {
        $response = $this->get(route('categories.index'));

        $response
            ->assertStatus(200)
            ->assertJson([$this->category->toArray()]);
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
        $data = ['name' => ''];
        $this->assertInvalidationInStoreAction($data, 'required');
        $this->assertInvalidationInUpdateAction($data, 'required');

        $data = ['name' => str_repeat('a', 256)];
        $this->assertInvalidationInStoreAction($data, 'max.string', ['max' => 255]);
        $this->assertInvalidationInUpdateAction($data, 'max.string', ['max' => 255]);

        $data = ['is_active' =>'a'];
        $this->assertInvalidationInStoreAction($data, 'boolean');
        $this->assertInvalidationInUpdateAction($data, 'boolean');
    }

    /** Teste de Criação de Categoria */
    public function testStore() {
        $data = [
            'name' => 'test'
        ];

        $this->assertStore($data, $data + ['description' => null, 'is_active' => true, 'deleted_at' => null]);

        $data = [
            'name' => 'test',
            'description' => 'description',
            'is_active' => false
        ];

        $this->assertStore($data, $data + ['description' => 'description', 'is_active' => false]);
    }

    /** Teste de Criação e Alteração de Categoria */
    public function testUpdate() {
        $this->category = factory(Category::class)->create([
            'description' => 'description',
            'is_active' => false
        ]);

        $data = [
            'name' => 'test',
            'description' => 'test',
            'is_active' => true
        ];
        $response = $this->assertUpdate($data, $data + ['deleted_at' => null]);
        $response->assertJsonStructure([
            'created_at' => 'updated_at'
        ]);

        $data = [
            'name' => 'test',
            'description' => ''
        ];
        $this->assertUpdate($data, array_merge($data, ['description' => null]));

        $data['description'] = 'test';
        $this->assertUpdate($data, array_merge($data, ['description' => 'test']));

        $data['description'] = null;
        $this->assertUpdate($data, array_merge($data, ['description' => null]));
    }
    
    /** Teste para deletar uma categoria */
    public function testDelete() {
        $response = $this->json('DELETE', route('categories.destroy', ['category' => 100]));
        $response->assertStatus(404);
        
        $response = $this->json('DELETE', route('categories.destroy', ['category' => $this->category->id]));
        $response->assertStatus(204);
    }
    protected function routeStore() {
        return route('categories.store');
    }

    protected function routeUpdate() {
        return route('categories.update', ['category' => $this->category->id]);
    }

    protected function model() {
        return Category::class;
    }
}