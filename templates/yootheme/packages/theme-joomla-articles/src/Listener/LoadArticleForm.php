<?php

namespace YOOtheme\Theme\Joomla\Listener;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Event\Model\PrepareDataEvent;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\SiteRouter;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\User\User;
use Joomla\Component\Content\Site\Helper\RouteHelper;
use Joomla\Database\DatabaseDriver;
use Joomla\Registry\Registry;
use YOOtheme\Config;
use YOOtheme\Metadata;
use YOOtheme\Url;

/**
 * @phpstan-import-type Article from \YOOtheme\Builder\Joomla\Source\ArticleHelper
 */
class LoadArticleForm
{
    public Config $config;
    public Metadata $metadata;
    public SiteRouter $router;
    public DatabaseDriver $db;
    public User $user;

    public function __construct(
        Config $config,
        Metadata $metadata,
        SiteRouter $router,
        DatabaseDriver $db,
        User $user
    ) {
        $this->config = $config;
        $this->metadata = $metadata;
        $this->router = $router;
        $this->db = $db;
        $this->user = $user;
    }

    /**
     * @param PrepareDataEvent $event
     */
    public function handle($event): void
    {
        $context = $event->getContext();
        $article = $event->getData();

        if ($context !== 'com_content.article') {
            return;
        }

        /** @var CMSApplication $joomla */
        $joomla = Factory::getApplication();
        $joomla->getDocument()->getWebAssetManager()->useScript('bootstrap.modal');

        /**
         * On error $article is an array instead of object!
         *
         * @var Article $article
         */
        $article = (object) $article;
        $template = $this->getTemplate();

        if (empty($template->id)) {
            return;
        }

        $values = [];

        if ($this->user->authorise('core.edit', 'com_templates')) {
            $values['url'] = Url::route('customizer', [
                'templateStyle' => $template->id,
                'format' => 'html',
                'site' => $this->getRoute($article),
                'return' => Uri::getInstance()->toString(['path', 'query']),
                'section' => 'builder',
            ]);
        }

        $this->metadata->set(
            'script:articles-data',
            sprintf('window.yootheme ||= {}; yootheme.articles = %s;', json_encode($values)),
        );

        $this->metadata->set('script:articles', [
            'src' => '~assets/admin/js/articles.js',
            'type' => 'module',
        ]);
    }

    /**
     * @param Article $article
     */
    public function getRoute(object $article): string
    {
        $route = RouteHelper::getArticleRoute(
            $article->id,
            $article->catid ?? 0,
            $article->language,
        );

        return ((string) $this->router->build($route)) ?: '/';
    }

    protected function getTemplate(): ?object
    {
        $this->db->setQuery(
            'SELECT id, params from #__template_styles WHERE client_id = 0 ORDER BY home DESC',
        );

        foreach ($this->db->loadObjectList() as $templ) {
            $params = new Registry($templ->params);

            if ($params->get('yootheme')) {
                return $templ;
            }
        }

        return null;
    }
}
