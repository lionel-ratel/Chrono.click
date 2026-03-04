<?php

namespace YOOtheme\Builder\Joomla\Fields\Type;

use YOOtheme\Builder\Source;
use function YOOtheme\trans;

/**
 * @phpstan-import-type ObjectConfig from Source
 */
class MediaFieldType
{
    /**
     * @return ObjectConfig
     */
    public static function config(): array
    {
        return [
            'fields' => [
                'imagefile' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => '',
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::imagefile',
                    ],
                ],
                'alt_text' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Alt'),
                    ],
                ],
            ],
        ];
    }

    /**
     * @param array<string, string> $image
     */
    public static function imagefile($image): ?string
    {
        return rawurldecode($image['imagefile'] ?? '') ?: null;
    }
}
