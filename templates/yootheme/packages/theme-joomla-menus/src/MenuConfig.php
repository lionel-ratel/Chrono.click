<?php

namespace YOOtheme\Theme\Joomla;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\Helpers\Menu;
use Joomla\CMS\Menu\MenuFactoryInterface;
use Joomla\CMS\User\User;
use YOOtheme\Config;
use YOOtheme\ConfigObject;

/**
 * @phpstan-type MenuItem array{id: numeric-string, title: string, level: int, menu: string, link: string, home: bool, parent: numeric-string, type: string}
 *
 * @property list<array{id: string, name: string}> $menus
 * @property list<MenuItem> $items
 * @property array<string, string> $positions
 * @property bool  $canEdit
 * @property bool  $canCreate
 * @property bool  $canDelete
 */
class MenuConfig extends ConfigObject
{
    /**
     * Constructor.
     */
    public function __construct(Config $config, User $user)
    {
        parent::__construct([
            'menus' => $this->getMenus(),
            'items' => $this->getItems(),
            'positions' => $config->get('theme.menus'),
            'canEdit' => $user->authorise('core.edit', 'com_menus'),
            'canCreate' => $user->authorise('core.create', 'com_menus'),
            'canDelete' => $user->authorise('core.edit.state', 'com_menus'),
        ]);
    }

    /**
     * @return list<array{id: string, name: string}>
     */
    protected function getMenus(): array
    {
        return array_map(
            fn($menu) => [
                'id' => $menu->value,
                'name' => $menu->text,
            ],
            Menu::menus(),
        );
    }

    /**
     * @return list<MenuItem>
     */
    protected function getItems(): array
    {
        return array_values(
            array_map(
                fn($item) => [
                    'id' => (string) $item->id,
                    'title' => $item->title,
                    'level' => $item->level - 1,
                    'menu' => (string) $item->menutype,
                    'link' => $item->link,
                    'home' => (bool) $item->home,
                    'parent' => (string) $item->parent_id,
                    'type' => $item->type == 'separator' ? 'heading' : $item->type,
                ],
                Factory::getContainer()
                    ->get(MenuFactoryInterface::class)
                    ->createMenu('site')
                    ->getMenu(),
            ),
        );
    }
}
