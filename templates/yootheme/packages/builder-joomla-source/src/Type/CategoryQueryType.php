<?php

namespace YOOtheme\Builder\Joomla\Source\Type;

use YOOtheme\Builder\Source;
use function YOOtheme\trans;

/**
 * @phpstan-import-type ObjectConfig from Source
 */
class CategoryQueryType
{
    /**
     * @var list<string>
     */
    protected static array $view = ['com_content.category'];

    /**
     * @return ObjectConfig
     */
    public static function config(): array
    {
        return [
            'fields' => [
                'category' => [
                    'type' => 'Category',
                    'metadata' => [
                        'label' => trans('Category'),
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
     * @return mixed
     */
    public static function resolve($root)
    {
        if (in_array($root['template'] ?? '', static::$view)) {
            return $root['category'];
        }

        return null;
    }
}
