<?php

namespace Locale\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Query\Builder;

/**
 * Class Locale
 *
 * @since 1.0.0
 * @package Locale\Models
 *
 * @method static Builder create(array $attributes)
 * @method static Locale find(string $id)
 * @method static Builder whereKey(string $id)
 *
 * @property integer id
 * @property string name
 * @property Pivot translation
 */
class Locale extends Model
{
    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that aren't mass assignable.
     *
     * @since 1.0.0
     * @var array
     */
    protected $guarded = [];

    /**
     * @since 1.0.0
     * @return Locale
     */
    public static function current()
    {
        return Locale::find(app()->getLocale());
    }
}
