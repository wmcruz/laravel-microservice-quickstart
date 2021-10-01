<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCase;
use Tests\Traits\TestValidations;

class GenreControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations;

    /** Cria e realiza uma listagem de generos. */
    public function testIndexGenre() {
        $genres = factory(Genre::class)->create();

        $response = $this->get(route('genres.index'));

        $response
            ->assertStatus(200)
            ->assertJson([$genres->toArray()]);
    }
    
    /** Testes para pesquisa de generos */
    public function testShowGenre() {
        // Test: Pesquisa de genero inexistente
        $response = $this->get(
            route('genres.show', ['genre' => 101]));

        $response
            ->assertStatus(404);
        
        // Test: Cria genero e pesquisa
        $genre = factory(Genre::class)->create();

        $response = $this->get(
            route('genres.show', ['genre' => $genre->id])
        );

        $response
            ->assertStatus(200);
    }

    public function testinvalidationData() {
        // Test: Criando genero inválido
        $response = $this->json('POST', route('genres.store', []));

        $this->assertInvalidationRequired($response);

        // Test: criacao de genero com nome e situacao inválidas
        $response = $this->json('POST', route('genres.store', [
            'name' => str_repeat('a', 256),
            'is_active' => 'a'
        ]));

        $this->assertInvalidationMax($response);
        $this->assertInvalidationBoolean($response);

        // Test: Criação de genero e alteração inválida
        $genre = factory(Genre::class)->create();
        $response = $this->json('PUT',
            route('genres.update', ['genre' => $genre->id]), []);
        $this->assertInvalidationRequired($response);

        // Test: Alteração de genero com dados inválidos
        $response = $this->json(
            'PUT',
            route('genres.update', ['genre' => $genre->id]),
            [
                'name' => str_repeat('a', 256),
                'is_active' => 'a'
            ]
            );

        $this->assertInvalidationMax($response);
        $this->assertInvalidationBoolean($response);
    }

    /** Teste de criação de generos */
    public function testStoreGenre() {
        // Test: Criação de genero via HTTP, pesquisa do mesmo no BD e assertions
        $response = $this->json('POST', route('genres.store'), [
            'name' => 'name_test_genre'
        ]);

        $id_genre = $response->json('id');
        $genre = Genre::find($id_genre);

        $response
            ->assertStatus(201)
            ->assertJson($genre->toArray());
        $this->assertTrue($response->json('is_active'));
        $this->assertNull($response->json('description'));

        // Test: Criação de genero fora do padrão, e assertions
        $response = $this->json('POST', route('genres.store'), [
            'name' => 'name_test_genre',
            'description' => 'description',
            'is_active' => false
        ]);

        $response
            ->assertJsonFragment([
                'description' => 'description',
                'is_active' => false
            ]);
    }

    /** Testes para alterações de generos */
    public function testUpdateGenre() {
        // Test: Criação no BD de um genero, alteração do genero via HTTP e assertions.
        $genre = factory(Genre::class)->create([
            'description' => 'description',
            'is_active' => false
        ]);

        $response = $this->json(
            'PUT',
            route('genres.update', ['genre' => $genre->id]),
            [
                'name' => 'test',
                'description' => 'teste',
                'is_active' => true
            ]);
        
        $id_genre = $response->json('id');
        $genre = Genre::find($id_genre);

        $response
            ->assertStatus(200)
            ->assertJson($genre->toArray())
            ->assertJsonFragment([
                'description' => 'teste',
                'is_active' => true
            ]);

        $response = $this->json(
            'PUT',
            route('genres.update', ['genre' => $genre->id]),
            [
                'name' => 'test',
                'description' => null
            ]);
        
        $response
            ->assertJsonFragment([

                'description' => null
            ]);
    }

    /** Teste para deletar generos */
    public function testDelete() {
        // Test: Deletando um genero inexistente.
        $response = $this->json(
            'DELETE',
            route('genres.destroy', ['genre' => 100]));

        $response
            ->assertStatus(404);
        
        // Test: Criando e deletando um genero
        $genre = factory(Genre::class)->create([
            'description' => 'Teste de descrição',
            'is_active' => false
        ]);

        $response = $this->json(
            'DELETE',
            route('genres.destroy', ['genre' => $genre->id]));

        $response
            ->assertStatus(204);
    }

    protected function assertInvalidationRequired(TestResponse $response) {
        $this->asserInvalidationFields($response, ['name'], 'required');

        $response->assertJsonMissingValidationErrors(['is_active']);
    }

    protected function assertInvalidationMax(TestResponse $response) {
        $this->asserInvalidationFields($response, ['name'], 'max.string', ['max' => 255]);
    }

    protected function assertInvalidationBoolean(TestResponse $response) {
        $this->asserInvalidationFields($response, ['is_active'], 'boolean');
    }
}