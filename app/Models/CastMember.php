<?php

namespace App\Models;

use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\CastMember
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CastMember newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CastMember newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CastMember query()
 * @mixin \Eloquent
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CastMember onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CastMember withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CastMember withoutTrashed()
 * @property string $id
 * @property string $name
 * @property int $type
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CastMember whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CastMember whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CastMember whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CastMember whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CastMember whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CastMember whereUpdatedAt($value)
 */
class CastMember extends Model {
    use Uuid, SoftDeletes;

    const TYPE_DIRECTOR = 1;
    const TYPE_ACTOR = 2;

    protected $fillable = ['name', 'type'];
    protected $dates = ['deleted_at'];
    protected $casts = [
        'id' => 'string'
    ];
    public $incrementing = false;
}
