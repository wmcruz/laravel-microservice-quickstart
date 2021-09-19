<?php

namespace Tests\Feature\Models;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GenreTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testList()
    {
        factory(Genre::class)->create();

        $genre = Genre::all();

        $this->assertCount(1, $genre);

        $genreKey = array_keys($genre->first()->getAttributes());
        $this->assertEqualsCanonicalizing([
            'id',
            'name',
            'description',
            'is_active',
            'created_at',
            'updated_at',
            'deleted_at'
        ], $genreKey);
    }

    public function testCreate() {
        $genre = Genre::create([
            'name' => 'genero1'
        ]);
        $genre->refresh();

        $this->assertEquals('genero1', $genre->name);
        $this->assertNull($genre->description);
        $this->assertTrue($genre->is_active);
        $this->assertRegExp('/^\w{8}\-\w{4}\-\w{4}\-\w{4}\-\w{12}$/',$genre->id);

        $genre = Genre::create([
            'name' => 'genero1',
            'is_active' => false
        ]);

        $this->assertFalse($genre->is_active);

        $genre = Genre::create([
            'name' => 'genero1',
            'is_active' => true
        ]);

        $this->assertTrue($genre->is_active);
    }

    public function testUpdate() {
        $genre = factory(Genre::class)->create([
            'description' => 'test_description',
            'is_active' => false
        ])->first();

        $data = [
            'name' => 'test_name_updated',
            'description' => 'test_description_updated',
            'is_active' => true
        ];

        $genre->update($data);

        foreach($data as $key => $value) {
            $this->assertEquals($value, $genre->{$key});
        }
    }

    public function testDelete() {
        $genre = factory(Genre::class)->create([
            'description' => 'test_description',
            'is_active' => false
        ]);
        $genre->delete();

        $genre_after_delete = Genre::find($genre->id);
        $genre_trash = Genre::withTrashed()->find($genre->id);
        
        $this->assertNull($genre_after_delete);
        $this->assertNotNull($genre_trash); 
        
        $genre_trash->forceDelete();
        $genre_after_force_delete = Genre::withoutTrashed()->find($genre->id);
        $this->assertNull($genre_after_force_delete);
    }
}
