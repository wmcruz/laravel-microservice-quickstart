<?php

namespace Tests\Feature\Models;

use App\Models\CastMember;
use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

/** Teste de Integração com BD */
class CastMemberTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testList() {
        factory(CastMember::class, 1)->create();

        $castMembers = CastMember::all();

        $this->assertCount(1, $castMembers);

        $castMembersKeys = array_keys($castMembers->first()->getAttributes());
        $this->assertEqualsCanonicalizing([
            'id',
            'name',
            'type',
            'created_at',
            'updated_at',
            'deleted_at'
        ], $castMembersKeys);
    }

    public function testCreate() {
        $castMember = CastMember::create([
            'name' => 'name_cast_member_test',
            'type' => CastMember::TYPE_ACTOR
        ]);
        $castMember->refresh();

        $this->assertEquals('name_cast_member_test', $castMember->name);
        $this->assertNotNull($castMember->type);
        $this->assertRegExp('/^\w{8}\-\w{4}\-\w{4}\-\w{4}\-\w{12}$/',$castMember->id);

        $castMember = CastMember::create([
            'name' => 'name_cast_member_test',
            'type' => CastMember::TYPE_ACTOR
        ]);

        $this->assertEquals(CastMember::TYPE_ACTOR, $castMember->type);

        $castMember = CastMember::create([
            'name' => 'name_cast_member_test',
            'type' => CastMember::TYPE_DIRECTOR
        ]);

        $this->assertEquals(CastMember::TYPE_DIRECTOR, $castMember->type);
    }

    public function testUpdate() {
        $castMember = factory(CastMember::class)->create([
            'name' => 'test',
            'type' => CastMember::TYPE_DIRECTOR
        ])->first();

        $data = [
            'name' => 'name_cast_member_test',
            'type' => CastMember::TYPE_ACTOR
        ];

        $castMember->update($data);

        foreach($data as $key => $value) {
            $this->assertEquals($value, $castMember->{$key});
        }
    }

    public function testDelete() {
        $castMember = factory(CastMember::class)->create([
            'name' => 'test',
            'type' => CastMember::TYPE_DIRECTOR
        ]);

        $castMember->delete();

        $cast_member_after_delete = CastMember::find($castMember->id);
        $cast_member_trash = CastMember::withTrashed()->find($castMember->id);

        $this->assertNull($cast_member_after_delete);
        $this->assertNotNull($cast_member_trash);

        $cast_member_trash->forceDelete();
        $cast_member_after_force_delete = CastMember::withoutTrashed()->find($castMember->id);
        $this->assertNull($cast_member_after_force_delete);
    }
}
