<?php
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die();

class plgInstallerYootheme extends CMSPlugin
{
    public function onInstallerBeforePackageDownload(&$url, &$headers)
    {
        if (parse_url($url, PHP_URL_HOST) == 'yootheme.com' && !strpos($url, 'key=')) {
            if ($key = $this->params->get('apikey')) {
                $pos = strpos($url, '?');

                if ($pos === false) {
                    $url .= "?key=$key";
                } else {
                    $url = substr_replace($url, "?key=$key&", $pos, 1);
                }
            } else {
                $app = Factory::getApplication();
                $language = method_exists($app, 'getLanguage')
                    ? $app->getLanguage()
                    : Factory::getLanguage();

                // load default and current language
                $language->load('plg_installer_yootheme', JPATH_ADMINISTRATOR, 'en-GB', true);
                $language->load('plg_installer_yootheme', JPATH_ADMINISTRATOR, null, true);

                // warn about missing api key
                $app->enqueueMessage(Text::_('PLG_INSTALLER_YOOTHEME_API_KEY_WARNING'), 'notice');
            }
        }

        return true;
    }
}
