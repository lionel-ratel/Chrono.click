<?php

namespace YOOtheme\Builder\Joomla\Source\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\TagsHelper;
use Joomla\CMS\Language\Multilanguage;
use Joomla\Database\DatabaseDriver as Db;

/**
 * @phpstan-type TagArgs array{
 *  typesr?: array<string>,
 *  include_children?: bool,
 *  limit?: int,
 *  offset?: int,
 *  order?: string,
 *  order_alphanum?: bool,
 *  order_direction?: 'ASC'|'DESC',
 * }
 */
class TagModel
{
    protected Db $db;

    /**
     * Constructor.
     */
    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    /**
     * @param TagArgs $args
     *
     * @return list<object>
     *
     * @see \Joomla\Component\Tags\Site\Model\TagModel::getListQuery()
     */
    public function getItems(int $tagId, array $args = []): array
    {
        $args += [
            'typesr' => [],
            'include_children' => false,
            'language' => Factory::getApplication()->getLanguage()->getTag(),
            'limit' => 0,
            'offset' => 0,
            'order' => 'core_title',
            'order_alphanum' => false,
            'order_direction' => 'ASC',
        ];

        $query = (new TagsHelper())->getTagItemsQuery(
            $tagId,
            array_filter($args['typesr']) ?: null,
            $args['include_children'],
            "c.{$args['order']}",
            $args['order_direction'],
            true,
            Multilanguage::isEnabled() ? $args['language'] : 'all',
            '1',
        );

        if ($args['order'] === 'rand') {
            $query->clear('order')->order($query->rand());
        } elseif ($args['order_alphanum']) {
            $column = $query->quoteName("c.{$args['order']}");
            $direction = $args['order_direction'] === 'ASC' ? 'ASC' : 'DESC';

            $query
                ->clear('order')
                ->order([
                    "(SUBSTR({$column}, 1, 1) > '9') {$direction}",
                    "{$column}+0 {$direction}",
                    "{$column} {$direction}",
                ]);
        }

        return $this->db
            ->setQuery($query->setLimit((int) $args['limit'], (int) $args['offset']))
            ->loadObjectList();
    }
}
