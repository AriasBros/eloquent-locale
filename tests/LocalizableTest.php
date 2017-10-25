<?php

namespace Locale\Tests;

use Illuminate\Database\Eloquent\Model;
use Locale\Models\Locale;
use Locale\Tests\Models\Bar;
use Locale\Tests\Models\Foo;

/**
 * Class LocalizableTest
 *
 * @since 1.0.0
 * @package Locale\Tests
 */
abstract class LocalizableTest extends TestCase
{
    /**
     * @since 1.0.0
     * @var string
     */
    const SPANISH_NAME = "Nombre en español";

    /**
     * @since 1.0.0
     * @var string
     */
    const SPANISH_DESCRIPTION = "Descripción en español";

    /**
     * @since 1.0.0
     * @param array $attributes
     * @return mixed
     */
    abstract protected function createModel(array $attributes = []);

    /**
     * @since 1.0.0
     */
    public function testAddTranslationToTheModel()
    {
        /** @var Foo|Bar $model */
        $model = $this->createModel([
            "color" => "#FF00000"
        ]);

        $model->locales()->save(Locale::find("es"), [
            "name" => self::SPANISH_NAME,
            "description" => self::SPANISH_DESCRIPTION
        ]);

        $this->app->setLocale("es");

        $this->assertSame(self::SPANISH_NAME, $model->name);
        $this->assertSame(self::SPANISH_DESCRIPTION, $model->description);
    }

    /**
     * @since 1.0.0
     */
    public function testAddTranslationToTheModelWhenCreated()
    {
        $this->app->setLocale("es");

        /** @var Foo|Bar $model */
        $model = $this->createModel([
            "color" => "#FF00000",
            "name" => self::SPANISH_NAME,
            "description" => self::SPANISH_DESCRIPTION
        ]);

        $this->assertSame(self::SPANISH_NAME, $model->name);
        $this->assertSame(self::SPANISH_DESCRIPTION, $model->description);
    }

    /**
     * @since 1.0.0
     */
    public function testFallbackToDefaultTranslation()
    {
        /** @var Foo|Bar $model */
        $model = $this->createModel([
            "color" => "#FF00000",
            "name" => "Name in english",
            "description" => "Description in english"
        ]);

        $this->app->setLocale("es");

        if ($model->usesFallbackLocale()) {
            $this->assertSame("Name in english", $model->name);
            $this->assertSame("Description in english", $model->description);
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
        /** @var Foo|Bar $model */
        $model = $this->createModel([
            "color" => "#FF00000",
        ]);

        $locale = "es";
        $this->app->setLocale($locale);

        $model->name = self::SPANISH_NAME;
        $model->description = self::SPANISH_DESCRIPTION;

        $this->assertSame(self::SPANISH_NAME, $model->name);
        $this->assertSame(self::SPANISH_DESCRIPTION, $model->description);

        $model->save();
        $this->assertDatabaseHas("locale_model", [
            "model_id" => $model->id,
            "locale_id" => $locale,
            "name" => self::SPANISH_NAME,
            "description" => self::SPANISH_DESCRIPTION
        ]);
    }

    /**
     * @since 1.0.0
     */
    public function testSetTranslationUsingUpdateMethod()
    {
        /** @var Foo|Bar $model */
        $model = $this->createModel([
            "color" => "#FF00000",
        ]);

        $locale = "es";
        $this->app->setLocale($locale);

        $model->update([
            "name" => self::SPANISH_NAME,
            "description" => self::SPANISH_DESCRIPTION
        ]);

        $this->assertSame(self::SPANISH_NAME, $model->name);
        $this->assertSame(self::SPANISH_DESCRIPTION, $model->description);
        $this->assertDatabaseHas("locale_model", [
            "model_id" => $model->id,
            "locale_id" => $locale,
            "name" => self::SPANISH_NAME,
            "description" => self::SPANISH_DESCRIPTION
        ]);
    }
}
