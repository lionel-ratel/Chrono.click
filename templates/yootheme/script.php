<?php

use Joomla\CMS\Factory;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Installer\InstallerScriptInterface;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\DatabaseQuery;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;

return new class implements InstallerScriptInterface, ServiceProviderInterface {
    /**
     * @var DatabaseDriver
     */
    protected DatabaseInterface $db;

    protected string $tmp;

    protected string $name;

    protected string $dest;

    public function register(Container $container): void
    {
        $this->db = $container->get(DatabaseInterface::class);
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
        $this->tmp = Factory::getApplication()->get('tmp_path');
        $this->name = $adapter->getName();
        $this->dest = $adapter->getParent()->getPath('extension_root');

        if ($type == 'update') {
            // backup theme*.css
            $files = glob("{$this->dest}/css/theme*.css");

            foreach ($files as $file) {
                $filename = basename($file);

                if (strpos($file, 'update.css')) {
                    continue;
                }

                if (is_file($file)) {
                    File::move($file, "{$this->tmp}/{$filename}");
                }
            }

            // clean folders
            foreach (['assets', 'cache', 'less', 'packages', 'templates', 'vendor'] as $path) {
                if (is_dir("{$this->dest}/{$path}")) {
                    Folder::delete("{$this->dest}/{$path}");
                }
            }
        }

        return true;
    }

    public function postflight(string $type, InstallerAdapter $adapter): bool
    {
        if ($type == 'update') {
            // restore theme*.css
            foreach (glob("{$this->tmp}/theme*.css") as $file) {
                $filename = basename($file);

                if (is_file($file)) {
                    File::move($file, "{$this->dest}/css/{$filename}");
                }
            }

            foreach ($this->loadTemplateStyles() as $id => $params) {
                $params = json_decode($params, true);

                // Add theme.support for uikit3
                if ($params && empty($params['uikit3'])) {
                    $params['uikit3'] = true;
                    $this->updateTemplateStyle($id, json_encode($params));
                }
            }
        }
        return true;
    }

    /**
     * @return array<string, string>
     */
    protected function loadTemplateStyles(): array
    {
        /** @var DatabaseQuery $query */
        $query = $this->db->createQuery();
        $query
            ->setQuery('SELECT id, params FROM #__template_styles WHERE template = :template')
            ->bind(':template', $this->name);

        return $this->db->setQuery($query)->loadAssocList('id', 'params');
    }

    /**
     * @param string $id
     */
    protected function updateTemplateStyle($id, string $params): void
    {
        $style = (object) ['id' => $id, 'params' => $params];
        $this->db->updateObject('#__template_styles', $style, 'id');
    }
};
