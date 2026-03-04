<?php

namespace YOOtheme\Theme\Joomla;

use Joomla\CMS\Application\CMSApplication;
use Joomla\Database\DatabaseDriver;
use Joomla\Registry\Registry;
use YOOtheme\Arr;
use YOOtheme\Config;
use YOOtheme\Container;
use YOOtheme\Event;
use YOOtheme\Theme\Updater;
use function YOOtheme\app;

/**
 * @phpstan-type Configs list<callable|array<string, mixed>>
 */
class ThemeLoader
{
    /**
     * @var Configs
     */
    protected static array $configs = [];

    /**
     * Load theme configurations.
     *
     * @param Configs $configs
     */
    public static function load(Container $container, array $configs): void
    {
        static::$configs = array_merge(static::$configs, $configs);
    }

    /**
     * Initialize current theme.
     */
    public static function initTheme(): void
    {
        $template = static::getTemplate();

        // is template active?
        if (!empty($template->params['yootheme'])) {
            static::loadConfiguration($template);
            Event::emit('theme.init');
        }
    }

    protected static function loadConfiguration(object $template): void
    {
        /** @var Config $config */
        $config = app(Config::class);

        // get theme config
        $themeConfig = $template->params->get('config', '');
        $themeConfig = json_decode($themeConfig, true) ?: [];

        // load child theme config
        if (!empty($themeConfig['child_theme'])) {
            app()->load(
                "~/templates/{$template->template}_{$themeConfig['child_theme']}/config.php",
            );
        }

        // add configurations
        $config->add('theme', [
            'id' => $template->id,
            'active' => true,
            'default' => !empty($template->home),
            'template' => $template->template,
        ]);

        foreach (static::$configs as $conf) {
            if ($conf instanceof \Closure) {
                $conf = $conf($config, app());
            }

            $config->add('theme', (array) $conf);
        }

        // merge defaults with configuration
        $config->set(
            '~theme',
            Arr::merge(
                $config('theme.defaults', []),
                static::updateConfig($template, $themeConfig),
            ),
        );
    }

    /**
     * Gets the current template.
     */
    protected static function getTemplate(): ?object
    {
        /** @var CMSApplication $joomla */
        $joomla = app(CMSApplication::class);
        $template = $joomla->getTemplate(true);

        // get site template
        if ($joomla->isClient('administrator')) {
            $input = $joomla->getInput();
            $view = $input->getCmd('view') === 'style';
            $option = $input->getCmd('option') === 'com_templates';
            $style = $input->getInt($view && $option ? 'id' : 'templateStyle');

            /** @var DatabaseDriver $db */
            $db = app(DatabaseDriver::class);
            $query =
                'SELECT * FROM #__template_styles WHERE ' .
                ($style ? "id = {$style}" : "client_id = 0 AND home = '1'");

            if ($template = $db->setQuery($query)->loadObject()) {
                $template->params = new Registry($template->params);
            }
        }

        return $template;
    }

    /**
     * @param array<string, mixed> $themeConfig
     * @return array<string, mixed>
     */
    protected static function updateConfig(object $template, array $themeConfig): array
    {
        /** @var Updater $updater */
        $updater = app(Updater::class);
        $version = $themeConfig['version'] ?? null;

        // handle empty config
        if (empty($themeConfig)) {
            /** @var Config $config */
            $config = app(Config::class);
            $themeConfig['version'] = $config('theme.version');
        }

        $themeConfig = $updater->update($themeConfig, ['app' => app(), 'config' => $themeConfig]);

        if ($version !== $themeConfig['version']) {
            $style = (object) [
                'id' => $template->id,
                'params' => json_encode(
                    [
                        'config' => json_encode(
                            $themeConfig,
                            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
                        ),
                    ] + $template->params->toArray(),
                ),
            ];

            /** @var DatabaseDriver $db */
            $db = app(DatabaseDriver::class);
            $db->updateObject('#__template_styles', $style, 'id');
        }

        return $themeConfig;
    }
}
