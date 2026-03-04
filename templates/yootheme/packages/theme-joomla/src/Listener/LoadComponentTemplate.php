<?php

namespace YOOtheme\Theme\Joomla\Listener;

use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Factory;

class LoadComponentTemplate
{
    public static function handle(): void
    {
        $joomla = Factory::getApplication();
        if (
            $joomla instanceof SiteApplication &&
            $joomla->getInput()->get('tmpl', 'index') === 'component'
        ) {
            $joomla->setTemplate((object) ['template' => 'cassiopeia', 'inheritable' => true]);
        }
    }
}
