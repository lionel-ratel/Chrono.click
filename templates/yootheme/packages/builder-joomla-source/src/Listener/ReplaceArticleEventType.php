<?php

namespace YOOtheme\Builder\Joomla\Source\Listener;

use YOOtheme\Event;
use YOOtheme\Theme\Joomla\LoadTemplateEvent;

class ReplaceArticleEventType
{
    /**
     * @param LoadTemplateEvent $event
     */
    public static function handle($event): void
    {
        // This is a workaround because it's not possible to listen for the onLoadTemplate event,
        // while the event is being dispatched.
        Event::emit('replaceArticleEventType', $event);
    }
}
