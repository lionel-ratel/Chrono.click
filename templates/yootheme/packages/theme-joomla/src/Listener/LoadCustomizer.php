<?php

namespace YOOtheme\Theme\Joomla\Listener;

use Joomla\CMS\User\User;
use YOOtheme\Config;
use YOOtheme\Theme\Joomla\ApiKey;

class LoadCustomizer
{
    public User $user;
    public Config $config;
    public ApiKey $apiKey;

    public function __construct(Config $config, ApiKey $apiKey, User $user)
    {
        $this->user = $user;
        $this->config = $config;
        $this->apiKey = $apiKey;
    }

    public function handle(): void
    {
        $this->config->addFile('customizer', __DIR__ . '/../../config/customizer.php');
        $this->config->add('customizer', [
            'config' => ['yootheme_apikey' => $this->apiKey->get()],
            'user_id' => $this->user->id,
        ]);
    }
}
