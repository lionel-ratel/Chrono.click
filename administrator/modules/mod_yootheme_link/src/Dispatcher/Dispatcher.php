<?php

namespace Joomla\Module\YOOthemeLink\Administrator\Dispatcher;

use Joomla\CMS\Application\WebApplication;
use Joomla\CMS\Dispatcher\AbstractModuleDispatcher;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Database\DatabaseInterface;
use Joomla\Registry\Registry;

class Dispatcher extends AbstractModuleDispatcher
{
    /**
     * @inheritdoc
     *
     * @return array<string, mixed>|false
     */
    protected function getLayoutData()
    {
        $app = $this->getApplication();

        if (!$app instanceof WebApplication) {
            return false;
        }

        if (!$app->getIdentity()->authorise('core.edit', 'com_templates')) {
            return false;
        }

        $params = Factory::getContainer()
            ->get(DatabaseInterface::class)
            ->setQuery("SELECT params FROM #__template_styles WHERE client_id = 0 AND home = '1'")
            ->loadResult();

        if (!(new Registry($params))->get('yootheme')) {
            return false;
        }

        if (!PluginHelper::isEnabled('system', 'yootheme')) {
            return false;
        }

        $wa = $app->getDocument()->getWebAssetManager();
        $wa->registerAndUseStyle(
            'yootheme_icon',
            'administrator/modules/mod_yootheme_link/assets/icon.css',
        );

        return parent::getLayoutData();
    }
}
