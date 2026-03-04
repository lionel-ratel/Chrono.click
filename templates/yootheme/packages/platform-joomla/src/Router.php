<?php

namespace YOOtheme\Joomla;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Router\SiteRouter;
use Joomla\CMS\Uri\Uri;
use YOOtheme\Config;
use YOOtheme\Url;
use function YOOtheme\app;

class Router
{
    public const ROUTE_PREFIX = 'media/yootheme/';

    /**
     * Convert a route to an internal URI.
     */
    public static function parse(SiteRouter $router, Uri $uri): void
    {
        if (!str_starts_with($path = $uri->getPath(), static::ROUTE_PREFIX)) {
            return;
        }

        $format = $uri->getVar('format');

        // `sef_suffix` removes the format suffix from the path, so we need to add it back
        if ($format && !Factory::getApplication()->getInput()->exists('format')) {
            $path .= ".{$format}";
        }

        $uri->setVar('option', 'com_ajax');
        $uri->setVar('p', substr($path, strlen(static::ROUTE_PREFIX)));
        $uri->setPath('');

        // Prevent redirect caused by Joomla's router e.g. through LanguageFilter
        \Closure::bind(fn() => ($this->tainted = false), $router, $router)();
    }

    /**
     * @param array<string, mixed> $parameters
     *
     * @return string|false
     */
    public static function generate(
        string $pattern = '',
        array $parameters = [],
        ?bool $secure = null
    ) {
        $joomla = Factory::getApplication();

        if (
            $joomla->get('sef') &&
            $joomla->get('sef_rewrite') &&
            str_starts_with($pattern, 'cache/') &&
            app(Config::class)->get('~theme.image_urls')
        ) {
            return Url::to(static::ROUTE_PREFIX . $pattern, $parameters, $secure);
        }

        if ($pattern) {
            $parameters = ['p' => $pattern] + $parameters;
        }

        return Url::to(
            Route::_('index.php?' . http_build_query(['option' => 'com_ajax']), false),
            $parameters,
            $secure,
        );
    }
}
