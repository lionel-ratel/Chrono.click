<?php

namespace YOOtheme\Builder\Joomla\Listener;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Content\Site\Helper\RouteHelper;
use Joomla\Component\Content\Site\Model\ArticleModel;
use YOOtheme\Config;
use YOOtheme\Theme\Joomla\LoadTemplateEvent;

class RenderBuilderButton
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
        if (!$this->config->get('app.isBuilder')) {
            return;
        }

        $view = $event->getView();
        $context = $event->getContext();

        $layout = $view->getLayout();

        if ($context !== 'com_content.article' || $layout !== 'default') {
            return;
        }

        /** @var ArticleModel $model */
        $model = $view->getModel();
        /** @var \stdClass $article */
        $article = $model->getItem();
        $content = $article->text;

        if ($article->params->get('access-edit') && !$this->config->get('app.isCustomizer')) {
            $url = Route::_(
                RouteHelper::getFormRoute($article->id) .
                    '&return=' .
                    base64_encode(Uri::getInstance()),
            );
            $content .=
                "<a style=\"position: fixed!important\" class=\"uk-position-medium uk-position-bottom-right uk-position-z-index uk-button uk-button-primary\" href=\"{$url}\">" .
                Text::_('JACTION_EDIT') .
                '</a>';
        }

        $event->setOutput($content);
    }
}
