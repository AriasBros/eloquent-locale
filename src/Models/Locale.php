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
     * The attributes that aren't mass assignable.
     *
     * @since 1.0.0
     * @var array
     */
    protected $guarded = [];
}
