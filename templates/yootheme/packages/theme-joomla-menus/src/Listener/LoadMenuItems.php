<?php

namespace YOOtheme\Theme\Joomla\Listener;

use Joomla\CMS\Extension\Module;
use Joomla\CMS\Factory;
use Joomla\CMS\Menu\MenuItem;
use Joomla\Module\Languages\Site\Helper\LanguagesHelper;
use Joomla\Registry\Registry;
use YOOtheme\Config;

class LoadMenuItems
{
    public Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Add Language Switcher menu items.
     *
     * @param list<MenuItem> $items
     *
     * @return list<MenuItem>
     */
    public function handle(array $items): array
    {
        $this->walk($items);

        return $items;
    }

    /**
     * @param list<MenuItem> $items
     */
    protected function walk(array &$items): void
    {
        foreach ($items as $i => $item) {
            if (!empty($item->children)) {
                $this->walk($item->children);
            }

            if ($item->type !== 'url' || $item->link !== '#language-switcher') {
                continue;
            }

            $params = new Registry(
                $this->config->get("~theme.menu.items.{$item->id}.language") ?? [],
            );

            /** @var Module $module */
            $module = Factory::getApplication()->bootModule('mod_languages', 'site');
            /** @var LanguagesHelper $helper */
            $helper = $module->getHelper('LanguagesHelper');
            $languages = $helper->getLanguages($params);

            $isDropdown = $params->get('dropdown', 1);

            $subitems = [];
            foreach ($languages as $language) {
                $title = $params->get('full_name', 1)
                    ? $language->title_native
                    : strtoupper($language->sef);

                if ($language->active) {
                    $item->title = $title;
                }

                if (!$params->get('show_active', 1) && $language->active) {
                    continue;
                }

                $subitems[] = new MenuItem([
                    'id' => $language->lang_code,
                    'title' => $title,
                    'url' => $language->link,
                    'type' => 'url',
                    'level' => $item->level + ($isDropdown ? 1 : 0),
                    'parent_id' => $isDropdown ? $item->id : $item->parent_id,
                    'active' => false,
                    'class' => "item-{$language->lang_code}",
                ]);
            }

            if ($isDropdown) {
                /* @phpstan-ignore property.notFound */
                $item->children = $subitems;
                /* @phpstan-ignore property.notFound */
                $item->url = '';
                $item->type = 'heading';
            } else {
                array_splice($items, $i, 1, $subitems);
            }
        }
    }
}
