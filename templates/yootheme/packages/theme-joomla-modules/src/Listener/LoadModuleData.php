<?php

namespace YOOtheme\Theme\Joomla\Listener;

use Joomla\CMS\User\User;
use YOOtheme\Config;
use YOOtheme\Theme\Joomla\ModuleConfig;

class LoadModuleData
{
    public User $user;
    public Config $config;
    public ModuleConfig $module;

    public function __construct(User $user, Config $config, ModuleConfig $module)
    {
        $this->user = $user;
        $this->config = $config;
        $this->module = $module;
    }

    public function handle(): void
    {
        $this->config->add('customizer', ['module' => $this->module->getArrayCopy()]);
        $this->config->addFile('customizer.panels.module', __DIR__ . '/../../config/modules.php');

        if ($this->user->authorise('core.manage', 'com_modules')) {
            $this->config->addFile('customizer', __DIR__ . '/../../config/customizer.php');
        }
    }
}
