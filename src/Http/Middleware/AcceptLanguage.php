<?php

namespace Locale\Http\Middleware;

use Closure;
use Locale\Models\Locale;

class AcceptLanguage
{
    /**
     * Set the locale to use in the API.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $locale = $this->parseLocale($request->header("Accept-Language"));

        if ($locale !== null && !app()->isLocale($locale)) {
            app()->setLocale($locale);
        }

        return $next($request);
    }

    /**
     * @since 1.0.0
     * @param string $locale
     * @return string
     */
    protected function parseLocale($locale)
    {
        $locale = explode(",", preg_replace("/\;q=\d.\d/", "", $locale));

        return collect($locale)->first(function ($locale) {
            return $this->isLocaleSupported($locale);
        });
    }

    /**
     * @since 1.0.0
     * @param string $locale
     * @return bool
     */
    protected function isLocaleSupported($locale)
    {
        return Locale::whereKey($locale)->count() > 0;
    }
}
