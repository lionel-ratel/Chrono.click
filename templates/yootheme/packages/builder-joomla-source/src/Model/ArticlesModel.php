<?php

namespace YOOtheme\Builder\Joomla\Source\Model;

use DateTime;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\User\User;
use Joomla\Component\Content\Administrator\Extension\ContentComponent;
use Joomla\Database\DatabaseDriver as Db;
use Joomla\Database\DatabaseQuery;
use Joomla\Database\ParameterType as Param;
use Joomla\Utilities\ArrayHelper;
use YOOtheme\Builder\DateHelper;
use YOOtheme\Builder\Joomla\Source\ArticleHelper;

/**
 * @phpstan-type ArticlesArgs array{
 *  article?: array<string>,
 *  article_operator?: 'IN'|'NOT IN',
 *  catid?: array<string>,
 *  cat_operator?: 'IN'|'NOT IN',
 *  subcategories?: bool,
 *  include_child_categories?: ''|'include'|'only',
 *  language?: string,
 *  tags?: array<string>,
 *  tag_operator?: 'IN'|'NOT IN'|'AND',
 *  include_child_tags?: ''|'include'|'only',
 *  users?: array<string>,
 *  users_operator?: 'IN'|'NOT IN',
 *  date_column?: string,
 *  date_start?: string|DateTime,
 *  date_end?: string|DateTime,
 *  fields?: list<array{id: int, value: string}>,
 *  featured?: ''|'only'|'hide',
 *  offset?: int,
 *  limit?: int,
 *  order?: string|array<string, 'ASC'|'DESC'>,
 *  order_alphanum?: bool,
 *  order_direction?: 'ASC'|'DESC',
 *  order_reverse?: bool,
 * }
 *
 * @phpstan-import-type Article from ArticleHelper
 */
class ArticlesModel
{
    protected Db $db;
    protected User $user;

    /**
     * Constructor.
     */
    public function __construct(Db $db, User $user)
    {
        $this->db = $db;
        $this->user = $user;
    }

    /**
     * @param ArticlesArgs $args
     *
     * @return list<Article>
     *
     * @see \Joomla\Component\Content\Site\Model\ArticlesModel::getListQuery()
     */
    public function getItems(array $args = []): array
    {
        $args += [
            'featured' => '',
            'article' => [],
            'article_operator' => 'IN',
            'catid' => [],
            'cat_operator' => 'IN',
            'subcategories' => false,
            'include_child_categories' => '',
            'tags' => [],
            'tag_operator' => 'IN',
            'include_child_tags' => '',
            'users' => [],
            'users_operator' => 'IN',
            'date_column' => null,
            'date_start' => null,
            'date_end' => null,
            'fields' => [],
            'limit' => 0,
            'offset' => 0,
            'order' => 'ordering',
            'order_direction' => 'ASC',
            'order_alphanum' => false,
            'order_reverse' => false,
        ];

        $access = $this->user->getAuthorisedViewLevels();
        $nowDate = Factory::getDate()->toSql();
        $archived = ContentComponent::CONDITION_ARCHIVED;
        $unpublished = ContentComponent::CONDITION_UNPUBLISHED;

        /** @var DatabaseQuery $query */
        $query = $this->db->createQuery();
        $query
            // Select the required fields from the table.
            ->select([
                'a.id',
                'a.title',
                'a.alias',
                'a.introtext',
                'a.fulltext',
                'a.checked_out',
                'a.checked_out_time',
                'a.catid',
                'a.created',
                'a.created_by',
                'a.created_by_alias',
                'a.modified',
                'a.modified_by',
                // Use created if publish_up is null
                'CASE WHEN a.publish_up IS NULL THEN a.created ELSE a.publish_up END AS publish_up',
                'a.publish_down',
                'a.images',
                'a.urls',
                'a.attribs',
                'a.metadata',
                'a.metakey',
                'a.metadesc',
                'a.access',
                'a.hits',
                'a.featured',
                'a.language',
                "{$query->length('a.fulltext')} AS readmore",
                'a.ordering',
                // Published/archived article in archived category is treated as archived article. If category is not published then force 0.
                "CASE WHEN c.published = 2 AND a.state > 0 THEN {$archived} WHEN c.published != 1 THEN {$unpublished} ELSE a.state END AS state",
                "CASE WHEN a.created_by_alias > ' ' THEN a.created_by_alias ELSE ua.name END AS author",
            ])

            ->from('#__content AS a')

            // Join over the users for the author email
            ->select('ua.email AS author_email')
            ->leftJoin('#__users AS ua', 'ua.id = a.created_by')

            // Join over the users for the modified by name
            ->select('uam.name AS modified_by_name')
            ->leftJoin('#__users AS uam', 'uam.id = a.modified_by')

            // Join over the frontpage
            ->select(['fp.featured_up', 'fp.featured_down'])
            ->leftJoin('#__content_frontpage AS fp', 'fp.content_id = a.id')

            // Join over the category
            ->select([
                'c.title AS category_title',
                'c.path AS category_route',
                'c.access AS category_access',
                'c.alias AS category_alias',
                'c.language AS category_language',
                'c.published',
                'c.published AS parents_published',
                'c.lft',
            ])
            ->leftJoin('#__categories AS c', 'c.id = a.catid')

            // Join over the parent category
            ->select([
                'parent.title AS parent_title',
                'parent.id AS parent_id',
                'parent.path AS parent_route',
                'parent.alias AS parent_alias',
                'parent.language AS parent_language',
            ])
            ->leftJoin('#__categories AS parent', 'parent.id = c.parent_id');

        // Join on voting table
        if (PluginHelper::isEnabled('content', 'vote')) {
            $query
                ->select([
                    'COALESCE(NULLIF(ROUND(v.rating_sum / v.rating_count, 1), 0), 0) AS rating',
                    'COALESCE(NULLIF(v.rating_count, 0), 0) AS rating_count',
                ])
                ->leftJoin('#__content_rating v', 'a.id = v.content_id');
        }

        // Article and Category has to be published
        $query->where(['a.state = 1', 'c.published = 1']);

        // Filter by access level
        $query->whereIn('a.access', $access)->whereIn('c.access', $access);

        // Filter by language
        if (Multilanguage::isEnabled()) {
            $query->whereIn(
                'a.language',
                [$args['language'] ?? Factory::getApplication()->getLanguage()->getTag(), '*'],
                Param::STRING,
            );
        }

        // Filter by featured
        if (in_array($args['featured'], ['hide', 'only'])) {
            $args['featured'] === 'hide'
                ? $query->andWhere([
                    'a.featured = 0',
                    '(fp.featured_up IS NOT NULL AND fp.featured_up >= :featuredUp)',
                    '(fp.featured_down IS NOT NULL AND fp.featured_down <= :featuredDown)',
                ])
                : $query->where([
                    'a.featured = 1',
                    '(fp.featured_up IS NULL OR fp.featured_up <= :featuredUp)',
                    '(fp.featured_down IS NULL OR fp.featured_down >= :featuredDown)',
                ]);

            $query->bind(':featuredUp', $nowDate)->bind(':featuredDown', $nowDate);
        }

        // Filter by users
        if ($users = (array) $args['users']) {
            $users = ArrayHelper::toInteger($users);
            $args['users_operator'] === 'IN'
                ? $query->whereIn('a.created_by', $users)
                : $query->whereNotIn('a.created_by', $users);
        }

        // Filter by articles
        if ($articles = (array) $args['article']) {
            $articles = ArrayHelper::toInteger($articles);
            $args['article_operator'] === 'IN'
                ? $query->whereIn('a.id', $articles)
                : $query->whereNotIn('a.id', $articles);
        }

        // Filter by categories
        if ($catIds = (array) $args['catid']) {
            $catIds = ArrayHelper::toInteger($catIds);
            $operator = $args['cat_operator'];

            if ($args['subcategories']) {
                $args['include_child_categories'] = 'include';
            }

            if (!$args['include_child_categories']) {
                $operator === 'IN'
                    ? $query->whereIn('a.catid', $catIds)
                    : $query->whereNotIn('a.catid', $catIds);
            } elseif (in_array($operator, ['IN', 'NOT IN'])) {
                /** @var DatabaseQuery $subQuery */
                $subQuery = $this->db->createQuery();
                $subQuery
                    ->select('DISTINCT sub.id')
                    ->from('#__categories AS sub')
                    ->innerJoin(
                        '#__categories AS this',
                        $args['include_child_categories'] === 'include'
                            ? 'sub.lft >= this.lft AND sub.rgt <= this.rgt'
                            : 'sub.lft > this.lft AND sub.rgt < this.rgt',
                    )
                    ->where('this.id IN (' . join(',', $query->bindArray($catIds)) . ')');

                $query->andWhere("a.catid {$operator} ({$subQuery})");
            }
        }

        // Filter by tags
        if ($tagIds = array_filter((array) $args['tags'])) {
            $tagIds = ArrayHelper::toInteger($tagIds);
            $operator = $args['tag_operator'];

            if ($operator === 'AND') {
                if ($args['include_child_tags']) {
                    /** @var DatabaseQuery $tagQuery */
                    $tagQuery = $this->db->createQuery();
                    $tagQuery
                        ->select('sub.id')
                        ->from('#__tags AS sub')
                        ->innerJoin(
                            '#__tags AS this',
                            $args['include_child_tags'] === 'include'
                                ? 'sub.lft >= this.lft AND sub.rgt <= this.rgt'
                                : 'sub.lft > this.lft AND sub.rgt < this.rgt',
                        )
                        ->where('this.id IN (' . join(',', $query->bindArray($tagIds)) . ')');
                } else {
                    $tagQuery = join(',', $query->bindArray($tagIds));
                }

                $countQuery = $args['include_child_tags']
                    ? (clone $tagQuery)->clear('select')->select('COUNT(sub.id)')
                    : count($tagIds);

                $subQuery = $this->db
                    ->createQuery()
                    ->select('COUNT(1)')
                    ->from('#__contentitem_tag_map')
                    ->where([
                        'a.id = content_item_id',
                        "tag_id IN ({$tagQuery})",
                        "type_alias = 'com_content.article'",
                    ]);

                $query->andWhere("({$subQuery}) = {$countQuery}");
            } elseif (in_array($operator, ['IN', 'NOT IN'])) {
                /** @var DatabaseQuery $subQuery */
                $subQuery = $this->db->createQuery();
                $subQuery = !$args['include_child_tags']
                    ? $subQuery
                        ->select('content_item_id')
                        ->from('#__contentitem_tag_map')
                        ->where("type_alias = 'com_content.article'")
                        ->where('tag_id IN (' . join(',', $query->bindArray($tagIds)) . ')')
                    : $subQuery
                        ->select('DISTINCT map.content_item_id')
                        ->from('#__tags AS sub')
                        ->innerJoin(
                            '#__tags AS this',
                            $args['include_child_tags'] === 'include'
                                ? 'sub.lft >= this.lft AND sub.rgt <= this.rgt'
                                : 'sub.lft > this.lft AND sub.rgt < this.rgt',
                        )
                        ->innerJoin('#__contentitem_tag_map AS map', 'sub.id = map.tag_id')
                        ->where("map.type_alias = 'com_content.article'")
                        ->where('this.id IN (' . join(',', $query->bindArray($tagIds)) . ')');

                $query->andWhere("a.id {$operator} ({$subQuery})");
            }
        }

        // Filter by publishUp and publishDown
        $query
            ->where([
                '(a.publish_up IS NULL OR a.publish_up <= :publishUp)',
                '(a.publish_down IS NULL OR a.publish_down >= :publishDown)',
            ])
            ->bind(':publishUp', $nowDate)
            ->bind(':publishDown', $nowDate);

        // Filter by date
        if ($column = $args['date_column']) {
            $dateStart = DateHelper::toSql($args['date_start'] ?? null);
            $dateEnd = DateHelper::toSql($args['date_end'] ?? null);

            if (str_starts_with($column, 'field:') && ($dateStart || $dateEnd)) {
                $conditionQuery = $this->db
                    ->createQuery()
                    ->select('item_id')
                    ->from('#__fields_values')
                    ->where('field_id = :fieldIdDate');

                if ($dateStart) {
                    $conditionQuery->where('value >= :dateStart');
                    $query->bind(':dateStart', $dateStart);
                }

                if ($dateEnd) {
                    $conditionQuery->where('value <= :dateEnd');
                    $query->bind(':dateEnd', $dateEnd);
                }

                $dateField = (int) substr($column, 6);
                $query
                    ->bind(':fieldIdDate', $dateField, Param::INTEGER)
                    ->andWhere("a.id IN ({$conditionQuery})");
            } else {
                if ($dateStart) {
                    $query->where("a.{$column} >= :dateStart");
                    $query->bind(':dateStart', $dateStart);
                }

                if ($dateEnd) {
                    $query->where("a.{$column} <= :dateEnd");
                    $query->bind(':dateEnd', $dateEnd);
                }
            }
        }

        // Filter by fields
        if ($fields = (array) $args['fields']) {
            foreach ($fields as $i => $field) {
                $subQuery = $this->db
                    ->createQuery()
                    ->select('item_id')
                    ->from('#__fields_values')
                    ->where("field_id = :fieldId{$i}")
                    ->where("value = :fieldValue{$i}");

                $query
                    ->bind(":fieldId{$i}", $field['id'], Param::INTEGER)
                    ->bind(":fieldValue{$i}", $field['value'])
                    ->andWhere("a.id IN ({$subQuery})");
            }
        }

        // Order by the specified column
        if (is_string($args['order'])) {
            $column = $query->quoteName("a.{$args['order']}");
            $direction = $args['order_direction'] === 'ASC' ? 'ASC' : 'DESC';

            if ($args['order'] === 'front') {
                $query->select('fp.ordering');
                $column = $query->quoteName('fp.ordering');
            } elseif (str_starts_with($args['order'], 'field:')) {
                $orderField = (int) substr($args['order'], 6);
                $column = $query->quoteName('fields.value');

                $query
                    ->leftJoin(
                        '#__fields_values AS fields',
                        'a.id = fields.item_id AND fields.field_id = :fieldId',
                    )
                    ->bind(':fieldId', $orderField, Param::INTEGER);
            }

            $query->order(
                $args['order'] === 'rand'
                    ? $query->rand()
                    : ($args['order_alphanum']
                        ? [
                            "(SUBSTR({$column}, 1, 1) > '9') {$direction}",
                            "{$column}+0 {$direction}",
                            "{$column} {$direction}",
                        ]
                        : "{$column} {$direction}"),
            );
        } elseif (is_array($args['order'])) {
            foreach ($args['order'] as $column => $direction) {
                $query->order(
                    $query->quoteName("a.{$column}") .
                        ' ' .
                        ($direction === 'ASC' ? 'ASC' : 'DESC'),
                );
            }
        }

        $items = $this->db
            ->setQuery($query->setLimit((int) $args['limit'], (int) $args['offset']))
            ->loadObjectList();

        return empty($args['order_reverse']) ? $items : array_reverse($items);
    }
}
