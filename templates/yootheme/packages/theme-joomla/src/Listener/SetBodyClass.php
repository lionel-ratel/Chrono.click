<?php

namespace YOOtheme\Theme\Joomla\Listener;

use Joomla\CMS\Application\SiteApplication;
use YOOtheme\Config;

class SetBodyClass
{
    public Config $config;
    public ?SiteApplication $joomla;

    public function __construct(Config $config, ?SiteApplication $joomla = null)
    {
        $this->config = $config;
        $this->joomla = $joomla;
    }

    public function handle(): void
    {
        if ($this->joomla) {
            $this->config->set('~theme.body_class', [
                $this->joomla->getParams()->get('pageclass_sfx'),
            ]);
        }
    }
}
