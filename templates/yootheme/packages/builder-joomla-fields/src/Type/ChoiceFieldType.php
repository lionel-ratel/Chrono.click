<?php

namespace YOOtheme\Builder\Joomla\Fields\Type;

use Joomla\CMS\Language\Text;
use stdClass;
use YOOtheme\Builder\Source;
use function YOOtheme\trans;

/**
 * @phpstan-import-type ObjectConfig from Source
 */
class ChoiceFieldType
{
    /**
     * @return ObjectConfig
     */
    public static function config(): array
    {
        return [
            'fields' => [
                'name' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Name'),
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::name',
                    ],
                ],

                'value' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Value'),
                    ],
                ],
            ],
        ];
    }

    /**
     * @param stdClass $choice
     */
    public static function name($choice): ?string
    {
        $name = $choice->name ?? ($choice['name'] ?? null);

        return $name ? Text::_($name) : null;
    }
}
