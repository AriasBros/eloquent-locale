<?php

namespace Locale;

use Illuminate\Support\ServiceProvider as Provider;

/**
 * Class ServiceProvider
 *
 * @since 1.0.0
 * @package Locale
 */
class ServiceProvider extends Provider
{
    /**
     * @since 1.0.0
     */
    public function boot()
    {
        // Publish a config file
        $this->publishes([
            __DIR__ . '/../config/locale.php' => config_path('locale.php'),
        ], 'config');
    }

    /**
     * @since 1.0.0
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/locale.php', 'locale');
    }
}
