<?php

namespace YOOtheme\Builder\Joomla\Source\Type;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Menu\MenuItem;
use Joomla\CMS\Tree\NodeInterface;
use Joomla\CMS\User\User;
use YOOtheme\Builder\Source;
use YOOtheme\Config;
use function YOOtheme\app;
use function YOOtheme\trans;

/**
 * @phpstan-import-type ObjectConfig from Source
 */
class MenuItemType
{
    /**
     * @return ObjectConfig
     */
    public static function config(): array
    {
        return [
            'fields' => [
                'title' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Title'),
                        'filters' => ['limit', 'preserve'],
                    ],
                ],

                'image' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Image'),
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::data',
                    ],
                ],

                'icon' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Icon'),
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::data',
                    ],
                ],

                'subtitle' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Subtitle'),
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::data',
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

                'active' => [
                    'type' => 'Boolean',
                    'metadata' => [
                        'label' => trans('Active'),
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::active',
                    ],
                ],

                'type' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Type'),
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::type',
                    ],
                ],

                'parent' => [
                    'type' => 'MenuItem',
                    'metadata' => [
                        'label' => trans('Parent Menu Item'),
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::parent',
                    ],
                ],

                'children' => [
                    'type' => [
                        'listOf' => 'MenuItem',
                    ],
                    'metadata' => [
                        'label' => trans('Child Menu Items'),
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::children',
                    ],
                ],

                'alias' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Alias'),
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

    public static function link(MenuItem $item): string
    {
        $link = $item->link;

        if ($item->type === 'alias' && str_ends_with($link, 'Itemid=')) {
            $link .= "&Itemid={$item->getParams()->get('aliasoptions')}";
        }

        if (str_starts_with($link, 'index.php?') && !str_contains($link, 'Itemid=')) {
            $link .= "&Itemid={$item->id}";
        }

        return $link;
    }

    public static function active(MenuItem $item): bool
    {
        /** @var CMSApplication $joomla */
        $joomla = Factory::getApplication();
        $active = $joomla->getMenu()->getActive();

        if (!$active) {
            return false;
        }

        $alias_id = $item->getParams()->get('aliasoptions');

        // set active state
        if ($item->id == $active->id || ($item->type == 'alias' && $alias_id == $active->id)) {
            return true;
        }

        if (in_array($item->id, $active->tree)) {
            return true;
        } elseif ($item->type == 'alias') {
            if (count($active->tree) > 0 && $alias_id == array_last($active->tree)) {
                return true;
            } elseif (in_array($alias_id, $active->tree) && !in_array($alias_id, $item->tree)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param array<string, mixed> $args
     * @param mixed $context
     * @return mixed
     */
    public static function data(MenuItem $item, array $args, $context, object $info)
    {
        $value = app(Config::class)->get("~theme.menu.items.{$item->id}.{$info->fieldName}");

        if ($info->fieldName === 'image' && empty($value)) {
            return $item->getParams()['menu_image'];
        }

        return $value;
    }

    public static function type(MenuItem $item): string
    {
        if ($item->type === 'separator') {
            return 'divider';
        }
        if ($item->type === 'heading') {
            return 'heading';
        }

        return '';
    }

    /**
     * @return ?NodeInterface
     */
    public static function parent(MenuItem $item)
    {
        return $item->getParent();
    }

    /**
     * @return array<NodeInterface>
     */
    public static function children(MenuItem $item)
    {
        /** @var array<MenuItem> $children */
        $children = $item->getChildren();
        $groups = app(User::class)->getAuthorisedViewLevels();
        $language = Multilanguage::isEnabled()
            ? [Factory::getApplication()->getLanguage()->getTag(), '*']
            : false;

        return array_filter(
            $children,
            fn($child) => $child->getParams()->get('menu_show', true) &&
                in_array($child->access, $groups) &&
                (!$language || in_array($child->language, $language)),
        );
    }
}
