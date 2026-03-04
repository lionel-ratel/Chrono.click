<?php

namespace YOOtheme\Builder\Joomla\Source\Type;

use YOOtheme\Builder\Source;
use function YOOtheme\trans;

/**
 * @phpstan-import-type ObjectConfig from Source
 */
class EventType
{
    /**
     * @return ObjectConfig
     */
    public static function config(): array
    {
        return [
            'fields' => [
                'afterDisplayTitle' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('After Display Title'),
                    ],
                    'extensions' => [
                        'call' => static::class . '::resolve',
                    ],
                ],

                'beforeDisplayContent' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Before Display Content'),
                    ],
                    'extensions' => [
                        'call' => static::class . '::resolve',
                    ],
                ],

                'afterDisplayContent' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('After Display Content'),
                    ],
                    'extensions' => [
                        'call' => static::class . '::resolve',
                    ],
                ],
            ],

            'metadata' => [
                'type' => true,
            ],
        ];
    }

    /**
     * @param array<string, mixed> $args
     * @param mixed $context
     */
    public static function resolve(object $article, array $args, $context, object $info): ?string
    {
        return $article->event->{$info->fieldName} ?? null;
    }
}
