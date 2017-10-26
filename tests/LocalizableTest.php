<?php

namespace Locale\Tests;

use Locale\Models\Locale;
use Locale\Tests\Models\Model;

/**
 * Class LocalizableTest
 *
 * @since 1.0.0
 * @package Locale\Tests
 */
class LocalizableTest extends TestCase
{
    /**
     * @since 1.0.0
     * @var string
     */
    const SPANISH_LOCALE = "es";

    /**
     * @since 1.0.0
     * @var string
     */
    const ENGLISH_LOCALE = "en";

    /**
     * @since 1.0.0
     * @var string
     */
    const MODEL_NAME_SPANISH = "Nombre en español";

    /**
     * @since 1.0.0
     * @var string
     */
    const MODEL_DESCRIPTION_SPANISH = "Descripción en español";

    /**
     * @since 1.0.0
     * @var string
     */
    const MODEL_NAME_ENGLISH = "Name in english";

    /**
     * @since 1.0.0
     * @var string
     */
    const MODEL_DESCRIPTION_ENGLISH = "Description in english";

    /**
     * @since 1.0.0
     * @var string
     */
    const MODEL_COLOR = "#FF00000";

    /**
     * @since 1.0.0
     * @param array $attributes
     * @return Model
     */
    protected function createModel(array $attributes = [])
    {
        return Model::create($attributes);
    }

    /**
     * @since 1.0.0
     */
    public function testAddTranslationToTheModel()
    {
        $model = $this->createModel([
            "color" => self::MODEL_COLOR
        ]);

        $model->locales()->save(Locale::find(self::SPANISH_LOCALE), [
            "name" => self::MODEL_NAME_SPANISH,
            "description" => self::MODEL_DESCRIPTION_SPANISH
        ]);

        $this->app->setLocale(self::SPANISH_LOCALE);

        $this->assertTranslation($model->id, self::SPANISH_LOCALE, $model->name, $model->description);
    }

    /**
     * @since 1.0.0
     */
    public function testAddTranslationToTheModelWhenCreated()
    {
        $this->app->setLocale(self::SPANISH_LOCALE);

        $model = $this->createModel([
            "color" => self::MODEL_COLOR,
            "name" => self::MODEL_NAME_SPANISH,
            "description" => self::MODEL_DESCRIPTION_SPANISH
        ]);

        $this->assertTranslation($model->id, self::SPANISH_LOCALE, $model->name, $model->description);
    }

    /**
     * @since 1.0.0
     */
    public function testFallbackToDefaultTranslation()
    {
        $model = $this->createModel([
            "color" => self::MODEL_COLOR,
            "name" => self::MODEL_NAME_ENGLISH,
            "description" => self::MODEL_DESCRIPTION_ENGLISH
        ]);

        $this->app->setLocale(self::SPANISH_LOCALE);

        if ($model->usesFallbackLocale()) {
            $this->assertTranslation($model->id, self::ENGLISH_LOCALE, $model->name, $model->description);
        } else {
            $this->assertNull($model->name);
            $this->assertNull($model->description);
        }
    }

    /**
     * @since 1.0.0
     */
    public function testSetTranslationUsingSaveMethod()
    {
        $model = $this->createModel([
            "color" => self::MODEL_COLOR
        ]);

        $this->app->setLocale(self::SPANISH_LOCALE);

        $model->name = self::MODEL_NAME_SPANISH;
        $model->description = self::MODEL_DESCRIPTION_SPANISH;

        $this->assertSame(self::MODEL_NAME_SPANISH, $model->name);
        $this->assertSame(self::MODEL_DESCRIPTION_SPANISH, $model->description);

        $model->save();

        $this->assertTranslation($model->id, self::SPANISH_LOCALE, $model->name, $model->description);
    }

    /**
     * @since 1.0.0
     */
    public function testSetTranslationUsingUpdateMethod()
    {
        $model = $this->createModel([
            "color" => self::MODEL_COLOR
        ]);

        $this->app->setLocale(self::SPANISH_LOCALE);

        $model->update([
            "name" => self::MODEL_NAME_SPANISH,
            "description" => self::MODEL_DESCRIPTION_SPANISH
        ]);

        $this->assertTranslation($model->id, self::SPANISH_LOCALE, $model->name, $model->description);
    }

    /**
     * @since 1.0.0
     */
    public function testSetTranslationUsingLocaleIdAttribute()
    {
        $model = $this->createModel([
            "color" => self::MODEL_COLOR,
            "name" => self::MODEL_NAME_SPANISH,
            "description" => self::MODEL_DESCRIPTION_SPANISH,
            "locale_id" => self::SPANISH_LOCALE
        ]);

        $this->assertNull($model->name);
        $this->assertNull($model->description);

        $this->app->setLocale(self::SPANISH_LOCALE);
        $this->assertTranslation($model->id, self::SPANISH_LOCALE, $model->name, $model->description);
    }

    /**
     * @since 1.0.0
     */
    public function testSetMultipleTranslationsChangingLocaleUsingSaveMethod()
    {
        $model = $this->createModel([
            "color" => self::MODEL_COLOR,
            "name" => self::MODEL_NAME_ENGLISH,
            "description" => self::MODEL_DESCRIPTION_ENGLISH
        ]);

        $this->assertTranslation($model->id, self::ENGLISH_LOCALE, $model->name, $model->description);

        $this->app->setLocale(self::SPANISH_LOCALE);

        $model->name = self::MODEL_NAME_SPANISH;
        $model->description = self::MODEL_DESCRIPTION_SPANISH;
        $model->save();

        $this->assertTranslation($model->id, self::SPANISH_LOCALE, $model->name, $model->description);
    }

    /**
     * @since 1.0.0
     */
    public function testSetMultipleTranslationsChangingLocaleUsingUpdateMethod()
    {
        $model = $this->createModel([
            "color" => self::MODEL_COLOR,
            "name" => self::MODEL_NAME_ENGLISH,
            "description" => self::MODEL_DESCRIPTION_ENGLISH
        ]);

        $this->assertTranslation($model->id, self::ENGLISH_LOCALE, $model->name, $model->description);

        $this->app->setLocale(self::SPANISH_LOCALE);

        $model->update([
            "name" => self::MODEL_NAME_SPANISH,
            "description" => self::MODEL_DESCRIPTION_SPANISH
        ]);

        $this->assertTranslation($model->id, self::SPANISH_LOCALE, $model->name, $model->description);
    }

    /**
     * @since 1.0.0
     */
    public function testLocaleId()
    {
        $model = $this->createModel([
            "color" => self::MODEL_COLOR,
            "name" => self::MODEL_NAME_ENGLISH,
            "description" => self::MODEL_DESCRIPTION_ENGLISH
        ]);

        $this->assertSame($model->locale_id, self::ENGLISH_LOCALE);

        $this->app->setLocale(self::SPANISH_LOCALE);

        if ($model->usesFallbackLocale()) {
            $this->assertSame($model->locale_id, self::ENGLISH_LOCALE);
        } else {
            $this->assertNull($model->locale_id);
        }
    }

    /**
     * @since 1.0.0
     */
    public function testLocalesRelationship()
    {
        $model = $this->createModel([
            "color" => self::MODEL_COLOR,
            "name" => self::MODEL_NAME_ENGLISH,
            "description" => self::MODEL_DESCRIPTION_ENGLISH
        ]);

        $model->update([
            "name" => self::MODEL_NAME_SPANISH,
            "description" => self::MODEL_DESCRIPTION_SPANISH,
            "locale_id" => self::SPANISH_LOCALE
        ]);

        $this->assertTranslation($model->id, self::ENGLISH_LOCALE, $model->name, $model->description);
        $this->app->setLocale(self::SPANISH_LOCALE);
        $this->assertTranslation($model->id, self::SPANISH_LOCALE, $model->name, $model->description);
        $this->assertCount(2, $model->locales);
    }

    /**
     * @since 1.0.0
     */
    public function testLocalizableResource()
    {
        $response = $this->getJson("/model");
        $response->assertHeader("Content-Language", "en");
    }

    /**
     * @since 1.0.0
     * @param $model_id
     * @param $locale_id
     * @param $name
     * @param $description
     */
    protected function assertTranslation($model_id, $locale_id, $name, $description)
    {
        if ($locale_id == self::ENGLISH_LOCALE) {
            $expented_name = self::MODEL_NAME_ENGLISH;
            $expented_desc = self::MODEL_DESCRIPTION_ENGLISH;
        } else {
            $expented_name = self::MODEL_NAME_SPANISH;
            $expented_desc = self::MODEL_DESCRIPTION_SPANISH;
        }

        $this->assertDatabaseHas("locale_model", [
            "model_id" => $model_id,
            "locale_id" => $locale_id,
            "name" => $expented_name,
            "description" => $expented_desc
        ]);

        $this->assertSame($expented_name, $name);
        $this->assertSame($expented_desc, $description);
    }
}
