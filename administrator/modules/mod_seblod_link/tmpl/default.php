<?php

use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Language\Text;
use Joomla\Component\Menus\Administrator\Helper\MenusHelper;
use Joomla\Module\Menu\Administrator\Menu\CssMenu;

defined('_JEXEC') or die();

if (in_array($module->position, ['icon', 'cpanel'])) {
    $buttons = [
        [
            'image' => 'cck-quicklink-cpanel',
            'text' => Text::_('SEBLOD'),
            'link' => "index.php?option=com_cck",
            'group' => Text::_('MOD_SEBLOD_LINKS'),
            'access' => ['core.edit', 'com_cck'],
        ],
    ];

    require ModuleHelper::getLayoutPath('mod_quickicon');
}

if ($module->position === 'menu') {
    MenusHelper::addPreset('seblod', 'SEBLOD', __DIR__ . '/../presets/seblod.xml');

    $enabled = !$app->getInput()->getBool('hidemainmenu');

    $menu = new CssMenu($app);
    Closure::bind(fn() => ($this->enabled = $enabled), $menu, $menu)();

    $root = MenusHelper::loadPreset('seblod');
    $root->level = 0;

    // Render the module layout
    include ModuleHelper::getLayoutPath('mod_menu');
}
