<?php

namespace YOOtheme\Builder\Joomla\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\User\User;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\ParameterType as Param;
use Joomla\Database\QueryInterface;

/**
 * @phpstan-type TaxonomyArgs array{
 *  taxonomy?: string,
 *  ids?: list<int>,
 *  active?: list<int>,
 *  showAllChildren?: bool,
 *  startLevel?: int,
 *  endLevel?: int,
 *  }
 */

class TaxonomyModel
{
    protected DatabaseDriver $db;
    protected User $user;

    /**
     * Constructor.
     */
    public function __construct(DatabaseDriver $db, User $user)
    {
        $this->db = $db;
        $this->user = $user;
    }

    /**
     * @param TaxonomyArgs $args
     *
     * @return array<string, object>
     *
     * @see \Joomla\Component\Tags\Site\Model\TagsModel::getListQuery()
     * @see \Joomla\Component\Content\Site\Model\CategoriesModel::getListQuery()
     */
    public function getItems(array $args = []): array
    {
        $args += [
            'taxonomy' => 'category',
            'ids' => [],
            'active' => [],
            'showAllChildren' => true,
            'startLevel' => 1,
        ];

        $table = $this->getTableName($args['taxonomy']);
        $nowDate = Factory::getDate()->toSql();

        $query = $this->db
            ->createQuery()
            ->select('t.*')
            ->order($this->db->quoteName('t.lft'))
            ->where($this->db->quoteName('t.published') . ' = 1')
            ->bind(':publish_up', $nowDate)
            ->bind(':publish_down', $nowDate);

        if ($args['active']) {
            $subquery = $this->db
                ->createQuery()
                ->select('*')
                ->from($this->db->quoteName($table, 'active'))
                ->where('id IN (' . implode(',', $query->bindArray($args['active'])) . ')')
                ->where('active.lft BETWEEN t.lft AND t.rgt');
            $query->select("EXISTS({$subquery}) AS active");
        } else {
            $query->select('0 AS active');
        }

        if (!$args['showAllChildren'] || $args['startLevel'] > 1) {
            $query
                ->from($this->db->quoteName($table, 'a'))
                ->where('t.level >= :startLevel')
                ->bind(':startLevel', $args['startLevel'], Param::INTEGER);

            if ($args['ids']) {
                $query->whereIn('a.id', $args['ids']);
            } else {
                $alias = 'root';
                $query->where('a.alias = :alias')->bind(':alias', $alias);
            }

            if ($args['showAllChildren']) {
                $query->join(
                    'INNER',
                    $this->db->quoteName($table, 't'),
                    't.lft BETWEEN a.lft AND a.rgt OR EXISTS (' .
                        $this->db
                            ->createQuery()
                            ->select('p.id')
                            ->from($this->db->quoteName($table, 'p'))
                            //if it is a child of a parent of the tag
                            ->where(
                                'p.lft < t.lft AND p.rgt > t.rgt AND p.lft < a.lft AND p.rgt > a.rgt AND p.level + 1 >= :startLevel',
                            ) .
                        ')',
                );
            } else {
                $query->join(
                    'INNER',
                    $this->db->quoteName($table, 't'),
                    '(
                    (t.lft BETWEEN a.lft AND a.rgt AND t.level <= a.level +1)
                    OR t.parent_id IN (' .
                        $this->db
                            ->createQuery()
                            ->select('p.id')
                            ->from($this->db->quoteName($table, 'p'))
                            ->where(
                                'p.lft < a.lft AND p.rgt > a.rgt AND p.level + 1 >= :startLevel',
                            ) .
                        ')
                    )',
                );
            }
        } else {
            $query->from($this->db->quoteName($table, 't'))->where('t.level > 0');
        }

        if ($args['endLevel']) {
            $query
                ->where('t.level <= :endLevel')
                ->bind(':endLevel', $args['endLevel'], Param::INTEGER);
        }

        if (Multilanguage::isEnabled()) {
            $query->whereIn(
                't.language',
                [Factory::getApplication()->getLanguage()->getTag(), '*'],
                Param::STRING,
            );
        }

        if ($args['taxonomy'] === 'category') {
            $query->where(
                $this->db->quoteName('t.extension') . ' = ' . $this->db->quote('com_content'),
            );
        }

        $query->where('EXISTS(' . $this->getItemsSubquery($args['taxonomy'], $query) . ')');

        return $this->db->setQuery($query)->loadObjectList('id');
    }

    protected function getItemsSubquery(string $taxonomy, QueryInterface $query): QueryInterface
    {
        $table = $taxonomy === 'tag' ? '#__ucm_content' : '#__content';

        $subQuery = $this->db
            ->createQuery()
            ->select('*')
            ->from($this->db->quoteName($table, 'c'));

        if ($taxonomy === 'tag') {
            $subQuery->join(
                'INNER',
                $this->db->quoteName('#__contentitem_tag_map', 'm'),
                $this->db->quoteName('m.type_alias') .
                    ' = ' .
                    $this->db->quoteName('c.core_type_alias') .
                    ' AND ' .
                    $this->db->quoteName('m.core_content_id') .
                    ' = ' .
                    $this->db->quoteName('c.core_content_id'),
            );
        }

        $prefix = $taxonomy === 'tag' ? 'core_' : '';

        $subQuery
            ->join(
                'INNER',
                $this->db->quoteName($this->getTableName($taxonomy), 'child'),
                $this->db->quoteName($taxonomy === 'tag' ? 'm.tag_id' : 'c.catid') .
                    ' = ' .
                    $this->db->quoteName('child.id'),
            )
            ->where('child.lft BETWEEN t.lft AND t.rgt')
            ->where($this->db->quoteName("c.{$prefix}state") . ' = 1')
            ->where(
                '(' .
                    $this->db->quoteName("c.{$prefix}publish_up") .
                    ' IS NULL OR ' .
                    $this->db->quoteName("c.{$prefix}publish_up") .
                    ' <= :publish_up)',
            )
            ->where(
                '(' .
                    $this->db->quoteName("c.{$prefix}publish_down") .
                    ' IS NULL OR ' .
                    $this->db->quoteName("c.{$prefix}publish_down") .
                    ' >= :publish_down)',
            )
            ->where(
                $this->db->quoteName("c.{$prefix}access") .
                    ' IN (' .
                    implode(',', $query->bindArray($this->user->getAuthorisedViewLevels())) .
                    ')',
            );

        if (Multilanguage::isEnabled()) {
            $subQuery->where(
                $this->db->quoteName("c.{$prefix}language") .
                    ' IN (' .
                    implode(
                        ',',
                        $query->bindArray([
                            Factory::getApplication()->getLanguage()->getTag(),
                            '*',
                        ]),
                    ) .
                    ')',
            );
        }

        return $subQuery;
    }

    protected function getTableName(string $taxonomy): string
    {
        return '#__' . ($taxonomy === 'category' ? 'categories' : 'tags');
    }
}
