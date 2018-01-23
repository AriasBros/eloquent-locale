<?php

namespace Locale\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Events\LocaleUpdated;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Locale\Models\Locale;

/**
 * Trait Localizable
 *
 * @since 1.0.0
 * @package Locale\Traits
 *
 * @method BelongsToMany belongsToMany($modelClass, $joiningTable = null, $modelForeignKey = null, $localeForeignKey = null)
 *
 * @property array localize
 * @property Locale locale
 * @property Collection locales
 */
trait Localizable
{
    /**
     * Save the model to the database.
     *
     * @param  array  $options
     * @return bool
     */
    public function save(array $options = [])
    {
        /** @var Model $this */
        $localeForeignKey = "locale_id";
        $keys = array_merge($this->localize, [$localeForeignKey]);
        $localizeAttributes = array_only($this->attributes, $keys);
        $this->attributes = array_except($this->attributes, $keys);

        /** @noinspection PhpUndefinedMethodInspection */
        /** @noinspection PhpUndefinedClassInspection */
        $result = parent::save($options);

        if ($result && !empty($localizeAttributes)) {
            if (isset($localizeAttributes[$localeForeignKey])) {
                $locale = Locale::find($localizeAttributes[$localeForeignKey]);
                unset($localizeAttributes[$localeForeignKey]);
            } else {
                $locale = Locale::find(app()->getLocale());
            }

            if ($locale) {
                if (!$this->locales()->updateExistingPivot($locale->id, $localizeAttributes)) {
                    $this->locales()->save($locale, $localizeAttributes);
                }
            } else {
                // TODO - Throw exception?
            }
        }

        return $result;
    }

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
        /** @var Model $this */

        if ($this->isLocalizableAttribute($key)) {
            return $this->getAttributeValue($key);
        }

        /** @noinspection PhpUndefinedMethodInspection */
        /** @noinspection PhpUndefinedClassInspection */
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
        /** @noinspection PhpUndefinedMethodInspection */
        /** @noinspection PhpUndefinedClassInspection */
        $attribute = parent::getAttributeFromArray($key);

        if (!$attribute && $this->isLocalizableAttribute($key) && $this->locale) {
            return $this->locale->translation->getAttribute($key);
        }

        return $attribute;
    }

    /**
     * Determine if the model uses locale timestamps.
     *
     * @return bool
     */
    public function usesLocaleTimestamps()
    {
        return isset($this->localeTimestamps) ? $this->localeTimestamps : true;
    }

    /**
     * Determine if the model uses fallback locale.
     *
     * @return bool
     */
    public function usesFallbackLocale()
    {
        return isset($this->fallbackLocale) ? $this->fallbackLocale : true;
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

        if ($this->usesFallbackLocale()) {
            $canBe[] = config("app.fallback_locale");
        }

        $locale = $locales->whereIn("id", $canBe);

        if ($this->usesFallbackLocale() && $locale->count() === 0) {
            $locale = $this->locales()->take(1);
        }

        return $locale;
    }

    /**
     * @since 1.0.0
     * @return BelongsToMany
     */
    public function locales()
    {
        $localeTable = config("locale.model");
        $modelTable = isset($this->table) ? $this->table : null;
        $joiningTable = $this->joiningLocaleTable($localeTable, $modelTable);
        $modelForeignKey = $this->getModelForeignKey();
        $localeForeignKey = $this->getLocaleForeignKey();

        $locales = $this->belongsToMany($localeTable, $joiningTable, $modelForeignKey, $localeForeignKey)
                        ->withPivot($this->localize)
                        ->as("translation");

        if ($this->usesLocaleTimestamps()) {
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
            /** @var Collection $value */
            if ($value->count() > 1) {
                $value = $value->where("id", app()->getLocale());
            }

            $value = $value->first();

            /** @var Locale $value */
            if (!$value) {
                return $this;
            } else {
                Event::listen(LocaleUpdated::class, function ($event) use ($value) {
                    /** @var LocaleUpdated $event */
                    if ($this->needsRefreshLocaleRelation($value, $event->locale)) {
                        $this->removeLocaleRelation();
                    }
                });
            }
        }

        /** @noinspection PhpUndefinedMethodInspection */
        return parent::setRelation($relation, $value);
    }

    /**
     * Get the joining locale table name.
     *
     * @param string $locale
     * @param string $modelTable
     * @return string
     */
    public function joiningLocaleTable($locale, $modelTable = null)
    {
        $models = [
            Str::snake(class_basename($locale)),
            $modelTable ? Str::singular($modelTable) : Str::snake(class_basename($this)),
        ];

        sort($models);

        return strtolower(implode('_', $models));
    }

    /**
     * Get the foreign key name for the model.
     *
     * @return string
     */
    public function getModelForeignKey()
    {
        if (!isset($this->table)) {
            $model = Str::snake(class_basename($this));
        } else {
            $model = Str::singular($this->getTable());
        }

        return "{$model}_{$this->primaryKey}";
    }

    /**
     * Get the foreign key name for the locale model.
     *
     * @return string
     */
    public function getLocaleForeignKey()
    {
        /** @var Model $instance */
        $instance = $this->newRelatedInstance(config("locale.model"));
        return Str::singular($instance->getTable()) . "_" . $instance->primaryKey;
    }

    /**
     * @since 1.0.0
     */
    public function removeLocaleRelation()
    {
        /** @var Model $this */
        $relations = $this->getRelations();
        unset($relations["locale"]);
        $this->setRelations($relations);
    }

    /**
     * @since 1.0.0
     * @param Locale $currentLocale
     * @param string $newLocaleId
     * @return bool
     */
    public function needsRefreshLocaleRelation($currentLocale, $newLocaleId)
    {
        return $currentLocale->id !== $newLocaleId;
    }
}
