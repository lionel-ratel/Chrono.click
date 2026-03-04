<?php

namespace YOOtheme\Joomla;

use Joomla\CMS\Factory;
use Joomla\Event\DispatcherInterface;
use YOOtheme\Application\EventLoader;
use YOOtheme\Container;
use YOOtheme\EventDispatcher;

/**
 * @property EventDispatcher|DispatcherInterface $dispatcher
 */
class ActionLoader extends EventLoader
{
    public function __construct()
    {
        $this->dispatcher = Factory::getApplication()->getDispatcher();
    }

    /**
     * @inheritdoc
     */
    public function __invoke(Container $container, array $configs): void
    {
        if (!$container->has('dispatcher')) {
            $container->set('dispatcher', $this->dispatcher);
        }

        parent::__invoke($container, $configs);
    }
}
