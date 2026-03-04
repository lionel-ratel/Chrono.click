<?php

namespace YOOtheme\Theme\Joomla\Listener;

use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Router\Route;
use YOOtheme\Config;

class AddSiteUrl
{
    public Config $config;
    public ?SiteApplication $joomla;

    public function __construct(Config $config, ?SiteApplication $joomla)
    {
        $this->config = $config;
        $this->joomla = $joomla;
    }

    public function handle(): void
    {
        if (!isset($this->joomla) || !$this->config->get('theme.active')) {
            return;
        }

        $item = $this->joomla->getMenu()->getDefault();
        $itemId = $item ? $item->id : 0;
        $siteUrl = Route::_("index.php?Itemid={$itemId}", false, 0, true);

        $this->config->set('~theme.site_url', $siteUrl);
    }
}
