<?php

namespace YOOtheme\Theme\Joomla\Listener;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Document\Document;
use Joomla\CMS\Document\HtmlDocument;
use Joomla\CMS\Event\Module\AfterCleanModuleListEvent;
use Joomla\CMS\Helper\ModuleHelper;
use YOOtheme\Config;
use YOOtheme\View;

class LoadModules
{
    public View $view;
    public Config $config;
    public ?Document $document;

    public function __construct(Config $config, View $view, ?Document $document)
    {
        $this->view = $view;
        $this->config = $config;
        $this->document = $document;
    }

    /**
     * @param AfterCleanModuleListEvent $event
     */
    public function handle($event): void
    {
        $modules = $event->getModules();

        if (
            $this->config->get('app.isAdmin') ||
            !$this->config->get('theme.active') ||
            !$this->document instanceof HtmlDocument
        ) {
            return;
        }

        $this->view['sections']->add(
            'breadcrumbs',
            fn() => ModuleHelper::renderModule(
                $this->createModule([
                    'module' => 'mod_breadcrumbs',
                    'params' => [
                        'showLast' => $this->config->get('~theme.site.breadcrumbs_show_current'),
                        'showHome' => $this->config->get('~theme.site.breadcrumbs_show_home'),
                        'homeText' => $this->config->get('~theme.site.breadcrumbs_home_text'),
                    ],
                ]),
            ),
        );

        // Logo Module
        foreach (['logo', 'logo-mobile', 'dialog', 'dialog-mobile'] as $position) {
            if (
                $content = trim(
                    $this->view->render('~theme/templates/header-logo', ['position' => $position]),
                )
            ) {
                $module = $this->createModule([
                    'module' => 'mod_custom',
                    'position' => $position,
                    'content' => $content,
                    'type' => 'logo',
                    'params' => ['layout' => 'blank'],
                ]);
                array_unshift($modules, $module);
            }
        }

        // Search Module
        foreach (['~theme.header.search', '~theme.mobile.header.search'] as $key) {
            if ($position = $this->config->get($key)) {
                $position = explode(':', $position, 2);

                $module = $this->createModule([
                    'module' => 'mod_finder',
                    'position' => $position[0],
                    'params' => [
                        'show_autosuggest' => ComponentHelper::getParams('com_finder')->get(
                            'show_autosuggest',
                            1,
                        ),
                    ],
                ]);

                $position[1] == 'start'
                    ? array_unshift($modules, $module)
                    : array_push($modules, $module);
            }
        }

        // Social Module
        foreach (['~theme.header.social', '~theme.mobile.header.social'] as $key) {
            if (
                $this->config->get($key) &&
                ($content = trim(
                    $this->view->render('~theme/templates/socials', [
                        'position' => ($position = explode(':', $this->config->get($key), 2))[0],
                    ]),
                ))
            ) {
                $module = $this->createModule([
                    'module' => 'mod_custom',
                    'position' => $position[0],
                    'content' => $content,
                    'params' => ['layout' => 'blank'],
                ]);

                $position[1] == 'start'
                    ? array_unshift($modules, $module)
                    : array_push($modules, $module);
            }
        }

        // Dialog Toggle Module
        foreach (['~theme.dialog.toggle', '~theme.mobile.dialog.toggle'] as $key) {
            if (
                $this->config->get($key) &&
                ($content = trim(
                    $this->view->render('~theme/templates/header-dialog', [
                        'position' => ($position = explode(':', $this->config->get($key), 2))[0],
                    ]),
                ))
            ) {
                $module = $this->createModule([
                    'module' => 'mod_custom',
                    'position' => $position[0],
                    'content' => $content,
                    'type' => 'dialog-toggle',
                    'params' => ['layout' => 'blank'],
                ]);

                $position[1] == 'start'
                    ? array_unshift($modules, $module)
                    : array_push($modules, $module);
            }
        }

        // Split Header Position
        if ($this->config->get('~theme.header.layout') === 'stacked-center-c') {
            $headerModules = $this->filterModules($modules, 'header');

            // Split Auto
            $index =
                $this->config->get('~theme.header.split_index') ?: ceil(count($headerModules) / 2);

            foreach (array_slice($headerModules, $index) as $module) {
                $module->position .= '-split';
            }
        }

        // Push Navbar Position
        if (
            $this->config->get('~theme.header.layout') === 'stacked-left' &&
            ($index = $this->config->get('~theme.header.push_index'))
        ) {
            $navbarModules = $this->filterModules($modules, 'navbar');

            foreach (array_slice($navbarModules, $index) as $module) {
                $module->position .= '-push';
            }
        }

        // Push Dialog Positions
        foreach (
            [
                'dialog' => '~theme.dialog.push_index',
                'dialog-mobile' => '~theme.mobile.dialog.push_index',
            ]
            as $key => $value
        ) {
            if ($index = $this->config->get($value)) {
                $dialogModules = $this->filterModules($modules, $key);

                foreach (array_slice($dialogModules, $index) as $module) {
                    $module->position .= '-push';
                }
            }
        }

        $temp = $this->config->get('req.customizer.module');

        // Module field defaults (Template Tab in Module edit view)
        $defaults = array_map(
            fn($field) => $field['default'] ?? '',
            $this->config->loadFile(__DIR__ . '/../../config/modules.php')['fields'],
        );

        foreach ($modules as $module) {
            if (empty($module->type)) {
                $module->type = str_replace('mod_', '', $module->module);
            }

            $module->attrs = ['id' => "module-{$module->id}", 'class' => []];

            // Replace module content with temporary customizer module content
            if ($temp && $temp['id'] == $module->id && $module->type === 'yootheme_builder') {
                $module->content = $temp['content'];
            }

            $this->config->update("~theme.modules.{$module->id}", function ($values) use (
                $temp,
                $module,
                $defaults
            ) {
                $params = json_decode($module->params);

                // Replace module config with temporary customizer module config
                $config =
                    isset($temp['yoo_config']) && $temp['id'] == $module->id
                        ? $temp['yoo_config']
                        : $params->yoo_config ?? ($params->config ?? '{}');

                return [
                    'showtitle' => $module->showtitle,
                    'class' => [$params->moduleclass_sfx ?? ''],
                    'title_tag' => $params->header_tag ?? 'h3',
                    'title_class' => $params->header_class ?? '',
                    'is_list' => in_array($module->type, [
                        'articles_archive',
                        'articles_categories',
                        'articles_category',
                        'articles_latest',
                        'articles_popular',
                        'tags_popular',
                        'tags_similar',
                    ]),
                ] +
                    json_decode($config, true) +
                    $defaults +
                    ($values ?: []);
            });
        }

        $event->setArgument('modules', $modules);
    }

    /**
     * @param array<string, mixed> $module
     */
    protected function createModule(array $module): object
    {
        static $id = 0;

        $module = (object) array_merge(
            [
                'id' => 'tm-' . ++$id,
                'name' => "tm-{$id}", // Joomla\CMS\Helper\ModuleHelper::getModule() requires 'name'
                'title' => '',
                'showtitle' => 0,
                'position' => '',
                'params' => '{}',
            ],
            $module,
        );

        if (is_array($module->params)) {
            $module->params = json_encode($module->params);
        }

        return $module;
    }

    /**
     * @param list<mixed> $modules
     *
     * @return list<mixed>
     */
    protected function filterModules(array $modules, string $position): array
    {
        return array_filter($modules, fn($module) => $module->position === $position);
    }
}
