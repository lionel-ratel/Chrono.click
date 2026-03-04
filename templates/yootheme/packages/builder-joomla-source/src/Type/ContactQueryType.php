<?php

namespace YOOtheme\Builder\Joomla\Source\Type;

use YOOtheme\Builder\Source;
use function YOOtheme\trans;

/**
 * @phpstan-import-type ObjectConfig from Source
 */
class ContactQueryType
{
    /**
     * @var list<string>
     */
    protected static array $view = ['com_contact.contact'];

    /**
     * @return ObjectConfig
     */
    public static function config(): array
    {
        return [
            'fields' => [
                'contact' => [
                    'type' => 'Contact',

                    'metadata' => [
                        'group' => trans('Page'),
                        'label' => trans('Contact'),
                        'view' => static::$view,
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
     * @return object|void
     */
    public static function resolve($root)
    {
        if (in_array($root['template'] ?? '', static::$view)) {
            return $root['item'];
        }
    }
}
