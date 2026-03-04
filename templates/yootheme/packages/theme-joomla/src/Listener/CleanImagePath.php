<?php

namespace YOOtheme\Theme\Joomla\Listener;

use Joomla\CMS\HTML\HTMLHelper;
use YOOtheme\Image;

class CleanImagePath
{
    /**
     * @param callable(string $file): ?Image $next
     */
    public static function handle(string $file, callable $next): ?Image
    {
        return $next(static::isAbsolute($file) ? $file : HTMLHelper::cleanImageURL($file)->url);
    }

    protected static function isAbsolute(?string $url): bool
    {
        return $url && preg_match('#^(/|[a-z]*:[/\\\\])#', $url);
    }
}
