<?php

namespace Tests\Unit;

use App\Models\Genre;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use PHPUnit\Framework\TestCase;

/** Testes de Unidade */
class GenreUnitTest extends TestCase {
    private $genre;

    protected function setUp(): void {
        parent::setUp();
        $this->genre = new Genre();
    }

    public function testIfUseTraitsAttribute() {
        $traits = [
            SoftDeletes::class,
            Uuid::class
        ];
        $genreTraits = array_keys(class_uses(Genre::class));
        $this->assertEquals($traits, $genreTraits);
    }

    public function testFillableAttribute() {
        $fillable = ['name', 'description', 'is_active'];
        $this->assertEquals($fillable, $this->genre->getFillable());
    }


    public function testCastsAttribute() {
        $casts = ['id' => 'string', 'is_active' => 'boolean'];
        $this->assertEquals($casts, $this->genre->getCasts());
    }

    public function testIncrementingAttribute() {
        $this->assertFalse($this->genre->incrementing);
    }

    public function testDatesAttribute() {
        $dates = ['deleted_at', 'created_at', 'updated_at'];
        foreach ($dates as $date) {
            $this->assertContains($date, $this->genre->getDates());
        }

        $this->assertCount(count($dates), $this->genre->getDates());
    }
}
