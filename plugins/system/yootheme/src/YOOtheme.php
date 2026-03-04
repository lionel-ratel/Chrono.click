<?php

namespace Joomla\Plugin\System\YOOtheme;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\Priority;
use Joomla\Event\SubscriberInterface;

class YOOtheme extends CMSPlugin implements SubscriberInterface
{
    /**
     * @inheritdoc
     * @return array<string, array{string, int}>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'onAfterInitialise' => ['onAfterInitialise', Priority::HIGH],
        ];
    }

    public function onAfterInitialise(): void
    {
        $pattern = JPATH_ROOT . '/templates/*/template_bootstrap.php';
        array_map(fn($file) => require $file, glob($pattern) ?: []);
    }
}
