<?php

namespace YooSeblod\MSeblod;

/**
 * Custom Type
 *
 * @see https://yootheme.com/support/yootheme-pro/joomla/developers-sources#add-custom-sources
 */
class CckSeblodFieldType
{
    public static function config()
    {
        return [
            'fields' => \YooSeblod\Integration\YooLayout::getSeblodFields(),
            'metadata' => [
                'type' => true,
                'label' => 'Field',
            ],
        ];
    }

    public static function resolve($obj, $args, $context, $info)
    {
        // Query the data …
        return $obj->my_field;
    }
}
