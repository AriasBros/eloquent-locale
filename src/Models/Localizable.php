<?php

namespace Locale\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Localizable
 *
 * @since 1.0.0
 * @package Locale\Models
 */
abstract class Localizable extends Model
{
    use \Locale\Traits\Localizable;

    /**
     * The attributes that are localizables.
     *
     * @since 1.0.0
     * @var array
     */
    protected $localize = [];

    /**
     * The relations to eager load on every query.
     *
     * @since 1.0.0
     * @var array
     */
    protected $with = ["locale"];

    /**
     * Indicates if the locale should be timestamped.
     *
     * @since 1.0.0
     * @var bool
     */
    protected $localeTimestamps = true;

    /**
     * Indicates that, if translation in current locale is missing, translation can be the fallback_locale of the app.
     *
     * @since 1.0.0
     * @var bool
     */
    protected $fallbackLocale = true;
}
