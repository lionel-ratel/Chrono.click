<?php

namespace YOOtheme\Builder\Joomla\Source\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\User\User;
use Joomla\Database\DatabaseDriver as Db;
use Joomla\Database\DatabaseQuery;
use Joomla\Database\ParameterType as Param;

/**
 * @phpstan-type TagsArgs array{
 *  ids?: int|list<int>,
 *  parent_id?: string,
 *  limit?: int,
 *  offset?: int,
 *  order?: string,
 *  order_direction?: 'ASC'|'DESC',
 * }
 */
class TagsModel
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
     * @param TagsArgs $args
     *
     * @return list<object>
     *
     * @see \Joomla\Component\Tags\Site\Model\TagsModel::getListQuery()
     */
    public function getItems(array $args = []): array
    {
        $args += [
            'ids' => [],
            'parent_id' => '',
            'limit' => 0,
            'language' => Factory::getApplication()->getLanguage()->getTag(),
            'offset' => 0,
            'order' => 'title',
            'order_direction' => 'ASC',
        ];

        /** @var DatabaseQuery $query */
        $query = $this->db->createQuery();
        $query
            // Select required fields from the tags.
            ->select('a.*, u.name as created_by_user_name, u.email')
            ->from('#__tags as a')
            ->leftJoin('#__users as u', 'a.created_user_id = u.id')
            ->where('a.published = 1')
            ->whereIn('a.access', $this->user->getAuthorisedViewLevels());

        if ($ids = (array) $args['ids']) {
            $query->whereIn('a.id', $ids);
        }

        if ($pid = (int) $args['parent_id']) {
            $query->where('a.parent_id = :pid')->bind(':pid', $pid, Param::INTEGER);
        }

        // Exclude the root.
        $query->where('a.parent_id <> 0');

        if (Multilanguage::isEnabled()) {
            $query->whereIn('language', [$args['language'], '*'], Param::STRING);
        }

        // Order by the specified column.
        $column = $query->quoteName($args['order']);
        $direction = $args['order_direction'] === 'ASC' ? 'ASC' : 'DESC';

        $query->order(
            $args['order'] === 'rand' ? $query->rand() : ["{$column} {$direction}", 'a.title ASC'],
        );

        return $this->db
            ->setQuery($query->setLimit((int) $args['limit'], (int) $args['offset']))
            ->loadObjectList();
    }
}
