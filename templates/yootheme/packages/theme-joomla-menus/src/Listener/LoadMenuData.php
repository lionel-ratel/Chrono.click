<?php

namespace YOOtheme\Theme\Joomla\Listener;

use Joomla\CMS\User\User;
use YOOtheme\Config;
use YOOtheme\Theme\Joomla\MenuConfig;

class LoadMenuData
{
    public User $user;
    public Config $config;
    public MenuConfig $menu;

    public function __construct(Config $config, MenuConfig $menu, User $user)
    {
        $this->menu = $menu;
        $this->user = $user;
        $this->config = $config;
    }

    public function handle(): void
    {
        $this->config->add('customizer', ['menu' => $this->menu->getArrayCopy()]);

        // Remove menu items that no longer exist.
        foreach (
            array_diff(
                array_keys($this->config->get('~theme.menu.items', [])),
                array_column($this->config->get('customizer.menu.items', []), 'id'),
            )
            as $id
        ) {
            $this->config->del("~theme.menu.items.{$id}");
        }

        if ($this->user->authorise('core.manage', 'com_menus')) {
            $this->config->addFile('customizer', __DIR__ . '/../../config/customizer.php');
        }
    }
}
