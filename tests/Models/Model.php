<?php

namespace Locale\Tests\Models;

use Locale\Models\Localizable;

/**
 * Class Model
 *
 * @since 1.0.0
 * @package Locale\Tests\Models
 *
 * @method static Model create(array $attributes)
 *
 * @property int id
 * @property int locale_id
 * @property string color
 * @property string name
 * @property string description
 */
class Model extends Localizable
{
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
