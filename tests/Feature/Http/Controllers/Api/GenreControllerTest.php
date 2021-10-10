<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class GenreControllerTest extends TestCase {
    use DatabaseMigrations, TestValidations, TestSaves;
    private $genre;

    protected function setUp(): void {
        parent::setUp();
        $this->genre = factory(Genre::class)->create();
    }

    /** Cria e realiza uma listagem de generos. */
    public function testIndexGenre() {
        $response = $this->get(route('genres.index'));
        $response
            ->assertStatus(200)
            ->assertJson([$this->genre->toArray()]);
    }

    /** Testes para pesquisa de generos */
    public function testShowGenre() {
        $response = $this->get(route('genres.show', ['genre' => 101]));
        $response->assertStatus(404);

        $genre = factory(Genre::class)->create();
        $response = $this->get(route('genres.show', ['genre' => $genre->id]));
        $response
            ->assertStatus(200)
            ->assertJson($genre->toArray());
    }

    public function testInvalidationData() {
        $data = ['name' =>''];
        $this->assertInvalidationInStoreAction($data, 'required');
        $this->assertInvalidationInUpdateAction($data, 'required');

        $data = ['name' => str_repeat('a', 256)];
        $this->assertInvalidationInStoreAction($data, 'max.string', ['max' => 255]);
        $this->assertInvalidationInUpdateAction($data, 'max.string', ['max' => 255]);

        $data = ['is_active' => 'b'];
        $this->assertInvalidationInStoreAction($data, 'boolean');
        $this->assertInvalidationInUpdateAction($data, 'boolean');
    }

    /** Teste de criação de generos */
    public function testStoreGenre() {
        $data = [
            'name' => 'name_test_genre'
        ];

        $this->assertStore($data, $data + ['description' => null, 'is_active' => true, 'deleted_at' => null]);

        $data = [
            'name' => 'name_test_genre',
            'description' => 'description_test_genre',
            'is_active' => false
        ];

        $this->assertStore($data, $data + ['description' => 'description_test_genre', 'is_active' => false, 'deleted_at' => null]);
    }

    /** Testes para alterações de generos */
    public function testUpdateGenre() {
        $this->genre = factory(Genre::class)->create([
            'description' => 'description',
            'is_active' => false
        ]);

        $data = [
            'name' => 'test',
            'description' => 'description',
            'is_active' => false
        ];
        $response = $this->assertUpdate($data, $data + ['deleted_at' => null]);
        $response->assertJsonStructure([
            'created_at' => 'updated_at'
        ]);

        $data = ['name' => 'test_name_genre', 'description' => ''];
        $this->assertUpdate($data, array_merge($data, ['description' => null]));

        $data['description'] = 'test_description_genre';
        $this->assertUpdate($data, array_merge($data, ['description' => 'test_description_genre']));

        $data['description'] = null;
        $this->assertUpdate($data, array_merge($data, ['description' => null]));
    }

    /** Teste para deletar generos */
    public function testDelete() {
        $response = $this->json('DELETE', route('genres.destroy', ['genre' => 100]));
        $response->assertStatus(404);

        $response = $this->json('DELETE', route('genres.destroy', ['genre' => $this->genre->id]));
        $response->assertStatus(204);
    }

    protected function routeStore() {
        return route('genres.store');
    }

    protected function routeUpdate() {
        return route('genres.update', ['genre' => $this->genre->id]);
    }

    protected function model() {
        return Genre::class;
    }
}
