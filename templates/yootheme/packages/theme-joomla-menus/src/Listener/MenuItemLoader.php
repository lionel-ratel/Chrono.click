<?php

namespace YOOtheme\Theme\Joomla\Listener;

use YOOtheme\Event;

class MenuItemLoader
{
    /**
     * Add Language Switcher menu items.
     *
     * @param array<string, mixed> $parameters
     */
    public static function handle(string $name, array $parameters, callable $next): string
    {
        if (!empty($parameters['items'])) {
            $parameters['items'] = Event::emit('theme.menu.items|filter', $parameters['items']);
        }

        return $next($name, $parameters);
    }
}
