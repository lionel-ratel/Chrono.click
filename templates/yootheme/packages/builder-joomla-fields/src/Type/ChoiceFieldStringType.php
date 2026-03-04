<?php

namespace YOOtheme\Builder\Joomla\Fields\Type;

use Joomla\CMS\Language\Text;
use stdClass;
use YOOtheme\Builder\Source;
use function YOOtheme\trans;

/**
 * @phpstan-import-type ObjectConfig from Source
 */
class ChoiceFieldStringType
{
    /**
     * @return ObjectConfig
     */
    public static function config(): array
    {
        $field = [
            'type' => 'String',
            'args' => [
                'separator' => [
                    'type' => 'String',
                ],
            ],
            'metadata' => [
                'arguments' => [
                    'separator' => [
                        'label' => trans('Separator'),
                        'description' => trans('Set the separator between fields.'),
                        'default' => ', ',
                    ],
                ],
            ],
        ];

        return [
            'fields' => [
                'name' => array_merge_recursive($field, [
                    'metadata' => [
                        'label' => trans('Names'),
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::resolveNames',
                    ],
                ]),

                'value' => array_merge_recursive($field, [
                    'metadata' => [
                        'label' => trans('Values'),
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::resolveValues',
                    ],
                ]),
            ],
        ];
    }

    /**
     * @param array<stdClass> $item
     * @param array<string> $args
     */
    public static function resolveNames(array $item, $args): string
    {
        $args += ['separator' => ', '];

        $result = array_map(fn($item) => Text::_($item), array_column($item, 'name'));

        return join($args['separator'], $result);
    }

    /**
     * @param array<stdClass> $item
     * @param array<string> $args
     */
    public static function resolveValues(array $item, $args): string
    {
        $args += ['separator' => ', '];

        return join($args['separator'], array_column($item, 'value'));
    }
}
