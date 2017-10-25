<?php

namespace Locale\Tests;

use Locale\Tests\Models\Bar;

/**
 * Class LocalizableUsingTraitTest
 *
 * @since 1.0.0
 * @package Locale\Tests
 */
class LocalizableUsingTraitTest extends LocalizableTest
{
    /**
     * @since 1.0.0
     * @param array $attributes
     * @return Bar
     */
    protected function createModel(array $attributes = [])
    {
        return Bar::create($attributes);
    }
}
