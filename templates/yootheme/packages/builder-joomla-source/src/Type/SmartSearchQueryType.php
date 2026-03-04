<?php

namespace YOOtheme\Builder\Joomla\Source\Type;

use YOOtheme\Builder\Source;
use function YOOtheme\trans;

/**
 * @phpstan-import-type ObjectConfig from Source
 */
class SmartSearchQueryType
{
    /**
     * @var list<string>
     */
    protected static array $view = ['com_finder.search', '_search'];

    /**
     * @return ObjectConfig
     */
    public static function config(): array
    {
        return [
            'fields' => [
                'smartSearch' => [
                    'type' => 'SmartSearch',
                    'metadata' => [
                        'label' => trans('Search'),
                        'view' => static::$view,
                        'group' => trans('Page'),
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
     * @return mixed|void
     */
    public static function resolve($root)
    {
        if (in_array($root['template'] ?? '', static::$view)) {
            return $root['search'] ?? null;
        }
    }
}
