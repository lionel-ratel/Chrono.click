<?php

namespace YOOtheme\Theme\Joomla\Listener;

use Joomla\CMS\Document\Document;
use Joomla\CMS\Document\HtmlDocument;
use YOOtheme\Theme\Highlight\Listener\LoadHighlightScript as BaseLoadHighlightScript;

class LoadHighlightScript
{
    protected Document $document;
    protected BaseLoadHighlightScript $highlightScript;

    public function __construct(Document $document, BaseLoadHighlightScript $highlightScript)
    {
        $this->document = $document;
        $this->highlightScript = $highlightScript;
    }

    public function beforeRender(): void
    {
        if ($this->document instanceof HtmlDocument) {
            $this->highlightScript->handle($this->document->getBuffer('component') ?? '');
        }
    }
}
