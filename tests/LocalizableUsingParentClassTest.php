<?php

namespace Locale\Tests;

use Locale\Tests\Models\Foo;

/**
 * Class LocalizableUsingParentClassTest
 *
 * @since 1.0.0
 * @package Locale\Tests
 */
class LocalizableUsingParentClassTest extends LocalizableTest
{
    /**
     * @since 1.0.0
     * @param array $attributes
     * @return Foo
     */
    protected function createModel(array $attributes = [])
    {
        return Foo::create($attributes);
    }
}
