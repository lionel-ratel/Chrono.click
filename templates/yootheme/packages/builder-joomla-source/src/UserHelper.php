<?php

namespace YOOtheme\Builder\Joomla\Source;

use Joomla\CMS\Access\Access;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\UserGroupsHelper;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\User\User;
use Joomla\CMS\User\UserFactoryInterface;
use Joomla\Component\Contact\Site\Helper\RouteHelper;
use Joomla\Component\Users\Administrator\Extension\UsersComponent;
use Joomla\Component\Users\Administrator\Model\UsersModel;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\DatabaseQuery;
use YOOtheme\Builder\Joomla\Source\Model\ContactsModel;
use function YOOtheme\app;

class UserHelper
{
    /**
     * @var array<int,User> User instances
     */
    protected static $instances = [];

    /**
     * Gets the user's contact.
     *
     * @param int $id
     *
     * @return object|null
     */
    public static function getContact($id)
    {
        static $contacts = [];

        if (!isset($contacts[$id])) {
            /** @var DatabaseDriver $db */
            $db = app(DatabaseDriver::class);
            $query = $db
                ->createQuery()
                ->select(['id AS contactid', 'alias', 'catid'])
                ->from('#__contact_details')
                ->where(['published = 1', 'user_id = :id'])
                ->bind(':id', $id, 'int');

            if (Multilanguage::isEnabled()) {
                $lang = Factory::getApplication()->getLanguage()->getTag();
                $query
                    ->where("(language IN (:lang, '*') OR language IS NULL)")
                    ->bind(':lang', $lang);
            }

            $query->order('id DESC')->setLimit(1);

            $contacts[$id] = $db->setQuery($query)->loadObject() ?: false;
        }

        return $contacts[$id] ?: null;
    }

    /**
     * Query users.
     *
     * @param array<string, mixed> $args
     *
     * @return list<object>
     */
    public static function queryContacts(array $args = []): array
    {
        return app(ContactsModel::class)->getItems($args);
    }

    /**
     * Gets the user's contact link.
     *
     * @param int $id
     */
    public static function getContactLink($id): ?string
    {
        if (!($contact = self::getContact($id))) {
            return null;
        }

        return RouteHelper::getContactRoute($contact->contactid, (int) $contact->catid);
    }

    public static function get(int $id): User
    {
        return self::$instances[$id] ??= Factory::getContainer()
            ->get(UserFactoryInterface::class)
            ->loadUserById($id);
    }

    /**
     * Query users.
     *
     * @param array<string, mixed> $args
     *
     * @return array<User>
     */
    public static function query(array $args = []): array
    {
        $model = static::getModel();
        $model->setState('params', ComponentHelper::getParams('com_users'));
        $model->setState('filter.active', true);
        $model->setState('filter.state', 0);

        $props = [
            'offset' => 'list.start',
            'limit' => 'list.limit',
            'order' => 'list.ordering',
            'order_direction' => 'list.direction',
            'groups' => 'filter.groups',
        ];

        if (empty($args['groups'])) {
            unset($args['groups']);
        }

        foreach (array_intersect_key($props, $args) as $key => $prop) {
            $model->setState($prop, $args[$key]);
        }

        return $model->getItems();
    }

    /**
     * @return mixed
     */
    public static function getAuthorList()
    {
        /** @var DatabaseDriver $db */
        $db = app(DatabaseDriver::class);
        /** @var DatabaseQuery $query */
        $query = $db->createQuery();
        $query
            ->select(['DISTINCT(m.user_id) AS value', 'u.name AS text'])
            ->from('#__usergroups AS ug1')
            ->innerJoin('#__usergroups AS ug2 ON ug2.lft >= ug1.lft AND ug1.rgt >= ug2.rgt')
            ->innerJoin('#__user_usergroup_map AS m ON ug2.id = m.group_id')
            ->innerJoin('#__users AS u ON u.id = m.user_id')
            ->whereIn(
                'ug1.id',
                array_filter(
                    array_map(fn($group) => $group->id, UserGroupsHelper::getInstance()->getAll()),
                    fn($id) => Access::checkGroup($id, 'core.create', 'com_content') ||
                        Access::checkGroup($id, 'core.admin'),
                ),
            );

        return $db->setQuery($query)->loadObjectList();
    }

    protected static function getModel(): UsersModel
    {
        /** @var UsersComponent $component */
        $component = Factory::getApplication()->bootComponent('com_users');

        /** @var UsersModel $model */
        $model = $component
            ->getMVCFactory()
            ->createModel('users', 'administrator', ['ignore_request' => true]);

        return $model;
    }
}
