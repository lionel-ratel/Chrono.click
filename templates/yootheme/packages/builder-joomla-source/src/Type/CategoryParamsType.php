<?php

namespace YOOtheme\Builder\Joomla\Source\Type;

use YOOtheme\Builder\Source;
use function YOOtheme\trans;

/**
 * @phpstan-import-type ObjectConfig from Source
 */
class CategoryParamsType
{
    /**
     * @return ObjectConfig
     */
    public static function config(): array
    {
        return [
            'fields' => [
                'image' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Image'),
                    ],
                ],

                'image_alt' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Image Alt'),
                        'filters' => ['limit', 'preserve'],
                    ],
                ],
            ],
        ];
    }
}
