<?php

namespace YOOtheme\Theme\Joomla;

use Joomla\CMS\Document\HtmlDocument;
use YOOtheme\View;

return [
    'routes' => [
        ['get', '/module', ModuleController::class . '@getModule'],
        ['post', '/module', ModuleController::class . '@saveModule'],
        ['get', '/modules', ModuleController::class . '@getModules'],
        ['get', '/positions', ModuleController::class . '@getPositions'],
    ],

    'events' => [
        'customizer.init' => [Listener\LoadModuleData::class => '@handle'],
    ],

    'actions' => [
        'onAfterInitialiseDocument' => [Listener\LoadModuleRenderer::class => '@handle'],
        'onContentPrepareForm' => [Listener\LoadModuleForm::class => 'handle'],
        'onAfterCleanModuleList' => [Listener\LoadModules::class => ['@handle', -10]],
    ],

    'extend' => [
        View::class => function (View $view, $app) {
            $view->addFunction(
                'countModules',
                fn(...$args) => $app(HtmlDocument::class)->countModules(...$args),
            );
        },
    ],
];
