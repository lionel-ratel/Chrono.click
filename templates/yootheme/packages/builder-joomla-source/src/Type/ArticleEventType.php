<?php

namespace YOOtheme\Builder\Joomla\Source\Type;

use Joomla\CMS\Event\Content\AfterDisplayEvent;
use Joomla\CMS\Event\Content\AfterTitleEvent;
use Joomla\CMS\Event\Content\BeforeDisplayEvent;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use stdClass;
use YOOtheme\Event;
use YOOtheme\Theme\Joomla\LoadTemplateEvent;

class ArticleEventType extends EventType
{
    /**
     * @param array<string, mixed> $args
     * @param mixed $context
     */
    public static function resolve(object $article, array $args, $context, object $info): string
    {
        $key = $info->fieldName;

        if (isset($article->event->$key)) {
            return $article->event->$key;
        }

        $marker = "{# article_{$article->id}_{$key} #}";

        // When Joomla 6.1 is minimum switch to native onAfterDisplay
        static::addListenerOnce(
            'replaceArticleEventType',
            /**
             * @param LoadTemplateEvent $event
             */
            function ($event) use ($article, $key, $marker): void {
                if (!isset($article->event->$key)) {
                    static::applyContentPlugins($article);
                }

                $event->setOutput(str_replace($marker, $article->event->$key, $event->getOutput()));
            },
        );

        return $marker;
    }

    protected static function addListenerOnce(string $name, callable $handler): void
    {
        Event::on($name, function (...$args) use ($name, $handler) {
            Event::off($name, $handler);
            $handler(...$args);
        });
    }

    protected static function applyContentPlugins(object $article): void
    {
        $dispatcher = Factory::getApplication()->getDispatcher();

        // Process the content plugins.
        PluginHelper::importPlugin('content');

        $article->event = new stdClass();

        $contentEventArguments = [
            'context' => 'com_content.article',
            'subject' => $article,
            'params' => $article->params,
        ];

        // Extra content from events

        $contentEvents = [
            'afterDisplayTitle' => new AfterTitleEvent(
                'onContentAfterTitle',
                $contentEventArguments,
            ),
            'beforeDisplayContent' => new BeforeDisplayEvent(
                'onContentBeforeDisplay',
                $contentEventArguments,
            ),
            'afterDisplayContent' => new AfterDisplayEvent(
                'onContentAfterDisplay',
                $contentEventArguments,
            ),
        ];

        foreach ($contentEvents as $resultKey => $event) {
            $results = $dispatcher->dispatch($event->getName(), $event)->getArgument('result', []);

            $article->event->{$resultKey} = $results ? trim(implode("\n", $results)) : '';
        }
    }
}
