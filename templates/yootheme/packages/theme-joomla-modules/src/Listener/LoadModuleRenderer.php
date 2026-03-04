<?php

namespace YOOtheme\Theme\Joomla\Listener;

use Joomla\CMS\Document\Document;
use Joomla\CMS\Document\Factory;
use Joomla\CMS\Document\FactoryInterface;
use Joomla\CMS\Document\RendererInterface;
use YOOtheme\Config;
use YOOtheme\Theme\Joomla\ModulesRenderer;

class LoadModuleRenderer
{
    public Config $config;
    public Document $document;

    public function __construct(Config $config, Document $document)
    {
        $this->config = $config;
        $this->document = $document;
    }

    public function handle(): void
    {
        if ($this->config->get('app.isSite')) {
            $this->document->setFactory($this->getFactory());
        }
    }

    protected function getFactory(): FactoryInterface
    {
        return new class extends Factory {
            public function createRenderer(
                Document $document,
                string $type,
                string $docType = ''
            ): RendererInterface {
                return $type === 'modules'
                    ? new ModulesRenderer($document)
                    : parent::createRenderer($document, $type, $docType);
            }
        };
    }
}
