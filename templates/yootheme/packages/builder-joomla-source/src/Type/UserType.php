<?php

namespace YOOtheme\Builder\Joomla\Source\Type;

use Joomla\CMS\Access\Access;
use Joomla\Component\Users\Administrator\Helper\UsersHelper;
use YOOtheme\Builder\Joomla\Source\UserHelper;
use YOOtheme\Builder\Source;
use function YOOtheme\trans;

/**
 * @phpstan-import-type ObjectConfig from Source
 */
class UserType
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
                        'filters' => ['limit', 'preserve'],
                    ],
                ],

                'username' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Username'),
                        'filters' => ['limit', 'preserve'],
                    ],
                ],

                'email' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Email'),
                        'filters' => ['limit', 'preserve'],
                    ],
                ],

                'registerDate' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Registered Date'),
                        'filters' => ['date'],
                    ],
                ],

                'lastvisitDate' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Last Visit Date'),
                        'filters' => ['date'],
                    ],
                ],

                'link' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Link'),
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::link',
                    ],
                ],

                'userGroupString' => [
                    'type' => 'String',
                    'args' => [
                        'separator' => [
                            'type' => 'String',
                        ],
                    ],
                    'metadata' => [
                        'label' => trans('User Groups'),
                        'arguments' => [
                            'separator' => [
                                'label' => trans('Separator'),
                                'description' => trans('Set the separator between user groups.'),
                                'default' => ', ',
                            ],
                        ],
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::userGroupString',
                    ],
                ],

                'id' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('ID'),
                    ],
                ],
            ],

            'metadata' => [
                'type' => true,
            ],
        ];
    }

    public static function link(object $user): ?string
    {
        return UserHelper::getContactLink($user->id);
    }

    /**
     * @param array<string, mixed> $args
     */
    public static function userGroupString(object $user, array $args): string
    {
        $result = [];
        $groups = Access::getGroupsByUser($user->id);
        foreach (UsersHelper::getGroups() as $group) {
            if (in_array($group->value, $groups)) {
                $result[] = $group->title;
            }
        }
        return implode($args['separator'] ?? ', ', $result);
    }
}
