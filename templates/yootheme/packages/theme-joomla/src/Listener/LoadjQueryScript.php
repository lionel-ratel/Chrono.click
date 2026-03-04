<?php

namespace YOOtheme\Theme\Joomla\Listener;

use Joomla\CMS\HTML\HTMLHelper;
use YOOtheme\Config;

class LoadjQueryScript
{
    public Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function handle(): void
    {
        if ($this->config->get('~theme.jquery')) {
            HTMLHelper::_('jquery.framework');
        }
    }
}
