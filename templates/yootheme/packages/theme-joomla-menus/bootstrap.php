<?php

namespace YOOtheme\Theme\Joomla;

use Joomla\CMS\Language\Multilanguage;
use YOOtheme\View;

return [
    'routes' => [['get', '/items', [MenuController::class, 'getItems']]],

    'events' => [
        'customizer.init' => [Listener\LoadMenuData::class => '@handle'],
        'theme.menu.items' => [Listener\LoadMenuItems::class => '@handle'],
    ],

    'actions' => [
        'onAfterCleanModuleList' => [
            Listener\LoadMenuModules::class => '@handle',
            Listener\LoadSplitNavbar::class => ['@handle', -20],
        ],
    ],

    'extend' => [
        View::class => function (View $view) {
            if (Multilanguage::isEnabled()) {
                $view->addLoader(
                    [Listener\MenuItemLoader::class, 'handle'],
                    '~theme/templates/menu/menu',
                );
            }
        },
    ],
];
