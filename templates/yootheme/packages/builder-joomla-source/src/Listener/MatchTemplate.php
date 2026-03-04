<?php

namespace YOOtheme\Builder\Joomla\Source\Listener;

use Joomla\CMS\Categories\Categories;
use Joomla\CMS\Categories\CategoryNode;
use Joomla\CMS\Document\Document;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Contact\Site\Model\CategoryModel;
use Joomla\Component\Contact\Site\Model\ContactModel;
use Joomla\Component\Contact\Site\Model\FeaturedModel;
use Joomla\Component\Content\Site\Helper\RouteHelper;
use Joomla\Component\Content\Site\Model\ArticleModel;
use Joomla\Component\Finder\Site\Model\SearchModel;
use Joomla\Component\Tags\Site\Model\TagModel;
use Joomla\Component\Tags\Site\Model\TagsModel;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\DatabaseQuery;
use stdClass;
use YOOtheme\Builder\Joomla\Source\TagHelper;
use YOOtheme\Theme\Joomla\LoadTemplateEvent;

/**
 * @phpstan-import-type Template from LoadTemplate
 */
class MatchTemplate
{
    public string $language;
    protected DatabaseDriver $db;

    public function __construct(?Document $document, DatabaseDriver $db)
    {
        $this->language = $document->language ?? 'en-gb';
        $this->db = $db;
    }

    /**
     * @param LoadTemplateEvent $event
     * @return ?Template
     */
    public function handle($event): ?array
    {
        if ($event->getTpl()) {
            return null;
        }

        $view = $event->getView();
        $layout = $view->getLayout();
        $context = $event->getContext();

        if ($context === 'com_content.article' && $layout === 'default') {
            /** @var ArticleModel $model */
            $model = $view->getModel();
            /** @var stdClass $item */
            $item = $model->getItem();

            return [
                'type' => $context,
                'query' => [
                    'catid' => fn($ids, $query) => $this->matchCategory(
                        $item->catid,
                        $ids,
                        $query['include_child_categories'] ?? false,
                        'content',
                    ),
                    'tag' => fn($ids, $query) => $this->matchTag(
                        $item->tags->itemTags,
                        $ids,
                        $query['include_child_tags'] ?? false,
                    ),
                    'lang' => $this->language,
                ],
                'params' => ['item' => $item],
                'editUrl' => $item->params->get('access-edit')
                    ? Route::_(
                        RouteHelper::getFormRoute($item->id) .
                            '&return=' .
                            base64_encode(Uri::getInstance()),
                    )
                    : null,
            ];
        }

        if ($context === 'com_content.category' && $layout === 'blog') {
            /** @var CategoryModel $model */
            $model = $view->getModel();
            $category = $model->getCategory();
            $pagination = $model->getPagination();

            return [
                'type' => $context,
                'query' => [
                    'catid' => fn($ids, $query) => $this->matchCategory(
                        $category,
                        $ids,
                        $query['include_child_categories'] ?? false,
                        'content',
                    ),
                    'tag' => fn($ids, $query) => $this->matchTag(
                        TagHelper::query(['ids' => $model->getState('filter.tag', [])]),
                        $ids,
                        $query['include_child_tags'] ?? false,
                    ),
                    'pages' => $pagination->pagesCurrent === 1 ? 'first' : 'except_first',
                    'lang' => $this->language,
                ],
                'params' => [
                    'category' => $category,
                    'items' => array_merge($view->get('lead_items'), $view->get('intro_items')),
                    'pagination' => $pagination,
                ],
            ];
        }

        if ($context === 'com_content.featured') {
            /** @var FeaturedModel $model */
            $model = $view->getModel();
            $pagination = $model->getPagination();

            return [
                'type' => $context,
                'query' => [
                    'pages' => $pagination->pagesCurrent === 1 ? 'first' : 'except_first',
                    'lang' => $this->language,
                ],
                'params' => ['items' => $model->getItems(), 'pagination' => $pagination],
            ];
        }

        if ($context === 'com_tags.tag') {
            /** @var TagModel $model */
            $model = $view->getModel();
            $pagination = $model->getPagination();
            /** @var list<object> $tags */
            $tags = $model->getItem();

            return [
                'type' => $context,
                'query' => [
                    'tag' => fn($ids, $query) => $this->matchTag(
                        $tags,
                        $ids,
                        $query['include_child_tags'] ?? false,
                    ),
                    'pages' => $pagination->pagesCurrent === 1 ? 'first' : 'except_first',
                    'lang' => $this->language,
                ],
                'params' => [
                    'tags' => $tags,
                    'items' => $model->getItems(),
                    'pagination' => $pagination,
                ],
            ];
        }

        if ($context === 'com_tags.tags') {
            /** @var TagsModel $model */
            $model = $view->getModel();
            $pagination = $model->getPagination();

            return [
                'type' => $context,
                'query' => [
                    'lang' => $this->language,
                    'pages' => $pagination->pagesCurrent === 1 ? 'first' : 'except_first',
                ],
                'params' => [
                    'tags' => $model->getItems(),
                    'pagination' => $pagination,
                ],
            ];
        }

        if ($context === 'com_contact.contact') {
            /** @var ContactModel $model */
            $model = $view->getModel();
            $item = $model->getItem();

            return [
                'type' => $context,
                'query' => [
                    'catid' => fn($ids, $query) => $this->matchCategory(
                        $item->catid,
                        $ids,
                        $query['include_child_categories'] ?? false,
                        'contact',
                    ),
                    'tag' => fn($ids, $query) => $this->matchTag(
                        $item->tags->itemTags,
                        $ids,
                        $query['include_child_tags'] ?? false,
                    ),
                    'lang' => $this->language,
                ],
                'params' => ['item' => $item],
            ];
        }

        if ($context === 'com_finder.search') {
            /** @var SearchModel $model */
            $model = $view->getModel();
            $pagination = $model->getPagination();
            $input = Factory::getApplication()->getInput();

            return [
                'type' => $input->getBool('live-search') ? '_search' : $context,
                'query' => [
                    'pages' => $pagination->pagesCurrent === 1 ? 'first' : 'except_first',
                    'lang' => $this->language,
                ],
                'params' => [
                    'search' => [
                        'searchword' => $model->getQuery()->input ?: '',
                        'total' => $pagination->total,
                    ],
                    'items' => $view->get('results') ?? [],
                    'pagination' => $pagination,
                ],
            ];
        }

        if ($view->getName() === '404') {
            return [
                'type' => 'error-404',
                'query' => ['lang' => $this->language],
            ];
        }

        return null;
    }

    /**
     * @param int|CategoryNode $category
     * @param array<string> $categoryIds
     * @param string|bool $includeChildren
     */
    protected function matchCategory(
        $category,
        $categoryIds,
        $includeChildren,
        string $extension
    ): bool {
        $match = in_array(is_object($category) ? $category->id : $category, $categoryIds);

        if (!$includeChildren || ($match && $includeChildren === 'include')) {
            return $match;
        }

        if ($match && $includeChildren === 'only') {
            return false;
        }

        if (!is_object($category)) {
            $category = Categories::getInstance('content')->get($category);
        }

        return $category && array_intersect(array_keys($category->getPath()), $categoryIds);
    }

    /**
     * @param list<object> $tags
     * @param array<string> $tagIds
     * @param string $includeChildren
     */
    protected function matchTag($tags, $tagIds, $includeChildren): bool
    {
        $match = (bool) array_intersect(array_column($tags, 'id'), $tagIds);

        if (!$includeChildren || ($match && $includeChildren === 'include')) {
            return $match;
        }

        if ($match && $includeChildren === 'only') {
            return false;
        }

        if (array_intersect(array_column($tags, 'parent_id'), $tagIds)) {
            return true;
        }

        $tags = array_filter($tags, fn($tag) => substr_count($tag->path, '/') >= 2);

        if (!$tags) {
            return false;
        }

        /** @var DatabaseQuery $query */
        $query = $this->db->createQuery();
        $query
            ->select('1')
            ->from('#__tags')
            ->whereIn('id', $tagIds)
            ->andWhere(array_map(fn($tag) => "(lft < {$tag->lft} AND rgt > {$tag->rgt})", $tags));

        return (bool) $this->db->setQuery($query)->loadResult();
    }
}
