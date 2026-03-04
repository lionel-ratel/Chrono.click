<?php

use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Installer\InstallerScriptInterface;

defined('_JEXEC') or die();

return new class implements InstallerScriptInterface {
    public function install(InstallerAdapter $adapter): bool
    {
        // enable plugin
        $extension = $adapter->getParent()->extension;
        $id = $extension->find(['type' => 'plugin', 'folder' => 'fields', 'element' => 'location']);

        if ($id) {
            $extension->load($id);
            $extension->enabled = 1;
            $extension->store();
        }

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
