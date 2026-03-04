<?php

namespace YOOtheme\Theme\Joomla\Listener;

use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Document\HtmlDocument;
use Joomla\CMS\Event\Application\BeforeCompileHeadEvent;
use YOOtheme\Theme\Consent\ConsentHelper;
use YOOtheme\View;

class LoadConsent
{
    protected View $view;
    protected ?SiteApplication $joomla;
    protected ConsentHelper $consent;

    public function __construct(View $view, ?SiteApplication $joomla, ConsentHelper $consent)
    {
        $this->view = $view;
        $this->joomla = $joomla;
        $this->consent = $consent;
    }

    public function handle(): void
    {
        $document = $this->joomla->getDocument();

        if (!$document instanceof HtmlDocument) {
            return;
        }

        $this->consent->load();

        foreach ($this->consent->getScripts('head') as $script) {
            $document->addCustomTag($script);
        }
    }

    /**
     * @param BeforeCompileHeadEvent $event
     */
    public function handleBody($event): void
    {
        $document = $event->getDocument();

        if (!$document instanceof HtmlDocument) {
            return;
        }

        $content = '';

        if ($this->consent->isEnabled) {
            $content = $this->view->render('~theme/templates/consent');
        }

        $content .= implode("\n", $this->consent->getScripts('body'));

        $document->setBuffer($content, ['type' => 'consent']);
    }
}
