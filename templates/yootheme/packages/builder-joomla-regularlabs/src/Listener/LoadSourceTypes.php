<?php

namespace YOOtheme\Builder\Joomla\RegularLabs\Listener;

use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Component\Fields\Administrator\Helper\FieldsHelper;
use YOOtheme\Builder\Source;

class LoadSourceTypes
{
    /**
     * @param Source $source
     */
    public static function handle($source): void
    {
        if (!PluginHelper::isEnabled('fields', 'articles')) {
            return;
        }

        if (!class_exists(FieldsHelper::class)) {
            return;
        }

        // configure field type
        $source->objectType('ArticleFields', fn() => ReferenceBy::config());
    }
}
