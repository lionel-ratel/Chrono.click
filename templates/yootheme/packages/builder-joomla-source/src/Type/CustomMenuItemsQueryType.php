<?php

namespace YOOtheme\Builder\Joomla\Source\Type;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Menu\MenuItem;
use YOOtheme\Builder\Source;
use YOOtheme\Event;
use function YOOtheme\trans;

/**
 * @phpstan-import-type ObjectConfig from Source
 */
class CustomMenuItemsQueryType
{
    /**
     * @return ObjectConfig
     */
    public static function config(): array
    {
        return [
            'fields' => [
                'customMenuItems' => [
                    'type' => [
                        'listOf' => 'MenuItem',
                    ],

                    'args' => [
                        'id' => [
                            'type' => 'String',
                        ],
                        'parent' => [
                            'type' => 'String',
                        ],
                        'heading' => [
                            'type' => 'String',
                        ],
                        'include_heading' => [
                            'type' => 'Boolean',
                            'defaultValue' => true,
                        ],
                        'ids' => [
                            'type' => [
                                'listOf' => 'String',
                            ],
                        ],
                    ],

                    'metadata' => [
                        'label' => trans('Custom Menu Items'),
                        'group' => trans('Custom'),
                        'fields' => [
                            'id' => [
                                'label' => trans('Menu'),
                                'type' => 'select',
                                'defaultIndex' => 0,
                                'options' => [
                                    ['evaluate' => 'yootheme.customizer.menu.menusSelect()'],
                                ],
                            ],
                            'parent' => [
                                'label' => trans('Parent Menu Item'),
                                'description' => trans(
                                    'Menu items are only loaded from the selected parent item.',
                                ),
                                'type' => 'select',
                                'defaultIndex' => 0,
                                'options' => [
                                    ['value' => '', 'text' => trans('Root')],
                                    ['evaluate' => 'yootheme.customizer.menu.itemsSelect(id)'],
                                ],
                            ],
                            'heading' => [
                                'label' => trans('Limit by Menu Heading'),
                                'type' => 'select',
                                'defaultIndex' => 0,
                                'options' => [
                                    ['value' => '', 'text' => trans('None')],
                                    [
                                        'evaluate' =>
                                            'yootheme.customizer.menu.headingItemsSelect(id, parent)',
                                    ],
                                ],
                            ],
                            'include_heading' => [
                                'description' => trans(
                                    'Only load menu items from the selected menu heading.',
                                ),
                                'type' => 'checkbox',
                                'default' => true,
                                'text' => trans('Include heading itself'),
                            ],
                            'ids' => [
                                'label' => trans('Select Manually'),
                                'description' => trans(
                                    'Select menu items manually. Use the <kbd>shift</kbd> or <kbd>ctrl/cmd</kbd> key to select multiple menu items.',
                                ),
                                'type' => 'select',
                                'options' => [
                                    ['evaluate' => 'yootheme.customizer.menu.itemsSelect(id)'],
                                ],
                                'attrs' => [
                                    'multiple' => true,
                                    'class' => 'uk-height-small',
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
     * @return list<MenuItem>
     */
    public static function resolve($root, array $args): array
    {
        /** @var CMSApplication $joomla */
        $joomla = Factory::getApplication();

        $items = Event::emit(
            'theme.menu.items|filter',
            $joomla->getMenu('site')->getItems('menutype', $args['id']),
        );

        $result = [];
        foreach ($items as $item) {
            // Pull children of heading items up to their parent.
            if (
                !empty($heading) &&
                (int) $item->parent_id === $heading->id &&
                $item->parent_id != ($args['parent'] ?? '')
            ) {
                $item = clone $item;
                $item->parent_id = $heading->parent_id;
                $item->level = $heading->level;
            }

            if ($item->type === 'heading' && $item->id != ($args['parent'] ?? '')) {
                $heading = $item;
            }

            if (!$item->getParams()->get('menu_show', true)) {
                continue;
            }

            if (!empty($args['ids'])) {
                if (in_array($item->id, $args['ids'])) {
                    $result[] = $item;
                }
            } elseif (!empty($args['heading'])) {
                if (empty($found)) {
                    if ($item->id == $args['heading']) {
                        $found = $item;
                        if (!empty($args['include_heading'])) {
                            $result[] = $item;
                        }
                    }
                    continue;
                }

                if ($item->parent_id !== $found->parent_id) {
                    continue;
                }

                if (!in_array($item->type, ['heading', 'separator'])) {
                    $result[] = $item;
                    continue;
                }

                break;
            } elseif (!empty($args['parent'])) {
                if ($item->parent_id == $args['parent']) {
                    $result[] = $item;
                }
            } elseif ($item->level == '1') {
                $result[] = $item;
            }
        }

        return $result;
    }
}
