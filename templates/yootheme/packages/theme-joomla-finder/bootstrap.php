<?php

namespace YOOtheme\Theme\Joomla;

return [
    'routes' => [['get', '/finder', [FinderController::class, 'index']]],

    'events' => [
        'customizer.init' => [Listener\LoadFinderData::class => '@handle'],
    ],
];
