<?php

use Joomla\CMS\Factory;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Installer\InstallerScriptInterface;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

return new class implements InstallerScriptInterface, ServiceProviderInterface {
    protected DatabaseInterface $db;

    public function register(Container $container): void
    {
        $this->db = $container->get(DatabaseInterface::class);
    }

    public function install(InstallerAdapter $adapter): bool
    {
        $module = (object) [
            'title' => 'YOOtheme Link',
            'position' => 'menu',
            'published' => 1,
            'client_id' => 1,
            'module' => 'mod_yootheme_link',
            'access' => 1,
            'params' => '',
            'language' => '*',
            'publish_up' => Factory::getDate()->toSql(),
            'ordering' => 2,
        ];
        $this->db->insertObject('#__modules', $module);

        $menu = (object) ['moduleid' => $this->db->insertid(), 'menuid' => 0];
        $this->db->insertObject('#__modules_menu', $menu);

        return true;
    }

    public function update(InstallerAdapter $adapter): bool
    {
        return true;
    }

    public function uninstall(InstallerAdapter $adapter): bool
    {
        return true;
    }

    public function preflight(string $type, InstallerAdapter $adapter): bool
    {
        return true;
    }

    public function postflight(string $type, InstallerAdapter $adapter): bool
    {
        return true;
    }
};
