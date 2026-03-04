<?php

namespace YOOtheme\Builder\Joomla\Source\Model;

use Joomla\CMS\Language\Associations;
use Joomla\CMS\User\User;
use Joomla\Database\DatabaseDriver as Db;
use Joomla\Database\DatabaseQuery;
use Joomla\Utilities\ArrayHelper;

/**
 * @phpstan-type ContactsArgs array{
 *  catid?: array<string>,
 *  include_child_categories?: ''|'include'|'only',
 *  tag?: array<string>,
 *  include_child_tags?: ''|'include'|'only',
 *  limit?: int,
 *  offset?: int,
 * }
 */
class ContactsModel
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
     * @param ContactsArgs $args
     *
     * @return list<object>
     *
     * @see \Joomla\Component\Contact\Administrator\Model\ContactsModel::getListQuery()
     */
    public function getItems(array $args = []): array
    {
        $args += [
            'catid' => [],
            'include_child_categories' => '',
            'tag' => [],
            'include_child_tags' => '',
            'limit' => 0,
            'offset' => 0,
        ];

        /** @var DatabaseQuery $query */
        $query = $this->db->createQuery();
        $query

            // Select the required fields from the table.
            ->select([
                'a.id',
                'a.name',
                'a.alias',
                'a.checked_out',
                'a.checked_out_time',
                'a.catid',
                'a.user_id',
                'a.published',
                'a.access',
                'a.created',
                'a.created_by',
                'a.ordering',
                'a.featured',
                'a.language',
                'a.publish_up',
                'a.publish_down',
            ])

            ->from('#__contact_details AS a')

            // Join over the users for the linked user.
            ->select(['ul.name AS linked_user', 'ul.email'])
            ->leftJoin('#__users AS ul', 'ul.id = a.user_id')

            // Join over the language
            ->select(['l.title AS language_title', 'l.image AS language_image'])
            ->leftJoin('#__languages AS l', 'l.lang_code = a.language')

            // Join over the users for the checked out user.
            ->select('uc.name AS editor')
            ->leftJoin('#__users AS uc', 'uc.id = a.checked_out')

            // Join over the asset groups.
            ->select('ag.title AS access_level')
            ->leftJoin('#__viewlevels AS ag', 'ag.id = a.access')

            // Join over the categories.
            ->select('c.title AS category_title')
            ->leftJoin('#__categories AS c', 'c.id = a.catid');

        // Join over the associations.
        if (Associations::isEnabled()) {
            /** @var DatabaseQuery $subQuery */
            $subQuery = $this->db->createQuery();
            $subQuery
                ->select('COUNT(asso1.id) > 1')
                ->from('#__associations AS asso1')
                ->innerJoin('#__associations AS asso2', 'asso1.key = asso2.key')
                ->where(['asso1.id = a.id', 'asso1.context = com_contact.item']);

            $query->select("({$subQuery}) AS association");
        }

        // Implement View Level Access
        if (!$this->user->authorise('core.admin')) {
            $query->whereIn('a.access', $this->user->getAuthorisedViewLevels());
        }

        // Filter by published state
        $query->where('a.published = 1');

        // Filter by tags
        if ($tags = (array) $args['tag']) {
            $tags = ArrayHelper::toInteger($tags);
            $where = [];

            if (!$args['include_child_tags'] || $args['include_child_tags'] === 'include') {
                $subQuery = $this->db
                    ->createQuery()
                    ->select('DISTINCT content_item_id')
                    ->from('#__contentitem_tag_map')
                    ->where("type_alias = 'com_contact.contact'")
                    ->where('tag_id IN (' . join(',', $query->bindArray($tags)) . ')');

                $where[] = "a.id IN ({$subQuery})";
            }

            if ($args['include_child_tags']) {
                /** @var DatabaseQuery $subQuery */
                $subQuery = $this->db->createQuery();
                $subQuery
                    ->select('DISTINCT map.content_item_id')
                    ->from('#__tags AS sub')
                    ->innerJoin('#__tags AS this', 'sub.lft > this.lft AND sub.rgt < this.rgt')
                    ->innerJoin('#__contentitem_tag_map as map', 'sub.id = map.tag_id')
                    ->where("map.type_alias = 'com_contact.contact'")
                    ->where('this.id IN (' . join(',', $query->bindArray($tags)) . ')');

                $where[] = "a.id IN ({$subQuery})";
            }

            $query->andWhere($where);
        }

        // Filter by categories
        if (($catIds = (array) $args['catid']) && $args['include_child_categories']) {
            $catIds = ArrayHelper::toInteger($catIds);
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

            $query->andWhere("a.catid IN ({$subQuery})");
        }

        // Order by the specified column
        $query->order('a.name ASC');

        return $this->db
            ->setQuery($query->setLimit((int) $args['limit'], (int) $args['offset']))
            ->loadObjectList();
    }
}
