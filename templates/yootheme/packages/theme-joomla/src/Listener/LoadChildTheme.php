<?php

namespace YOOtheme\Theme\Joomla\Listener;

use Joomla\CMS\Application\SiteApplication;
use YOOtheme\Config;
use YOOtheme\Event;
use YOOtheme\File;

class LoadChildTheme
{
    public Config $config;
    public ?SiteApplication $joomla;

    public function __construct(Config $config, ?SiteApplication $joomla)
    {
        $this->config = $config;
        $this->joomla = $joomla;
    }

    public function handle(): void
    {
        if (empty(($child = $this->config->get('~theme.child_theme')))) {
            return;
        }

        $rootDir = $this->config->get('theme.rootDir');
        $childDir = "{$rootDir}_{$child}";

        if (!file_exists($childDir)) {
            return;
        }

        // Simulate Joomla's child theme and allow layout and template file overrides
        if ($this->joomla) {
            $template = $this->joomla->getTemplate(true);

            // onAfterInitialiseDocument is triggered twice, if 404 is dispatched by component
            if ($template->parent) {
                return;
            }

            $template->parent = $template->template;
            $template->template = basename($childDir);
            $this->joomla->setTemplate($template);

            $child = $this->joomla->getTemplate(true);
            $child->id = $template->id;
            $child->home = $template->home;

            $this->joomla->getLanguage()->load('tpl_' . $template->parent, $rootDir);
        }

        // add childDir to config
        $this->config->set('theme.childDir', $childDir);

        // add ~theme alias resolver
        Event::on(
            'path ~theme',
            fn($path, $file) => $file && File::find($childDir . $file) ? $childDir . $file : $path,
        );
    }
}
