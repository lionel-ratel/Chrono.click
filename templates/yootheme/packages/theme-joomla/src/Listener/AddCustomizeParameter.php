<?php

namespace YOOtheme\Theme\Joomla\Listener;

use YOOtheme\Config;
use YOOtheme\Http\Uri;
use function YOOtheme\app;

class AddCustomizeParameter
{
    public static Config $config;

    /**
     * @param array<string, mixed> $parameters
     */
    public static function handle(
        string $path,
        array $parameters,
        ?bool $secure,
        callable $next
    ): Uri {
        /** @var Uri $uri */
        $uri = $next($path, $parameters, $secure);

        if (str_starts_with((string) $uri->getQueryParam('p'), 'theme/')) {
            static::$config ??= app(Config::class);

            $query = $uri->getQueryParams();
            $query['templateStyle'] = static::$config->get('theme.id');

            $uri = $uri->withQueryParams($query);
        }

        return $uri;
    }
}
