<?php

namespace YOOtheme\Theme\Joomla\Listener;

use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Document\HtmlDocument;
use YOOtheme\Config;
use YOOtheme\Event;

class LoadThemeHead
{
    public Config $config;
    public ?HtmlDocument $document;

    public function __construct(Config $config, ?SiteApplication $joomla = null)
    {
        $this->config = $config;
        $document = $joomla ? $joomla->getDocument() : null;
        $this->document = $document instanceof HtmlDocument ? $document : null;
    }

    public function handle(): void
    {
        if (!isset($this->document) || !$this->config->get('theme.active')) {
            return;
        }

        $this->config->set('~theme.direction', $this->document->getDirection());

        $this->addHeadLink('~theme.favicon', 'icon', 'rel', ['sizes' => 'any']);
        $this->addHeadLink('~theme.favicon_svg', 'icon', 'rel', ['type' => 'image/svg+xml']);
        $this->addHeadLink('~theme.touchicon', 'apple-touch-icon');

        Event::emit('theme.head');
    }

    /**
     * @param string|array<string, string> ...$args
     */
    protected function addHeadLink(string $src, ...$args): void
    {
        $src = $this->config->get($src);

        if (!$src) {
            return;
        }

        // Workaround for Joomla's HtmlDocument::addHeadLink which doesn't allow duplicate links,
        // we need to potentially add multiple favicons with the same link but different attributes (e.g. sizes or type).
        static $i = 1;
        $postFixedSrc = $src;
        while (isset($this->document->_links[$postFixedSrc])) {
            $postFixedSrc = $src . '#' . ++$i;
        }

        $this->document->addHeadLink($postFixedSrc, ...$args);
    }
}
