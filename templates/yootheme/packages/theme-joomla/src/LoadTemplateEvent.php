<?php

namespace YOOtheme\Theme\Joomla;

use Joomla\CMS\MVC\View\HtmlView;
use Joomla\Event\Event;

class LoadTemplateEvent extends Event
{
    protected ?string $output = null;

    public function getView(): HtmlView
    {
        return $this->arguments['view'];
    }

    public function getContext(): string
    {
        return $this->arguments['context'];
    }

    public function getTpl(): ?string
    {
        return $this->arguments['tpl'];
    }

    public function getOutput(): ?string
    {
        return $this->output;
    }

    public function setOutput(string $output): void
    {
        $this->output = $output;
    }
}
