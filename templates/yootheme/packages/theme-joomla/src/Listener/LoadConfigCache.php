<?php

namespace YOOtheme\Theme\Joomla\Listener;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Document\Document;
use Joomla\CMS\Document\ErrorDocument;
use Joomla\CMS\Document\HtmlDocument;
use Joomla\CMS\WebAsset\WebAssetItemInterface;
use YOOtheme\Config;
use YOOtheme\Theme\ThemeConfig;

/**
 * This class uses WebAssetItems to store config values when Joomla's caching is enabled.
 */
class LoadConfigCache
{
    /**
     * @var list<string>
     */
    public array $keys = [
        'app.isBuilder',
        'app.template.type',
        '~theme.page_layout',
        'header.section.transparent',
    ];
    public int $caching;
    public Config $config;
    public ?HtmlDocument $document;
    protected ThemeConfig $theme;

    public function __construct(
        Config $config,
        CMSApplication $joomla,
        ?Document $document,
        ThemeConfig $theme
    ) {
        $this->config = $config;
        $this->document =
            $document instanceof HtmlDocument && !($document instanceof ErrorDocument)
                ? $document
                : null;
        $this->caching = $this->document ? (int) $joomla->get('caching', 0) : 0;
        $this->theme = $theme;
    }

    /**
     * Add to Joomla caching from layout rendering.
     */
    public function addFromPage(): void
    {
        if (!$this->caching) {
            return;
        }

        $config = [];
        foreach ($this->keys as $key) {
            $config[$key] = $this->config->get($key);
        }

        $this->set('config', $config);
        $this->set('scripts', $this->theme->scripts);
    }

    /**
     * Load config from Joomla caching.
     */
    public function loadPage(): void
    {
        if (!$this->caching) {
            return;
        }

        $config = $this->get('config');
        foreach ($this->keys as $key) {
            if (isset($config[$key])) {
                $this->config->set($key, $config[$key]);
            }
        }

        $this->loadScripts();
    }

    /**
     * Add to Joomla caching after Modules rendering.
     */
    public function addFromModules(): void
    {
        if ($this->caching === 2) {
            $this->set('scripts', $this->theme->scripts);
        }
    }

    /**
     * Load scripts from Joomla caching before compile head.
     */
    public function loadFromModules(): void
    {
        if ($this->caching === 2) {
            $this->loadScripts();
        }
    }

    protected function loadScripts(): void
    {
        $scripts = $this->get('scripts');
        if (isset($scripts)) {
            $this->theme->scripts = $scripts;
        }
    }

    /**
     * @param array<string, mixed>|list<mixed> $value
     */
    protected function set(string $key, array $value): void
    {
        $this->getAssetItem()->setOption($key, json_encode($value));
    }

    /**
     * @return array<string, mixed>|list<mixed>|null
     */
    protected function get(string $key): ?array
    {
        $value = $this->getAssetItem()->getOption($key);
        return isset($value) ? json_decode($value, true) : null;
    }

    protected function getAssetItem(): WebAssetItemInterface
    {
        $wa = $this->document->getWebAssetManager();

        $args = ['yootheme', 'cache'];

        if (!$wa->assetExists(...$args)) {
            $wa->registerAsset(...$args)->useAsset(...$args);
        }

        return $wa->getAsset(...$args);
    }
}
