<?php

namespace YOOtheme\Theme\Joomla\Listener;

use Joomla\CMS\Event\Model\PrepareDataEvent;
use Joomla\CMS\Uri\Uri;
use stdClass;
use YOOtheme\Config;
use YOOtheme\Url;

class LoadCustomizerContext
{
    public Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param PrepareDataEvent $event
     */
    public function handle($event): void
    {
        /** @var stdClass $data */
        $data = $event->getData();
        $context = $event->getContext();

        if ($context !== 'com_templates.style') {
            return;
        }

        $this->config->add('customizer', [
            'context' => $context,
            'url' => Url::route('customizer', [
                'templateStyle' => $data->id,
                'format' => 'html',
                'return' => Uri::getInstance()->toString(['path', 'query']),
            ]),
        ]);
    }
}
