<?php

namespace YOOtheme\Joomla;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Document\HtmlDocument;
use Joomla\CMS\Factory;
use Joomla\CMS\WebAsset\WebAssetManager;
use YOOtheme\Arr;
use YOOtheme\Path;
use YOOtheme\View\MetadataManager as BaseMetadataManager;
use YOOtheme\View\MetadataObject;

class MetadataManager extends BaseMetadataManager
{
    /**
     * @inheritdoc
     */
    public function set(string $name, $value, array $attributes = []): MetadataObject
    {
        $metadata = parent::set($name, $value, $attributes);
        $this->registerMetadata($metadata);
        return $metadata;
    }

    /**
     * @inheritdoc
     */
    public function del(string $name): void
    {
        if (isset($this->metadata[$name])) {
            $this->unregisterMetadata($this->metadata[$name]);
        }

        parent::del($name);
    }

    protected function registerMetadata(MetadataObject $metadata): void
    {
        $wa = $this->getWebAssetManager();

        if (!$wa) {
            return;
        }

        if ($metadata->getTag() === 'script') {
            if ($metadata->src) {
                $wa->registerAndUseScript(
                    $metadata->getName(),
                    static::toRelativeUrl($metadata->src),
                    ['version' => $metadata->version],
                    Arr::omit($metadata->getAttributes(), ['version', 'src']),
                );
            } elseif ($value = $metadata->getValue()) {
                $wa->addInlineScript(
                    $value,
                    [],
                    Arr::omit($metadata->getAttributes(), ['version']),
                );
            }
        }

        if ($metadata->getTag() === 'link') {
            if ($metadata->href) {
                $attrs = Arr::omit($metadata->getAttributes(), ['version', 'href', 'rel', 'defer']);

                if ($metadata->defer) {
                    /** @var CMSApplication $app */
                    $app = Factory::getApplication();
                    $document = $app->getDocument();

                    if ($document instanceof HtmlDocument) {
                        $attrs = array_merge($attrs, [
                            'rel' => 'preload',
                            'as' => 'style',
                            'onload' => "this.onload=null;this.rel='stylesheet'",
                        ]);
                    }
                }

                $wa->registerAndUseStyle(
                    $metadata->getName(),
                    static::toRelativeUrl($metadata->href),
                    ['version' => $metadata->version],
                    $attrs,
                );
            } elseif ($value = $metadata->getValue()) {
                $wa->addInlineStyle($value, [], Arr::omit($metadata->getAttributes(), ['version']));
            }
        }
    }

    protected function unregisterMetadata(MetadataObject $metadata): void
    {
        $wa = $this->getWebAssetManager();

        if (!$wa) {
            return;
        }

        if ($metadata->getTag() === 'script') {
            $wa->disableScript($metadata->getName());
        }

        if ($metadata->getTag() === 'link') {
            $wa->disableStyle($metadata->getName());
        }
    }

    protected function getWebAssetManager(): ?WebAssetManager
    {
        /** @var CMSApplication $app */
        $app = Factory::getApplication();
        $wa = $app->getDocument()->getWebAssetManager();

        // Ensure WebAssetManager is not locked
        // This might happen if a view is rendered after the documents head has been rendered (e.g. HikaShop renders multiple Views)
        if (\Closure::bind(fn() => $this->locked, $wa, $wa)()) {
            return null;
        }

        return $wa;
    }

    protected static function toRelativeUrl(string $url): string
    {
        $url = Path::resolveAlias($url);

        if (Path::isBasePath(JPATH_ROOT, $url)) {
            return Path::relative(JPATH_ROOT, $url);
        }

        return $url;
    }
}
