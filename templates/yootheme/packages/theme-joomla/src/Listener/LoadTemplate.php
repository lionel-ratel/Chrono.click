<?php

namespace YOOtheme\Theme\Joomla\Listener;

use Joomla\CMS\Event\View\DisplayEvent;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView;
use YOOtheme\Config;
use YOOtheme\Theme\Joomla\LoadTemplateEvent;
use YOOtheme\Theme\Joomla\StreamWrapper;

class LoadTemplate
{
    public Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param DisplayEvent $event
     */
    public function handle($event): void
    {
        if ($this->config->get('app.isAdmin') || !$this->config->get('theme.active')) {
            return;
        }

        $view = $event->getArgument('subject');

        if (!$view instanceof HtmlView) {
            return;
        }

        $context = $event->getArgument('extension');

        // loader callback for template event
        $loader = function ($path) use ($context, $view) {
            $event = new LoadTemplateEvent('onLoadTemplate', [
                'view' => $view,
                'context' => $context,
                'tpl' => substr(basename($path, '.php'), strlen($view->getLayout()) + 1) ?: null,
            ]);
            Factory::getApplication()->getDispatcher()->dispatch($event->getName(), $event);
            return $event->getOutput();
        };

        // register the stream wrapper
        if (!in_array('views', stream_get_wrappers())) {
            stream_wrapper_register('views', StreamWrapper::class);
        }

        // add loader using a stream reference
        $view->addTemplatePath('views://' . StreamWrapper::setObject($loader));
    }
}
