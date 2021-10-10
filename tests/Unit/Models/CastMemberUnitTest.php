<?php

namespace Tests\Unit;

use App\Models\CastMember;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use PHPUnit\Framework\TestCase;

/** Testes de Unidade */
class CastMemberUnitTest extends TestCase {
    private $castMember;

    protected function setUp(): void {
        parent::setUp();
        $this->castMember = new CastMember();
    }

    public function testIfUseTraitsAttribute() {
        $traits = [
            SoftDeletes::class,
            Uuid::class
        ];
        $castMemberTraits = array_keys(class_uses(CastMember::class));
        $this->assertEquals($traits, $castMemberTraits);
    }

    public function testFillableAttribute() {
        $fillable = ['name', 'type'];
        $this->assertEquals($fillable, $this->castMember->getFillable());
    }


    public function testCastsAttribute() {
        $casts = ['id' => 'string', 'type' => 'integer'];
        $this->assertEquals($casts, $this->castMember->getCasts());
    }

    public function testIncrementingAttribute() {
        $this->assertFalse($this->castMember->incrementing);
    }

    public function testDatesAttribute() {
        $dates = ['deleted_at', 'created_at', 'updated_at'];
        foreach ($dates as $date) {
            $this->assertContains($date, $this->castMember->getDates());
        }

        $this->assertCount(count($dates), $this->castMember->getDates());
    }
}
