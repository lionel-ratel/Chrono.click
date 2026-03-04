<?php

namespace YOOtheme\Theme\Joomla;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Editor\Editor;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Uri\Uri;
use YOOtheme\Config;
use YOOtheme\ConfigObject;
use YOOtheme\Url;

class EditorConfig extends ConfigObject
{
    /**
     * Constructor.
     */
    public function __construct(CMSApplication $joomla, Config $config)
    {
        $root = Uri::root();
        $editor = Editor::getInstance();
        $element = $joomla->get('editor');
        $language = $joomla->getLanguage();
        $id = "plg_editors_{$element}";
        $language->load($id);

        $joomla
            ->getDocument()
            ->getWebAssetManager()
            // Add media select to allow Media (button)
            ->useScript('webcomponent.media-select')
            ->useScript('editors');

        parent::__construct([
            'id' => 'editor-xtd',
            'title' => $language->_($id),
            'iframe' => Url::route('theme/editor', ['format' => 'html', 'tmpl' => 'component']),
            'buttons' => $this->getButtons($editor),
            'settings' => $this->getSettings() + [
                'branding' => false,
                'content_css' => "{$root}media/system/css/editor.min.css",
                'directionality' => $config->get('locale.rtl') ? 'rtl' : 'ltr',
                'document_base_url' => $root,
                'entity_encoding' => 'raw',
                'insert_button_items' => '', // e.g. 'hr charmap',
                'plugins' => 'link autolink hr lists charmap paste',
                'toolbar1' =>
                    'formatselect bold italic bullist numlist blockquote alignleft aligncenter alignright link insert strikethrough hr pastetext removeformat charmap outdent indent',
            ],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function getSettings(): array
    {
        $tinymce = PluginHelper::getPlugin('editors', 'tinymce');
        $params = $tinymce ? json_decode($tinymce->params, true) : [];

        if (!empty($params['newlines'])) {
            $settings = [
                'forced_root_block' => '',
                'force_p_newlines' => false,
                'force_br_newlines' => true,
            ];
        } else {
            $settings = [
                'forced_root_block' => 'p',
                'force_p_newlines' => true,
                'force_br_newlines' => false,
            ];
        }

        return $settings;
    }

    /**
     * @return list<array{text: string, link: string, options: array<string, mixed>}>
     */
    protected function getButtons(Editor $editor): array
    {
        return array_values(
            array_map(
                fn($button) => [
                    'text' => $button->get('text'),
                    'link' => $button->get('link'),
                    'options' => $button->getOptions(),
                ],
                array_filter(
                    $editor->getButtons('editor-xtd', ['pagebreak', 'readmore', 'widgetkit']),
                    fn($button) => $button->get('action') === 'modal',
                ),
            ),
        );
    }
}
