<?php

namespace YOOtheme\Theme\Joomla\Listener;

use Joomla\CMS\Router\Route;
use Joomla\CMS\User\User;
use YOOtheme\Config;
use YOOtheme\Http\Request;
use YOOtheme\Http\Response;
use function YOOtheme\app;

class CheckUserPermission
{
    public User $user;
    public Config $config;

    public function __construct(Config $config, User $user)
    {
        $this->user = $user;
        $this->config = $config;
    }

    /**
     * Check permission of current user.
     *
     * @param Request $request
     */
    public function handle($request, callable $next): Response
    {
        if (
            !$request->getAttribute('allowed') &&
            !$this->user->authorise('core.edit', 'com_templates')
        ) {
            // redirect guest user to user login
            if (
                $this->user->guest &&
                str_contains($request->getHeaderLine('Accept'), 'text/html')
            ) {
                $url = Route::_(
                    $this->config->get('app.isAdmin')
                        ? 'index.php?option=com_login'
                        : 'index.php?option=com_users&view=login',
                    false,
                );

                return app(Response::class)->withRedirect($url);
            }

            $request->abort(403, 'Insufficient User Rights.');
        }

        return $next($request);
    }
}
