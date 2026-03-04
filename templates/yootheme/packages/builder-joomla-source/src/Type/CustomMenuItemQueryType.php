<?php

namespace YOOtheme\Builder\Joomla\Source\Type;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Menu\MenuItem;
use Joomla\CMS\User\User;
use YOOtheme\Builder\Source;
use function YOOtheme\app;
use function YOOtheme\trans;

/**
 * @phpstan-import-type ObjectConfig from Source
 */
class CustomMenuItemQueryType
{
    /**
     * @return ObjectConfig
     */
    public static function config(): array
    {
        return [
            'fields' => [
                'customMenuItem' => [
                    'type' => 'MenuItem',

                    'args' => [
                        'menu' => [
                            'type' => 'String',
                        ],
                        'id' => [
                            'type' => 'String',
                        ],
                    ],

                    'metadata' => [
                        'label' => trans('Custom Menu Item'),
                        'group' => trans('Custom'),
                        'fields' => [
                            'menu' => [
                                'label' => trans('Menu'),
                                'type' => 'select',
                                'defaultIndex' => 0,
                                'options' => [
                                    ['evaluate' => 'yootheme.customizer.menu.menusSelect()'],
                                ],
                            ],
                            'id' => [
                                'label' => trans('Menu Item'),
                                'description' => trans('Select menu item.'),
                                'type' => 'select',
                                'defaultIndex' => 0,
                                'options' => [
                                    ['evaluate' => 'yootheme.customizer.menu.itemsSelect(menu)'],
                                ],
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
     */
    public static function resolve($root, array $args): ?MenuItem
    {
        /** @var CMSApplication $joomla */
        $joomla = Factory::getApplication();
        $item = $joomla->getMenu('site')->getItem($args['id'] ?? 0);

        return $item &&
            in_array($item->access, app(User::class)->getAuthorisedViewLevels()) &&
            (!Multilanguage::isEnabled() ||
                in_array($item->language, [
                    Factory::getApplication()->getLanguage()->getTag(),
                    '*',
                ]))
            ? $item
            : null;
    }
}
