<?php

namespace Locale\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;
use Locale\Models\Locale;

/**
 * Trait Localizable
 *
 * @since 1.0.0
 * @package Locale\Traits
 *
 * @method Builder belongsToMany(string $modelClass)
 *
 * @property array localize
 * @property bool locale_timestamps
 * @property bool fallback_locale
 * @property Locale locale
 * @property Collection locales
 */
trait Localizable
{
    //////////////
    // !Attributes

    /**
     * @since 1.0.0
     * @return string
     */
    public function getLocaleIdAttribute()
    {
        return $this->locale ? $this->locale->id : null;
    }

    /**
     * @since 1.0.0
     * @param string $key
     * @return bool
     */
    public function isLocalizableAttribute($key)
    {
        return array_search($key, $this->localize) !== false;
    }

    /**
     * Get an attribute from the model.
     *
     * @since 1.0.0
     * @param string $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        if ($this->isLocalizableAttribute($key)) {
            return $this->getAttributeValue($key);
        }

        /** @noinspection PhpUndefinedMethodInspection */
        return parent::getAttribute($key);
    }

    /**
     * Get an attribute from the $attributes array.
     *
     * @param string $key
     * @return mixed
     */
    protected function getAttributeFromArray($key)
    {
        if ($this->isLocalizableAttribute($key) && $this->locale) {
            return $this->locale->translation->getAttribute($key);
        }

        /** @noinspection PhpUndefinedMethodInspection */
        return parent::getAttributeFromArray($key);
    }

    /////////////////
    // !Relationships

    /**
     * @since 1.0.0
     */
    public function locale()
    {
        $locales = $this->locales();
        $canBe = [app()->getLocale()];

        if ($this->fallback_locale) {
            $canBe[] = config("app.fallback_locale");
        }

        /** @noinspection PhpUndefinedMethodInspection */
        return $locales->whereIn("id", $canBe);
    }

    /**
     * @since 1.0.0
     */
    public function locales()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $locales = $this->belongsToMany(config("locale.model"))->withPivot($this->localize)->as("translation");

        if ($this->locale_timestamps) {
            /** @noinspection PhpUndefinedMethodInspection */
            $locales = $locales->withTimestamps();
        }

        return $locales;
    }

    /**
     * Get a relationship value from a method.
     *
     * @param  string  $method
     * @return mixed
     *
     * @throws \LogicException
     */
    protected function getRelationshipFromMethod($method)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $results = parent::getRelationshipFromMethod($method);

        if ($method == "locale") {
            /** @noinspection PhpUndefinedMethodInspection */
            return $results->first();
        }

        return $results;
    }

    /**
     * Set the specific relationship in the model.
     *
     * @param  string  $relation
     * @param  mixed  $value
     * @return $this
     */
    public function setRelation($relation, $value)
    {
        if ($relation == "locale") {
            $value = $value->first();
        }

        /** @noinspection PhpUndefinedMethodInspection */
        return parent::setRelation($relation, $value);
    }
}
