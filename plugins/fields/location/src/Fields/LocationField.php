<?php

namespace Joomla\Plugin\Fields\Location\Fields;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;

class LocationField extends FormField
{
    protected $type = 'location';

    protected function getInput()
    {
        /** @var CMSApplication $joomla */
        $joomla = Factory::getApplication();
        $document = $joomla->getDocument();

        $base = Uri::root() . 'plugins/fields/location';
        // TODO replace deprecated call
        $document
            ->getWebAssetManager()
            ->registerAndUseScript('location', "{$base}/app/location.min.js", [], ['defer' => true])
            ->registerAndUseStyle('location', "{$base}/app/location.css");

        $params = new Registry(PluginHelper::getPlugin('fields', 'location')->params);
        $params['base'] = $base;
        $document->addScriptOptions('location', $params);

        $data = parent::getLayoutData();

        return "<yootheme-field-location><input type=\"hidden\" name=\"{$data['name']}\" value=\"{$data['value']}\"></yootheme-field-location>";
    }
}
