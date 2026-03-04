<?php

namespace YOOtheme\Theme\Joomla\Listener;

use Joomla\CMS\Application\CMSApplication;

class RedirectLogin
{
    public ?CMSApplication $joomla;

    public function __construct(CMSApplication $joomla)
    {
        $this->joomla = $joomla;
    }

    public function handle(): void
    {
        $input = $this->joomla->getInput();
        $user = $this->joomla->getIdentity();
        if (
            $this->joomla->isClient('administrator') &&
            ($user->guest || !$user->authorise('core.login.admin')) &&
            $input->getCmd('option') === 'com_ajax' &&
            $input->get('p') &&
            str_contains($input->server->getString('HTTP_ACCEPT'), 'text/html')
        ) {
            $input->set('option', 'com_login');
        }
    }
}
