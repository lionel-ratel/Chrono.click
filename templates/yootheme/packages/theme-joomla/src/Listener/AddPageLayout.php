<?php

namespace YOOtheme\Theme\Joomla\Listener;

use Joomla\Component\Content\Site\Model\ArticleModel;
use stdClass;
use YOOtheme\Config;
use YOOtheme\Theme\Joomla\LoadTemplateEvent;

class AddPageLayout
{
    public Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param LoadTemplateEvent $event
     */
    public function handle($event): void
    {
        $view = $event->getView();
        $context = $event->getContext();

        if (in_array($context, ['com_content.category', 'com_content.featured', 'com_tags.tag'])) {
            $this->config->set('~theme.page_layout', 'blog');
        }

        if ($context === 'com_content.article' && $view->getLayout() === 'default') {
            /** @var ArticleModel $model */
            $model = $view->getModel();
            /** @var stdClass $item */
            $item = $model->getItem();

            if ($this->config->get('~theme.page_category') != $item->catid) {
                $this->config->set('~theme.page_layout', 'post');
            }
        }
    }
}
