<?php

namespace YOOtheme\Builder\Joomla\Source;

use Exception;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\User\User;
use Joomla\Database\DatabaseDriver;
use YOOtheme\Http\Request;
use YOOtheme\Http\Response;

class SourceController
{
    /**
     * @throws Exception
     */
    public static function articles(
        Request $request,
        Response $response,
        DatabaseDriver $db,
        User $user
    ): Response {
        $ids = $request->getQueryParam('ids');
        $titles = [];

        if (!empty($ids)) {
            $query = $db
                ->createQuery()
                ->select(['id', 'title'])
                ->from('#__content')
                ->whereIn('id', $ids)
                ->whereIn('access', $user->getAuthorisedViewLevels());

            $titles = $db->setQuery($query)->loadAssocList('id', 'title');
        }

        return $response->withJson((object) $titles);
    }

    /**
     * @throws Exception
     */
    public static function users(Request $request, Response $response, User $user): Response
    {
        $titles = [];

        if ($user->authorise('core.manage', 'com_users')) {
            foreach ((array) $request->getQueryParam('ids') as $id) {
                $titles[$id] = UserHelper::get($id)->name;
            }
        }

        return $response->withJson((object) $titles);
    }

    /**
     * @throws Exception
     */
    public static function menuItems(
        Request $request,
        Response $response,
        CMSApplication $joomla
    ): Response {
        $titles = [];

        $user = $joomla->getIdentity();
        $menu = $joomla->getMenu('site');
        $items = $menu->getMenu();
        $viewLevels = $user->getAuthorisedViewLevels();
        foreach ((array) $request->getQueryParam('ids') as $id) {
            if (!isset($items[$id])) {
                continue;
            }

            if (!in_array($items[$id]->access, $viewLevels)) {
                continue;
            }

            $titles[$id] = $items[$id]->title;
        }

        return $response->withJson((object) $titles);
    }

    /**
     * @throws Exception
     */
    public static function modules(
        Request $request,
        Response $response,
        DatabaseDriver $db,
        User $user
    ): Response {
        $ids = implode(',', array_map('intval', (array) $request->getQueryParam('ids')));
        $groups = implode(',', $user->getAuthorisedViewLevels());
        $titles = [];

        if (!empty($ids)) {
            $query = "SELECT id, title
                FROM #__modules
                WHERE id IN ({$ids})
                AND access IN ({$groups})";

            $titles = $db->setQuery($query)->loadAssocList('id', 'title');
        }

        return $response->withJson((object) $titles);
    }
}
