<?php

namespace YOOtheme\Builder\Joomla\Source\Type;

use Joomla\Component\Finder\Site\Helper\RouteHelper;
use YOOtheme\Builder\Source;
use function YOOtheme\trans;

/**
 * @phpstan-import-type ObjectConfig from Source
 */
class SmartSearchType
{
    /**
     * @return ObjectConfig
     */
    public static function config(): array
    {
        return [
            'fields' => [
                'searchword' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Search Word'),
                    ],
                ],

                'total' => [
                    'type' => 'Int',
                    'metadata' => [
                        'label' => trans('Item Count'),
                    ],
                ],

                'link' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Link'),
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::link',
                    ],
                ],
            ],

            'metadata' => [
                'type' => true,
            ],
        ];
    }

    public static function link(): string
    {
        return RouteHelper::getSearchRoute();
    }
}
