<?php

namespace YOOtheme\Builder\Joomla\Source;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseDriver;
use YOOtheme\Builder;
use YOOtheme\Builder\BuilderConfig;
use YOOtheme\Builder\Source\Filesystem\FileHelper;
use YOOtheme\Builder\Source\SourceTransform;
use YOOtheme\Builder\UpdateTransform;
use YOOtheme\Joomla\Media;
use YOOtheme\Path;

return [
    'config' => [
        'source' => [
            'id' => 1,
        ],

        BuilderConfig::class => __DIR__ . '/config/customizer.php',
    ],

    'routes' => [
        ['get', '/joomla/articles', [SourceController::class, 'articles']],
        ['get', '/joomla/users', [SourceController::class, 'users']],
        ['get', '/joomla/menu-items', [SourceController::class, 'menuItems']],
        ['get', '/joomla/modules', [SourceController::class, 'modules']],
    ],

    'events' => [
        'source.init' => [Listener\LoadSourceTypes::class => 'handle'],
        'builder.template' => [Listener\MatchTemplate::class => '@handle'],
        'builder.template.load' => [Listener\LoadTemplateUrl::class => '@handle'],
        BuilderConfig::class => [Listener\LoadBuilderConfig::class => '@handle'],
    ],

    'actions' => [
        'onLoad404' => [Listener\LoadNotFound::class => '@handle'],
        'onAfterInitialiseDocument' => [
            Listener\LoadSearchTemplate::class => '@afterInitialiseDocument',
        ],
        'onLoadTemplate' => [
            Listener\LoadTemplate::class => '@handle',
            Listener\LoadSearchHighlight::class => ['@handle', -10],
            Listener\ReplaceArticleEventType::class => ['handle', -10],
        ],
        'onAfterDispatch' => [Listener\LoadSearchTemplate::class => ['@afterDispatch', -10]],
    ],

    'extend' => [
        Builder::class => function (Builder $builder) {
            $builder->addType('pagination', __DIR__ . '/elements/pagination/element.php');
        },

        UpdateTransform::class => function (UpdateTransform $update) {
            $update->addGlobals(require __DIR__ . '/updates.php');
        },

        SourceTransform::class => function (SourceTransform $transform, $app) {
            $transform->addFilter('date', function ($value, $format) use ($app) {
                if (!$value) {
                    return $value;
                }

                if ($value === $app(DatabaseDriver::class)->getNullDate()) {
                    return null;
                }

                return HTMLHelper::date($value, $format ?: Text::_('DATE_FORMAT_LC3'));
            });
        },
    ],

    'services' => [
        Listener\LoadSearchTemplate::class => '',

        FileHelper::class => fn() => new FileHelper(
            array_map(fn($dir) => Path::join(JPATH_ROOT, $dir), Media::getRootPaths()),
        ),
    ],
];
