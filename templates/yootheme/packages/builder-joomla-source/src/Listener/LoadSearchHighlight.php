<?php
namespace YOOtheme\Builder\Joomla\Source\Listener;

use Joomla\CMS\Application\SiteApplication;
use YOOtheme\Config;

class LoadSearchHighlight
{
    public Config $config;
    public ?SiteApplication $joomla = null;

    public function __construct(Config $config, ?SiteApplication $joomla)
    {
        $this->config = $config;
        $this->joomla = $joomla;
    }

    public function handle(): void
    {
        if (
            $this->config->get('app.template.type') === 'com_finder.search' &&
            $this->joomla->getParams()->get('highlight_terms', 1)
        ) {
            $document = $this->joomla->getDocument();
            $document->getWebAssetManager()->useScript('highlight');
            $document->addScriptOptions('highlight', [
                [
                    'class' => 'js-highlight',
                    'highLight' => [$this->config->get('req.query.q') ?? ''],
                ],
            ]);
        }
    }
}
