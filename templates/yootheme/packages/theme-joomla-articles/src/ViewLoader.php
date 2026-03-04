<?php

namespace YOOtheme\Theme\Joomla;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\Helpers\StringHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Content\Site\Helper\RouteHelper;
use Joomla\Registry\Registry;
use stdClass;
use YOOtheme\Config;

/**
 * @phpstan-import-type Article from \YOOtheme\Builder\Joomla\Source\ArticleHelper
 */
class ViewLoader
{
    /**
     * @param array<string, mixed> $parameters
     */
    public static function loadArticle(string $name, array $parameters, callable $next): string
    {
        /**
         * @var Config $config
         * @var Article $article
         */
        ['config' => $config, 'article' => $article] = $parameters;

        $single = $parameters['single'] ?? null;
        $params = $parameters['params'] ?? $article->params;

        if (is_array($params)) {
            $params = new Registry($params);
        }

        if (!empty($article->created_by_alias)) {
            $article->author = $article->created_by_alias;
        }

        if (isset($article->event) && $params['show_intro'] && !$single) {
            $article->event->afterDisplayTitle = '';
        }

        // Link
        $link =
            $parameters['link'] ??
            RouteHelper::getArticleRoute($article->slug, $article->catid, $article->language);

        // Permalink
        $permalink = $parameters['permalink'] ?? Route::_($link, true, 0, true);

        if ($params['access-view'] === false) {
            $link = self::getLink($link);
        }

        // Title
        $title = $params['show_title'] ? $article->title : null;

        if ($title && $params['link_titles']) {
            $title = HTMLHelper::link($link, $title, ['class' => 'uk-link-reset']);
        }

        // Image
        if (is_string($image = $parameters['image'] ?? null)) {
            $image = self::getImage($article, $link, $image, $params);
        }

        // Blog
        if (($parameters['layout'] ?? '') === 'blog') {
            $data = $config('~theme.post', []);

            // Merge blog config?
            if (!$single) {
                $data = array_merge($data, $config('~theme.blog', []));

                // Has excerpt field?
                foreach ($article->jcfields ?? [] as $field) {
                    if ($field->name === 'excerpt' && $field->rawvalue) {
                        $parameters['content'] = $field->rawvalue;
                        break;
                    }
                }
            }

            $params->loadArray($data);
        }

        return $next(
            $name,
            [
                'title' => $title,

                'link' => $link,

                'permalink' => $permalink,

                'image' => $image,

                'event' => $article->event ?? null,

                'pagination' => $article->pagination ?? null,

                'params' => $params,

                'single' => $single,

                'hits' => $params['show_hits'] ? $article->hits : null,

                'tags' =>
                    $params->get('show_tags', 1) && !empty($article->tags->itemTags)
                        ? self::getTags($article)
                        : null,

                'icons' => array_filter(
                    ($parameters['icons'] ?? []) +
                        ($params['access-edit'] && !$config('app.isCustomizer')
                            ? ['edit' => HTMLHelper::_('icon.edit', $article, $params)]
                            : []),
                ),

                'readmore' =>
                    $params['show_readmore'] &&
                    (!empty($article->readmore) ||
                        (is_numeric($length = $config('~theme.blog.content_length')) &&
                            (int) $length >= 0 &&
                            !$single))
                        ? self::getReadmore($article, $link, $params)
                        : null,

                'created' => $params['show_create_date']
                    ? static::getDate($article->created)
                    : null,

                'modified' => $params['show_modify_date']
                    ? static::getDate($article->modified)
                    : null,
            ] + $parameters,
        );
    }

    protected static function getLink(string $link): Uri
    {
        /** @var CMSApplication $joomla */
        $joomla = Factory::getApplication();

        $menu = $joomla->getMenu()->getActive();
        $login = Route::_("index.php?option=com_users&view=login&Itemid={$menu->id}", false);

        $uri = new Uri($login);
        $uri->setVar('return', base64_encode(Route::_($link, false)));

        return $uri;
    }

    /**
     * @param Article $article
     */
    protected static function getImage(
        object $article,
        string $link,
        string $type,
        Registry $params
    ): ?object {
        $images = new Registry($article->images);

        if (!$images->get("image_{$type}")) {
            return null;
        }

        $image = new stdClass();
        $image->link = $params['link_titles'] ? $link : null;
        $image->caption = $images->get("image_{$type}_caption");
        $image->attrs = [
            'src' => HTMLHelper::cleanImageURL($images->get("image_{$type}"))->url,
            'alt' => $images->get("image_{$type}_alt"),
            'title' => $image->caption,
            'class' => [],
        ];

        $image->attrs['class'][] = $images->get("float_{$type}") ?: $params["float_{$type}"];

        return $image;
    }

    /**
     * @param Article $article
     */
    protected static function getTags(object $article): string
    {
        return (new FileLayout('joomla.content.tags'))->render($article->tags->itemTags);
    }

    /**
     * @param Article $article
     */
    protected static function getReadmore(object $article, string $link, Registry $params): object
    {
        $readmore = new stdClass();
        $readmore->link = $link;

        if ($params['access-view']) {
            $attribs = new Registry($article->attribs);

            if (!($readmore->text = $attribs->get('alternative_readmore'))) {
                $readmore->text = Text::_(
                    $params['show_readmore_title']
                        ? 'COM_CONTENT_READ_MORE'
                        : 'TPL_YOOTHEME_READ_MORE',
                );
            }

            if ($params['show_readmore_title']) {
                $readmore->text .= StringHelper::truncate(
                    $article->title,
                    $params['readmore_limit'],
                );
            }
        } else {
            $readmore->text = Text::_('COM_CONTENT_REGISTER_TO_READ_MORE');
        }

        return $readmore;
    }

    protected static function getDate(?string $date): string
    {
        return sprintf(
            '<time datetime="%s">%s</time>',
            HTMLHelper::date($date, 'c'),
            HTMLHelper::date($date, Text::_('DATE_FORMAT_LC3')),
        );
    }
}
