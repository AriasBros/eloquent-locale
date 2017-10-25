<?php

namespace Locale\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Locale\Traits\Localizable;

/**
 * Class Bar
 *
 * @since 1.0.0
 * @package Locale\Tests\Models
 *
 * @method static Foo create(array $attributes)
 *
 * @property int id
 * @property int locale_id
 * @property string color
 * @property string name
 * @property string description
 */
class Bar extends Model
{
    use Localizable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "models";

    /**
     * The attributes that aren't mass assignable.
     *
     * @since 1.0.0
     * @var array
     */
    protected $guarded = ["id"];

    /**
     * The attributes that are localizables.
     *
     * @since 1.0.0
     * @var array
     */
    protected $localize = ["name", "description"];
}
