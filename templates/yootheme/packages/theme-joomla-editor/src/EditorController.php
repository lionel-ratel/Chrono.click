<?php

namespace YOOtheme\Theme\Joomla;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Editor\Editor;

class EditorController
{
    public static function render(CMSApplication $joomla): string
    {
        $editor = Editor::getInstance($joomla->get('editor'));
        $exclude = ['pagebreak', 'readmore', 'widgetkit'];

        // core.js needs to initialize Joomla.editors early
        $joomla->getDocument()->getWebAssetManager()->useScript('core');

        // @phpstan-ignore argument.type, argument.type, argument.type
        return "<form>{$editor->display('content', '', '100%', '550', '', '30', $exclude)}</form>";
    }
}
