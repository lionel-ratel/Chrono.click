<?php

use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Factory;

$joomla = Factory::getApplication();

$contexts = [
    'com_ajax.',
    'com_content.', // Needed for onContentBeforeSave
    'com_content.article',
    'com_templates.style',
    'com_modules.', // Needed for onContentPrepareForm in module edit
    'com_modules.module',
    'com_advancedmodules.module',
];

if (
    $joomla->isClient('site') ||
    in_array(
        ApplicationHelper::getComponentName() . '.' . $joomla->getInput()->get('view', ''),
        $contexts,
        true,
    )
) {
    // bootstrap application
    $app = require __DIR__ . '/bootstrap.php';
    $app->load(
        __DIR__ .
            '/{packages/{platform-joomla,' .
            'theme{,-consent,-highlight,-settings},' .
            'builder{,-source*,-templates,-newsletter},' .
            'styler,theme-joomla*,builder-joomla*}' .
            '/bootstrap.php,config.php}',
    );
}
