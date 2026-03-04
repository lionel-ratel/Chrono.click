<?php

namespace YOOtheme\Theme\Joomla;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Document\DocumentRenderer;
use Joomla\CMS\Event\Module\AfterRenderModulesEvent;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Layout\LayoutHelper;
use YOOtheme\Config;
use YOOtheme\View;
use function YOOtheme\app;

class ModulesRenderer extends DocumentRenderer
{
    /**
     * @param string $name
     * @param array<string, mixed> $params
     * @param string $content
     */
    public function render($name, $params = [], $content = null): string
    {
        [$config, $view, $joomla] = app(Config::class, View::class, CMSApplication::class);

        $user = $joomla->getIdentity();
        $modules = ModuleHelper::getModules($name);
        $renderer = $this->_doc->loadRenderer('module');
        $frontEdit = $joomla->isClient('site') && $joomla->get('frontediting', 1) && !$user->guest;
        $menusEdit =
            $joomla->get('frontediting', 1) == 2 && $user->authorise('core.edit', 'com_menus');

        // Reset section transparent header
        $prevSectionTransparency = $config->get('header.section.transparent');
        if ($name === 'top') {
            $config->del('header.section.transparent');
        }

        foreach ($modules as $module) {
            $moduleHtml = $renderer->render($module, $params, $content);

            $module->attrs ??= [];

            if (trim($moduleHtml) != '') {
                if (
                    $name === 'top' &&
                    ($module->type ?? '') !== 'yootheme_builder' &&
                    null === $config->get('header.section.transparent')
                ) {
                    $config->set(
                        'header.section.transparent',
                        (bool) $config('~theme.top.header_transparent'),
                    );
                }

                if (
                    $frontEdit &&
                    $user->authorise('module.edit.frontend', "com_modules.module.{$module->id}")
                ) {
                    $displayData = [
                        'moduleHtml' => &$moduleHtml,
                        'module' => $module,
                        'position' => $name,
                        'menusediting' => $menusEdit,
                    ];
                    LayoutHelper::render('joomla.edit.frontediting_modules', $displayData);
                }
            }

            $module->content = $moduleHtml;
        }

        if ($name === 'top' && null === $config->get('header.section.transparent')) {
            $config->set('header.section.transparent', $prevSectionTransparency);
        }

        $buffer = $view(
            '~theme/templates/position',
            ['name' => $name, 'items' => $modules] + $params,
        );

        // @see https://manual.joomla.org/docs/building-extensions/plugins/plugin-events/module/#onafterrendermodules
        $event = new AfterRenderModulesEvent('onAfterRenderModules', [
            'content' => &$buffer,
            'attributes' => $params,
        ]);
        $joomla->getDispatcher()->dispatch('onAfterRenderModules', $event);

        return $event->getContent();
    }
}
