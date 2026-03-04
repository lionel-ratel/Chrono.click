<?php

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\Registry\Registry;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die();

if (!Factory::getUser()->authorise('core.edit', 'com_templates')) {
    return;
}

$query = "SELECT * FROM #__template_styles WHERE client_id=0 AND home='1'";
if (
    !($templ = Factory::getDbo()
        ->setQuery($query)
        ->loadObject())
) {
    return;
}

$templ->params = new Registry($templ->params);

// if (!$templ->params->get('sb')) {
//     return;
// }

if (!Joomla\CMS\Plugin\PluginHelper::isEnabled('system', 'cck')) {
    return;
}

$app->getDocument()->addStyleSheet(
    Uri::root(true) . '/administrator/modules/mod_seblod_link/assets/icon.css'
);

require ModuleHelper::getLayoutPath(
    'mod_seblod_link',
    $params->get('layout', 'default')
);
