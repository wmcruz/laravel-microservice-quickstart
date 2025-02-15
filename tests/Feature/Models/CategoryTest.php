<?php

namespace Tests\Feature\Models;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

/** Teste de Integração com BD */
class CategoryTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testList()
    {
        factory(Category::class, 1)->create();

        $categories = Category::all();

        $this->assertCount(1, $categories);

        $categoryKey = array_keys($categories->first()->getAttributes());
        $this->assertEqualsCanonicalizing([
            'id',
            'name',
            'description',
            'is_active',
            'created_at',
            'updated_at',
            'deleted_at'
        ], $categoryKey);
    }

    public function testCreate() {
        $category = Category::create([
            'name' => 'test1'
        ]);
        $category->refresh();

        $this->assertEquals('test1', $category->name);
        $this->assertNull($category->description);
        $this->assertTrue($category->is_active);
        $this->assertRegExp('/^\w{8}\-\w{4}\-\w{4}\-\w{4}\-\w{12}$/',$category->id);

        $category = Category::create([
            'name' => 'test1',
            'is_active' => false
        ]);

        $this->assertFalse($category->is_active);

        $category = Category::create([
            'name' => 'test1',
            'is_active' => true
        ]);

        $this->assertTrue($category->is_active);
    }

    public function testUpdate() {
        $category = factory(Category::class)->create([
            'description' => 'test_description',
            'is_active' => false
        ])->first();

        $data = [
            'name' => 'test_name_updated',
            'description' => 'test_description_updated',
            'is_active' => true
        ];

        $category->update($data);

        foreach($data as $key => $value) {
            $this->assertEquals($value, $category->{$key});
        }
    }

    public function testDelete() {
        $category = factory(Category::class)->create([
            'description' => 'test_description',
            'is_active' => false
        ]);
        $category->delete();

        $category_after_delete = Category::find($category->id);
        $category_trash = Category::withTrashed()->find($category->id);

        $this->assertNull($category_after_delete);
        $this->assertNotNull($category_trash);

        $category_trash->forceDelete();
        $category_after_force_delete = Category::withoutTrashed()->find($category->id);
        $this->assertNull($category_after_force_delete);
    }
}
