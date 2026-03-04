<?php

namespace YOOtheme\Theme\Joomla\Listener;

use Joomla\CMS\Event\Model\BeforeSaveEvent;
use YOOtheme\Config;

class SaveBuilderData
{
    public Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param BeforeSaveEvent $event
     */
    public function handle($event): void
    {
        $context = $event->getContext();
        $article = $event->getItem();

        if (!in_array($context, ['com_content.form', 'com_content.article'], true)) {
            return;
        }

        $articletext = $this->config->get('req.body.jform.articletext', '');

        // use "jform.articletext" from request to keep builder data, when JText filters are active
        if (preg_match('/<!--\s{.*}\s-->\s*$/', $articletext, $matches)) {
            $article->fulltext = $matches[0];
        }
    }
}
