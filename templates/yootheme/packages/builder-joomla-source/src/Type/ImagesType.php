<?php

namespace YOOtheme\Builder\Joomla\Source\Type;

use YOOtheme\Builder\Source;
use function YOOtheme\trans;

/**
 * @phpstan-import-type ObjectConfig from Source
 */
class ImagesType
{
    /**
     * @return ObjectConfig
     */
    public static function config(): array
    {
        return [
            'fields' => [
                'image_intro' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Intro Image'),
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::image',
                    ],
                ],

                'image_intro_alt' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Intro Image Alt'),
                        'filters' => ['limit', 'preserve'],
                    ],
                ],

                'image_intro_caption' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Intro Image Caption'),
                        'filters' => ['limit', 'preserve'],
                    ],
                ],

                'image_fulltext' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Full Article Image'),
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::image',
                    ],
                ],

                'image_fulltext_alt' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Full Article Image Alt'),
                        'filters' => ['limit', 'preserve'],
                    ],
                ],

                'image_fulltext_caption' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Full Article Image Caption'),
                        'filters' => ['limit', 'preserve'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param array<string, mixed> $args
     * @param mixed $context
     * @return ?string
     */
    public static function image(object $data, array $args, $context, object $info): ?string
    {
        return $data->{$info->fieldName} ?? null;
    }
}
