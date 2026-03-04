<?php

use Joomla\CMS\Factory;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Installer\InstallerScriptInterface;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\DatabaseQuery;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

return new class implements InstallerScriptInterface, ServiceProviderInterface {
    protected InstallerAdapter $adapter;

    /**
     * @var DatabaseDriver
     */
    protected DatabaseInterface $database;

    public function register(Container $container): void
    {
        $this->database = $container->get(DatabaseInterface::class);
    }

    public function install(InstallerAdapter $adapter): bool
    {
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
        return $this->requirePHP('7.4');
    }

    public function postflight(string $type, InstallerAdapter $adapter): bool
    {
        if (!in_array($type, ['install', 'update'])) {
            return true;
        }

        $this->adapter = $adapter;

        $this->patchUpdateSite();
        $this->removeOldUpdateSites();

        return true;
    }

    protected function patchUpdateSite(): void
    {
        $site = $this->getUpdateSite($this->getExtensionId());
        $server = $this->adapter->manifest->updateservers->children()[0];

        if (!$site) {
            return;
        }

        // set name and location
        $site->name = strval($server['name']);
        $site->location = trim(strval($server));

        // set installer api key
        if (!$site->extra_query && ($key = $this->getInstallerApikey())) {
            $site->extra_query = "key={$key}";
        }

        $this->database->updateObject('#__update_sites', $site, 'update_site_id');
    }

    protected function getExtensionId(): ?int
    {
        return \Closure::bind(fn() => $this->currentExtensionId, $this->adapter, $this->adapter)();
    }

    protected function getInstallerApikey(): ?string
    {
        /** @var DatabaseQuery $query */
        $query = $this->database->createQuery();
        $query
            ->select('params')
            ->from('#__extensions')
            ->where("type = {$this->database->quote('plugin')}")
            ->where("folder = {$this->database->quote('installer')}")
            ->where("element = {$this->database->quote('yootheme')}");

        if ($params = $this->database->setQuery($query)->loadResult()) {
            $params = json_decode($params);
        }

        return $params->apikey ?? null;
    }

    protected function getUpdateSite(?int $extensionId): ?object
    {
        /** @var DatabaseQuery $query */
        $query = $this->database->createQuery();
        $query
            ->select('s.*')
            ->from('#__update_sites AS s')
            ->innerJoin('#__update_sites_extensions AS se ON se.update_site_id = s.update_site_id')
            ->where("se.extension_id = {$extensionId}");

        return $extensionId ? $this->database->setQuery($query)->loadObject() : null;
    }

    /**
     * @param list<int> $siteIds
     */
    protected function removeUpdateSites(array $siteIds): void
    {
        foreach (['#__update_sites', '#__update_sites_extensions'] as $table) {
            /** @var DatabaseQuery $query */
            $query = $this->database->createQuery();
            $query->delete($table)->where('update_site_id IN (' . implode(',', $siteIds) . ')');

            $this->database->setQuery($query)->execute();
        }
    }

    protected function removeOldUpdateSites(): void
    {
        /** @var DatabaseQuery $query */
        $query = $this->database->createQuery();
        $query
            ->select('update_site_id')
            ->from('#__update_sites')
            ->where("location LIKE '%/yootheme.com/api/update/yootheme_j33.xml'");

        if ($ids = $this->database->setQuery($query)->loadColumn()) {
            $this->removeUpdateSites($ids);
        }
    }

    protected function requirePHP(string $version): bool
    {
        if (version_compare(PHP_VERSION, $version, '>=')) {
            return true;
        }

        Factory::getApplication()->enqueueMessage(
            "<p>You need PHP {$version} or later to install the template.</p>",
            'warning',
        );

        return false;
    }
};
