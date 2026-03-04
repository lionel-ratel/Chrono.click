<?php

namespace YOOtheme\Builder\Joomla\Source;

use Joomla\CMS\Event\Content\BeforeDisplayEvent;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\TagsHelper;
use Joomla\CMS\Layout\FileLayout;
use Joomla\Plugin\Content\PageNavigation\Extension\PageNavigation;
use Joomla\Registry\Registry;
use stdClass;
use YOOtheme\Builder\DateHelper;
use YOOtheme\Builder\Joomla\Source\Model\ArticlesModel;
use function YOOtheme\app;

/**
 * @phpstan-type Article object{
 * id: int,
 * asset_id: int,
 * title: string,
 * alias: string,
 * introtext: string,
 * fulltext: string,
 * state: int,
 * catid: int,
 * created: string,
 * created_by: int,
 * created_by_alias: string,
 * modified: string,
 * modified_by: int,
 * modified_by_name: string,
 * checked_out: ?bool,
 * checked_out_time: ?string,
 * publish_up: string,
 * publish_down: ?string,
 * images: string,
 * urls: string,
 * attribs: string,
 * version: int,
 * ordering: ?int,
 * metakey: string,
 * metadesc: string,
 * access: int,
 * hits: int,
 * metadata: Registry,
 * featured: int,
 * language: string,
 * featured_up: mixed,
 * featured_down: mixed,
 * category_title: string,
 * category_alias: string,
 * category_access: int,
 * category_language: string,
 * published: int|bool,
 * parents_published: int,
 * lft: int,
 * author:  string,
 * author_email: string,
 * parent_title: string,
 * parent_id: int,
 * parent_route: string,
 * parent_alias: string,
 * parent_language: string,
 * alternative_readmore: mixed,
 * layout: mixed,
 * rating: ?int,
 * rating_count: ?int,
 * params: Registry,
 * displayDate: string,
 * tagLayout: FileLayout,
 * slug: string|int,
 * readmore_link: string,
 * text: string,
 * tags: TagsHelper,
 * jfields: list<stdClass>,
 * event: stdClass,
 * prev: string,
 * next: string,
 * prev_label: string,
 * next_label: string,
 * pagination: string,
 * paginationposition: string,
 * paginationrelative: int,
 * pageclass_sfx: string
 * } & stdClass
 */
class ArticleHelper
{
    /**
     * Gets the articles.
     *
     * @param int|int[] $ids
     * @param array<string, mixed> $args
     *
     * @return list<Article>
     */
    public static function get($ids, array $args = []): array
    {
        return $ids ? static::query(['article' => (array) $ids] + $args) : [];
    }

    /**
     * Query articles.
     *
     * @param array<string, mixed> $args
     *
     * @return list<Article>
     */
    public static function query(array $args = []): array
    {
        if (!empty($args['date_column'])) {
            $timezone = Factory::getApplication()->getIdentity()->getTimezone();
            $args = DateHelper::parseStartEndArguments($args, $timezone);
        }

        $items = app(ArticlesModel::class)->getItems($args);

        // add params, see Joomla\Component\Content\Site\Model\ArticlesModel::getItems()
        foreach ($items as $item) {
            $item->params = new Registry($item->attribs);
            $item->params->set('access-view', true);
        }

        return $items;
    }

    /**
     * @param Article $article
     */
    public static function applyPageNavigation(object $article): ?bool
    {
        if (empty($article->pagination)) {
            /** @var PageNavigation $plugin */
            $plugin = Factory::getApplication()->bootPlugin('pagenavigation', 'content');

            $plugin->params = new Registry(['display' => 0]);

            $params = clone $article->params;
            $params->set('show_item_navigation', true);

            $plugin->onContentBeforeDisplay(
                new BeforeDisplayEvent('onContentBeforeDisplay', [
                    'context' => 'com_content.article',
                    'subject' => $article,
                    'params' => $params,
                ]),
            );
        }

        return !empty($article->prev) || !empty($article->next);
    }
}
