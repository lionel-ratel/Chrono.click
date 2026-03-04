<?php

namespace YOOtheme\Builder\Joomla\Listener;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\User\User;
use Joomla\CMS\User\UserFactoryInterface;
use YOOtheme\Builder\Joomla\ArticleHelper;
use YOOtheme\Config;

class LoadSessionUser
{
    public ?User $user = null;
    public Config $config;
    public CMSApplication $joomla;

    public function __construct(Config $config, CMSApplication $joomla)
    {
        $this->joomla = $joomla;
        $this->config = $config;
    }

    public function handle(): void
    {
        if (
            ArticleHelper::isArticleView() &&
            $this->config->get('req.customizer.admin') &&
            ($user_id = $this->config->get('req.customizer.user_id')) &&
            $this->joomla->getIdentity()->id !== $user_id
        ) {
            $this->user = $this->joomla->getIdentity();
            $this->setCurrentUser(
                Factory::getContainer()->get(UserFactoryInterface::class)->loadUserById($user_id),
            );
        }
    }

    public function reset(): void
    {
        if ($this->user) {
            $this->setCurrentUser($this->user);
        }
    }

    protected function setCurrentUser(User $user): void
    {
        $session = $this->joomla->getSession();

        $session->set('user', $user);

        $this->joomla->loadIdentity($user);

        // Set the flag indicating that MFA is already checked.
        $session->set('com_users.mfa_checked', 1);
    }
}
