<?php

namespace YooSeblod\MSeblod;

use YOOtheme\Builder;
use YOOtheme\Path;
use YOOtheme\Theme\Styler\StylerConfig;

// includes
include_once __DIR__ . '/src/TranslationListener.php';
include_once __DIR__ . '/src/SourceListener.php';
include_once __DIR__ . '/src/CckTypeProvider.php';
include_once __DIR__ . '/src/Type/CckSeblodFieldType.php';
include_once __DIR__ . '/src/Type/CckQueryType.php';
include_once __DIR__ . '/src/StyleListener.php';
include_once __DIR__ . '/src/SettingsListener.php';
include_once __DIR__ . '/src/AssetsListener.php';

return [

    'theme' => [
        // add theme config ...

        'styles' => [
            'components' => [
                'my-component' => Path::get('./assets/less/my-component.less'),
            ],
        ],
    ],

    'config' => [
        StylerConfig::class => __DIR__ . '/config/styler.json',
    ],

    'events' => [
        // add event handlers ...

        'source.init' => [
            SourceListener::class => ['initSource']
        ],

        StylerConfig::class => [
            StyleListener::class => 'config'
        ],

        'customizer.init' => [
            TranslationListener::class => ['initCustomizer', -10],
            SettingsListener::class => 'initCustomizer',
        ],

        'theme.head' => [
            AssetsListener::class => 'initHead',
        ],
    ],

    'extend' => [
        // extend container services ...

        Builder::class => function (Builder $builder) {
            $builder->addTypePath(Path::get('./elements/*/element.json'));
        },
    ],

];
