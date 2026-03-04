<?php

namespace YOOtheme\Builder\Joomla\Source\Type;

use YOOtheme\Builder\Source;
use function YOOtheme\trans;

/**
 * @phpstan-import-type ObjectConfig from Source
 */
class ArticleUrlsType
{
    /**
     * @return ObjectConfig
     */
    public static function config(): array
    {
        $fields = [];

        foreach (['a', 'b', 'c'] as $letter) {
            $fields["url{$letter}"] = [
                'type' => 'String',
                'metadata' => [
                    'label' => trans('Link %letter%', ['%letter%' => ucfirst($letter)]),
                ],
                'extensions' => [
                    'call' => __CLASS__ . '::resolve',
                ],
            ];

            $fields["url{$letter}text"] = [
                'type' => 'String',
                'metadata' => [
                    'label' => trans('Link %letter% Text', ['%letter%' => ucfirst($letter)]),
                    'filters' => ['limit', 'preserve'],
                ],
            ];
        }

        return [
            'fields' => $fields,
            'metadata' => [
                'type' => true,
            ],
        ];
    }

    /**
     * @param array<string, mixed> $args
     * @param mixed $context
     */
    public static function resolve(object $item, array $args, $context, object $info): string
    {
        return $item->{$info->fieldName} ?: '';
    }
}
