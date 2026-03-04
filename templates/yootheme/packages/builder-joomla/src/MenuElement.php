<?php

namespace YOOtheme\Builder\Joomla;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\HTML\Helpers\Menu;
use Joomla\Component\Content\Site\Helper\RouteHelper;
use Joomla\Component\Tags\Site\Helper\RouteHelper as TagsRouteHelper;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;
use YOOtheme\Arr;
use YOOtheme\Builder\Joomla\Model\TaxonomyModel;
use YOOtheme\Config;
use YOOtheme\View;
use function YOOtheme\app;

/**
 * @phpstan-type Item object{id: string, title: string, level: int, type: string, divider: bool, active: bool, url: string, children: list<object>}
 */
class MenuElement
{
    /**
     * @var list<string>
     */
    protected static array $menuProps = [
        'type',
        'divider',
        'style',
        'size',
        'image_width',
        'image_height',
        'image_svg_inline',
        'image_margin',
        'image_align',
        'text_align',
        'text_align_breakpoint',
        'text_align_fallback',
    ];

    public static function render(object $node): bool
    {
        if ($node->props['taxonomy']) {
            $node->content = static::renderTaxonomyMenu($node->props);
        } else {
            $node->content = static::renderMenuModule($node->props);
        }

        return $node->content != '';
    }

    /**
     * @param array<string, mixed> $props
     */
    protected static function renderMenuModule(array $props): string
    {
        static $id = 0;

        $moduleId = 'tm-element-menu-' . ++$id;

        app(Config::class)->set(
            "~theme.modules.{$moduleId}",
            array_combine(
                array_map(fn($prop) => "menu_{$prop}", static::$menuProps),
                array_map(fn($prop) => $props[$prop], static::$menuProps),
            ),
        );

        return ModuleHelper::renderModule(
            (object) [
                'id' => $moduleId,
                'name' => 'menu',
                'module' => 'mod_menu',
                'title' => '',
                'showtitle' => 0,
                'position' => '',
                'params' => json_encode([
                    'menutype' => $props['menu'] ?? (Menu::menus()[0]->value ?? ''),
                    'base' => $props['menu_base_item'],
                    'startLevel' => max(1, (int) $props['start_level']),
                    'endLevel' => (int) $props['end_level'],
                    'showAllChildren' => (bool) $props['show_all_children'],
                ]),
            ],
        );
    }

    /**
     * @param array<string, mixed> $props
     */
    protected static function renderTaxonomyMenu(array $props): string
    {
        $items = static::getTaxonomyMenuItems(
            $props["{$props['taxonomy']}_base_item"],
            $props['taxonomy'],
            (int) $props['start_level'],
            (int) $props['end_level'],
            (bool) $props['show_all_children'],
        );

        if (!$items) {
            return '';
        }

        // set menu config
        app(Config::class)->set(
            '~menu',
            array_intersect_key($props, array_flip(static::$menuProps)),
        );

        return app(View::class)->render('~theme/templates/menu/menu', [
            'items' => $items,
            'attrs' => [],
        ]);
    }

    /**
     * @return list<Item>
     */
    protected static function getTaxonomyMenuItems(
        ?string $base,
        string $taxonomy,
        int $startLevel = 1,
        int $endLevel = 0,
        bool $showAllChildren = false
    ): array {
        $startLevel = max(1, $startLevel);
        $endLevel = max(0, $endLevel);

        $active = Arr::wrap(
            $taxonomy === 'category'
                ? static::getCategoryFromRequest()
                : static::getTagFromRequest(),
        );

        /** @var TaxonomyModel $model */
        $model = app(TaxonomyModel::class);
        $results = $model->getItems([
            'taxonomy' => $taxonomy,
            'ids' => Arr::wrap($base ?: $active),
            'active' => $active,
            'showAllChildren' => $showAllChildren,
            'startLevel' => $startLevel,
            'endLevel' => $endLevel,
        ]);

        return static::buildTree(
            $results,
            $taxonomy === 'category'
                ? [RouteHelper::class, 'getCategoryRoute']
                : [TagsRouteHelper::class, 'getComponentTagRoute'],
        );
    }

    /**
     * @param  array<string, object> $results
     * @return list<Item>
     */
    protected static function buildTree(array $results, callable $linkFn): array
    {
        $tree = [];
        $items = [];

        foreach ($results as $item) {
            $items[$item->id] = (object) [
                'id' => "tax_{$item->id}",
                'title' => $item->title,
                'level' => $item->level,
                'type' => '',
                'divider' => false,
                'active' => (bool) $item->active,
                'url' => $linkFn($item->id, $item->language),
                'children' => [],
                'parent_id' => $item->parent_id,
            ];

            if (empty($items[$item->parent_id])) {
                $tree[] = $items[$item->id];
            } else {
                $items[$item->parent_id]->children[] = $items[$item->id];
            }
        }

        return $tree;
    }

    public static function getCategoryFromRequest(): ?int
    {
        $input = Factory::getApplication()->getInput();

        if ($input->getCmd('option') !== 'com_content') {
            return null;
        }

        $view = $input->getCmd('view');

        if (in_array($view, ['categories', 'category'])) {
            return $input->getInt('id');
        }

        if ($view === 'article') {
            $catid = $input->getInt('catid');

            if ($catid) {
                return $catid;
            }

            $id = $input->getInt('id');

            /** @var DatabaseDriver $db */
            $db = Factory::getContainer()->get(DatabaseInterface::class);
            return $db
                ->setQuery(
                    $db
                        ->createQuery()
                        ->select('catid')
                        ->from('#__content')
                        ->where('id = :id')
                        ->bind(':id', $id, ParameterType::INTEGER),
                )
                ->loadResult();
        }

        return null;
    }

    /**
     * @return list<int>
     */
    public static function getTagFromRequest(): ?array
    {
        $input = Factory::getApplication()->getInput();

        $option = $input->getCmd('option');
        $view = $input->getCmd('view');

        if ($option === 'com_tags' && in_array($view, ['tags', 'tag'])) {
            return (array) $input->getInt('id');
        }

        if ($option === 'com_content' && $view === 'article') {
            $id = $input->getInt('id');

            /** @var DatabaseDriver $db */
            $db = Factory::getContainer()->get(DatabaseInterface::class);
            return $db
                ->setQuery(
                    $db
                        ->createQuery()
                        ->select('tag_id')
                        ->from('#__contentitem_tag_map')
                        ->where('content_item_id = :id')
                        ->bind(':id', $id, ParameterType::INTEGER),
                )
                ->loadColumn();
        }

        return null;
    }
}
