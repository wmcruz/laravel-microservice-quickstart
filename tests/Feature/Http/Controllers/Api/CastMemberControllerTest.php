<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\CastMember;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

/** Teste de Integração HTTP */
class CastMemberControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves;
    private $castMember;

    protected function setUp(): void {
        parent::setUp();
        $this->castMember = factory(CastMember::class)->create([
            'type' => CastMember::TYPE_DIRECTOR
        ]);
    }

    /** Cria e realiza uma listagem do elenco */
    public function testIndexCastMember() {
        $response = $this->get(route('cast_members.index'));

        $response
            ->assertStatus(200)
            ->assertJson([$this->castMember->toArray()]);
    }

    /** Cria e pesquisa um elemento do elenco */
    public function testShowCastMember() {
        $response = $this->get(route('cast_members.show', ['cast_member' => $this->castMember->id]));

        $response
            ->assertStatus(200)
            ->assertJson($this->castMember->toArray());
    }

    /** Teste de validação de dados do elenco */
    public function testInvalidationData() {
        $data = ['name' => '', 'type' => ''];
        $this->assertInvalidationInStoreAction($data, 'required');
        $this->assertInvalidationInUpdateAction($data, 'required');

        $data = ['type' => 'b'];
        $this->assertInvalidationInStoreAction($data, 'in');
        $this->assertInvalidationInUpdateAction($data, 'in');
    }

    /** Teste de criação de itens do elenco */
    public function testStoreCastMember() {
        $data = [
            [
                'name' => 'test',
                'type' => CastMember::TYPE_ACTOR
            ],
            [
                'name' => 'test',
                'type' => CastMember::TYPE_DIRECTOR
            ]
        ];

        foreach ($data as $key => $value) {
            $response = $this->assertStore($value, $value + ['deleted_at' => null]);
            $response->assertJsonStructure([
                'created_at' => 'updated_at'
            ]);
        }
    }

    /** Teste de criação e alteração do alenco */
    public function testUpdateCastMember() {
        $data = [
            'name' => 'test',
            'type' => CastMember::TYPE_DIRECTOR
        ];
        $response = $this->assertUpdate($data, $data + ['deleted_at' => null]);
        $response->assertJsonStructure([
            'created_at' => 'updated_at'
        ]);
    }

    /** Teste para deletar um item do elenco */
    public function testDeleteCastMember() {
        $response = $this->json('DELETE', route('cast_members.destroy', ['cast_member' => 100]));
        $response->assertStatus(404);

        $response = $this->json('DELETE', route('cast_members.destroy', ['cast_member' => $this->castMember->id]));
        $response->assertStatus(204);
    }
    protected function routeStore() {
        return route('cast_members.store');
    }

    protected function routeUpdate() {
        return route('cast_members.update', ['cast_member' => $this->castMember->id]);
    }

    protected function model() {
        return CastMember::class;
    }
}
