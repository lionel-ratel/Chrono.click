<?php

namespace YOOtheme\Builder\Joomla\Source\Type;

use YOOtheme\Builder\Joomla\Source\TagHelper;
use YOOtheme\Builder\Source;
use function YOOtheme\trans;

/**
 * @phpstan-import-type ObjectConfig from Source
 */
class CustomTagQueryType
{
    /**
     * @return ObjectConfig
     */
    public static function config(): array
    {
        return [
            'fields' => [
                'customTag' => [
                    'type' => 'Tag',

                    'args' => [
                        'id' => [
                            'type' => 'String',
                        ],
                    ],

                    'metadata' => [
                        'label' => trans('Custom Tag'),
                        'group' => trans('Custom'),
                        'fields' => [
                            'id' => [
                                'label' => trans('Tag'),
                                'type' => 'select',
                                'defaultIndex' => 0,
                                'options' => [['evaluate' => 'yootheme.builder.tags']],
                            ],
                        ],
                    ],

                    'extensions' => [
                        'call' => __CLASS__ . '::resolve',
                    ],
                ],
            ],
        ];
    }

    /**
     * @param array<string, mixed> $root
     * @param array<string, mixed> $args
     * @return ?object
     */
    public static function resolve($root, array $args)
    {
        if (!empty($args['id'])) {
            return array_first(TagHelper::query(['ids' => $args['id']]));
        }

        return null;
    }
}
