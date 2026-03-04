<?php

namespace YOOtheme\Builder\Joomla;

use Joomla\CMS\HTML\Helpers\Content;
use YOOtheme\Builder;
use YOOtheme\Builder\Listener\LoadGoogleMapsScript;
use YOOtheme\Builder\Listener\LoadLeafletScript;
use YOOtheme\Builder\Listener\LoadVimeoScript;
use YOOtheme\Builder\Listener\LoadYoutubeScript;
use YOOtheme\File;
use YOOtheme\View;

return [
    'routes' => [
        ['post', '/page', PageController::class . '@savePage'],
        ['get', '/builder/pages', PageController::class . '@getPages'],
        ['post', '/builder/image', [BuilderController::class, 'loadImage']],
    ],

    'actions' => [
        'onAfterInitialiseDocument' => [
            Listener\LoadSessionUser::class => '@handle',
        ],

        'onLoadTemplate' => [
            Listener\LoadSessionUser::class => ['@reset', 10],
            Listener\RenderBuilderButton::class => ['@handle', 10],
        ],

        'onContentPrepare' => [
            // Register with high priority, so other content plugins can't break the JSON
            Listener\RenderBuilderPage::class => ['@handle', 10],
        ],

        'onSchemaBeforeCompileHead' => [
            Listener\LoadSessionUser::class => [['@handle', 10], ['@reset', -10]],
        ],

        'onBeforeCompileHead' => [
            LoadLeafletScript::class => ['@body', 10],
            LoadGoogleMapsScript::class => ['@body', 10],
            LoadYoutubeScript::class => ['@body', 10],
            LoadVimeoScript::class => ['@body', 10],
        ],
    ],

    'extend' => [
        View::class => function (View $view) {
            $view->addLoader(function ($name, $parameters, callable $next) {
                $content = $next($name, $parameters);

                return empty($parameters['prefix']) || $parameters['prefix'] !== 'page'
                    ? Content::prepare($content)
                    : $content;
            }, '*/builder/elements/layout/templates/template.php');
        },

        Builder::class => function (Builder $builder, $app) {
            $elements = [
                'breadcrumbs',
                'menu',
                'module',
                'module_position',
                'search',
                'search_ordering',
            ];

            foreach ($elements as $element) {
                $builder->addType($element, __DIR__ . "/elements/{$element}/element.php");
            }

            if ($childDir = $app->config->get('theme.childDir')) {
                $files = File::glob("{$childDir}/builder/*/element.{json,php}");
                $filter = fn($file) => str_ends_with($file, '.json') ||
                    !in_array(dirname($file) . '/element.json', $files);
                $builder->addTypePath(array_filter($files, $filter));
            }
        },
    ],

    'services' => [
        Listener\LoadSessionUser::class => '',
        Listener\RenderBuilderPage::class => '',
    ],
];
