<?php

namespace YOOtheme\Builder\Joomla\Source\Listener;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Event\Plugin\System\Schemaorg\BeforeCompileHeadEvent;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\Event\Priority;
use YOOtheme\Config;
use YOOtheme\Theme\Joomla\LoadTemplateEvent;
use YOOtheme\Theme\Joomla\ThemeLoader;

class LoadNotFound
{
    public Config $config;
    public CMSApplication $joomla;

    public function __construct(Config $config, CMSApplication $joomla)
    {
        $this->config = $config;
        $this->joomla = $joomla;
    }

    public function handle(): void
    {
        $document = $this->joomla->getDocument();

        if (!$this->config->get('theme.template')) {
            ThemeLoader::initTheme();
        }

        $loadTemplateEvent = new LoadTemplateEvent('onLoadTemplate', [
            'view' => new HtmlView([
                'name' => '404',
                'base_path' => '',
                'template_path' => '',
            ]),
            'context' => '404',
            'tpl' => null,
        ]);

        $dispatcher = $this->joomla->getDispatcher();
        $dispatcher->dispatch($loadTemplateEvent->getName(), $loadTemplateEvent);

        if ($output = $loadTemplateEvent->getOutput()) {
            $document->setBuffer($output, ['type' => 'component']);
        }

        /**
         * Force error context to avoid conflict with Joomla's content plugin.
         * The builder loads Joomla's content plugins.
         *
         * @see \Joomla\Plugin\Content\Joomla\Extension\Joomla::onSchemaBeforeCompileHead
         */
        $dispatcher->addListener(
            'onSchemaBeforeCompileHead',
            function (BeforeCompileHeadEvent $event) {
                $event->setArgument('context', "error.{$event->getContext()}");
            },
            Priority::HIGH,
        );
    }
}
