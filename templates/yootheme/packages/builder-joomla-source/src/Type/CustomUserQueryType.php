<?php

namespace YOOtheme\Builder\Joomla\Source\Type;

use Joomla\CMS\User\User;
use YOOtheme\Builder\Joomla\Source\UserHelper;
use YOOtheme\Builder\Source;
use function YOOtheme\trans;

/**
 * @phpstan-import-type ObjectConfig from Source
 */
class CustomUserQueryType
{
    /**
     * @return ObjectConfig
     */
    public static function config(): array
    {
        return [
            'fields' => [
                'customUser' => [
                    'type' => 'User',

                    'args' => [
                        'id' => [
                            'type' => 'String',
                        ],
                    ],

                    'metadata' => [
                        'label' => trans('Custom User'),
                        'group' => trans('Custom'),
                        'fields' => [
                            'id' => [
                                'label' => trans('User'),
                                'type' => 'select-item',
                                'route' => 'joomla/users',
                                'labels' => ['type' => trans('User')],
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
     * @return ?User
     */
    public static function resolve($root, array $args)
    {
        return !empty($args['id']) ? UserHelper::get((int) $args['id']) : null;
    }
}
