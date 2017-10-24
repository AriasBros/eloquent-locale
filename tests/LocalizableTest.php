<?php

namespace Locale\Tests;

use Locale\Models\Locale;
use Locale\Tests\Models\Foo;

/**
 * Class LocalizableTest
 *
 * @since 1.0.0
 * @package Locale\Tests
 */
class LocalizableTest extends TestCase
{
    public function testAddTranslationToTheModel()
    {
        /** @var Foo $model */
        $model = Foo::create([
            "color" => "#FF00000"
        ]);

        $model->locales()->save(Locale::find("es"), [
            "name" => "Nombre en español",
            "description" => "Descripción en español"
        ]);

        $this->app->setLocale("es");

        $this->assertSame("Nombre en español", $model->name);
        $this->assertSame("Descripción en español", $model->description);
    }
}
