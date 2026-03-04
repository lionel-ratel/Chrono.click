<?php

namespace YOOtheme\Theme\Joomla;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Version;
use YOOtheme\Config;
use YOOtheme\Path;
use YOOtheme\Theme\I18nConfig;
use YOOtheme\Theme\SystemCheck as BaseSystemCheck;
use YOOtheme\Theme\Updater;
use YOOtheme\View;

return [
    'theme' => function (Config $config) {
        $config->set('theme.styles.vars.@internal-joomla-version', (string) Version::MAJOR_VERSION);

        return $config->loadFile(__DIR__ . '/config/theme.php');
    },

    'config' => [
        'image' => ['cacheDir' => Path::get('~/media/yootheme/cache')],
    ],

    'routes' => [
        ['get', '/customizer', [CustomizerController::class, 'index'], ['customizer' => true]],
        ['post', '/customizer', [CustomizerController::class, 'save']],
    ],

    'events' => [
        'app.request' => [Listener\CheckUserPermission::class => '@handle'],
        'url.resolve' => [Listener\AddCustomizeParameter::class => 'handle'],

        'theme.head' => [
            Listener\LoadConsent::class => '@handle',
            Listener\LoadThemeI18n::class => '@handle',
            Listener\LoadFontAwesome::class => '@handle',
            Listener\LoadjQueryScript::class => '@handle',
        ],

        'theme.init' => [
            Listener\AddPageCategory::class => ['@handle', 10],
            Listener\LoadChildTheme::class => ['@handle', -10],
            Listener\LoadCustomizerSession::class => ['@handle', -20],
            Listener\AddSiteUrl::class => '@handle',
        ],

        'customizer.init' => [
            Listener\LoadCustomizer::class => ['@handle', 10],
            Listener\LoadChildThemeNames::class => ['@handle', 20],
        ],

        'config.save' => [
            Listener\AlterParamsColumnType::class => '@handle',
            Listener\SaveInstallerApiKey::class => '@handle',
        ],

        'image.create' => [Listener\CleanImagePath::class => ['handle', 10]],

        I18nConfig::class => [Listener\LoadThemeI18n::class => 'handleConfig'],
    ],

    'actions' => [
        'onAfterRoute' => [
            Listener\RedirectLogin::class => '@handle',
            Listener\LoadComponentTemplate::class => 'handle',
        ],

        'onAfterInitialiseDocument' => [
            ThemeLoader::class => ['initTheme', 50],
        ],

        'onBeforeDisplay' => [
            Listener\LoadTemplate::class => ['@handle', -10],
        ],

        'onLoadTemplate' => [
            Listener\AddPageLayout::class => '@handle',
            Listener\LoadConfigCache::class => ['@addFromPage', -20],
        ],

        'onAfterDispatch' => [
            Listener\LoadConfigCache::class => '@loadPage',
            Listener\SetBodyClass::class => '@handle',
        ],

        'onBeforeRender' => [
            Listener\LoadHighlightScript::class => '@beforeRender',
        ],

        'onAfterRenderModules' => [
            Listener\LoadConfigCache::class => ['@addFromModules', -20],
        ],

        'onBeforeCompileHead' => [
            Listener\LoadThemeHead::class => '@handle',
            Listener\LoadConsent::class => '@handleBody',
            Listener\LoadCustomizerData::class => '@handle',
            Listener\LoadConfigCache::class => ['@loadFromModules', 20],
        ],

        'onContentPrepareData' => [Listener\LoadCustomizerContext::class => '@handle'],
    ],

    'extend' => [
        View::class => function (View $view, $app) {
            if (!PluginHelper::isEnabled('system', 'sef')) {
                $view->addLoader([UrlLoader::class, 'resolveRelativeUrl']);
            }

            $view->addFunction('trans', [Text::class, '_']);
            $view->addFunction(
                'formatBytes',
                fn($bytes, $precision = 0) => HTMLHelper::_(
                    'number.bytes',
                    $bytes,
                    'auto',
                    $precision,
                ),
            );
        },

        Updater::class => function (Updater $updater) {
            $updater->add(__DIR__ . '/updates.php');
        },
    ],

    'services' => [
        ThemeLoader::class => '',
        BaseSystemCheck::class => SystemCheck::class,
    ],

    'loaders' => [
        'theme' => [ThemeLoader::class, 'load'],
    ],
];
