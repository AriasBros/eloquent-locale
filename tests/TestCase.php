<?php

namespace Locale\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\File;
use Locale\Models\Locale;
use Locale\ServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

/**
 * Class TestCase
 *
 * @since 1.0.0
 * @package Locale\Test
 */
abstract class TestCase extends Orchestra
{
    /**
     * @since 1.0.0
     */
    public function setUp()
    {
        parent::setUp();

        $this->setUpDatabase($this->app);
        $this->seedsDatabase($this->app);
    }

    /**
     * @since 1.0.0
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }

    /**
     * @since 1.0.0
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $tmp = $this->getTempDirectory();

        $this->initializeDirectory($tmp);

        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver'   => 'sqlite',
            'database' => "{$tmp}/database.sqlite",
            'prefix'   => '',
        ]);
    }

    /**
     * @since 1.0.0
     * @param \Illuminate\Foundation\Application $app
     */
    protected function setUpDatabase($app)
    {
        $tmp = $this->getTempDirectory();

        file_put_contents("{$tmp}/database.sqlite", null);

        $app['db']->connection()->getSchemaBuilder()->create('locales', function (Blueprint $table) {
            $table->string('id', 2)->primary();
            $table->string('name');
            $table->timestamps();
        });

        $app['db']->connection()->getSchemaBuilder()->create('foos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('color');
            $table->timestamps();
        });

        $app['db']->connection()->getSchemaBuilder()->create('foo_locale', function (Blueprint $table) {
            $table->string("locale_id", 2);
            $table->integer("foo_id")->unsigned();

            $table->string('name');
            $table->string('description');
            $table->timestamps();

            $table->unique(["locale_id", "foo_id"], "unique_locale_foo");
            $table->foreign('foo_id')->references('id')->on('foos');

        });
    }

    /**
     * @since 1.0.0
     * @param \Illuminate\Foundation\Application $app
     */
    protected function seedsDatabase($app)
    {
        Locale::create(["id" => "en", "name" => "English"]);
        Locale::create(["id" => "es", "name" => "Español"]);
    }

    /**
     * @since 1.0.0
     * @param string $directory
     */
    protected function initializeDirectory($directory)
    {
        if (File::isDirectory($directory)) {
            File::deleteDirectory($directory);
        }

        File::makeDirectory($directory);
    }

    /**
     * @since 1.0.0
     * @return string
     */
    public function getTempDirectory()
    {
        return __DIR__ . '/temp';
    }
}
