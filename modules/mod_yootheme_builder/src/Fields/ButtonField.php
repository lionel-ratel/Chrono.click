<?php

namespace Joomla\Module\YOOthemeBuilder\Site\Fields;

use Joomla\CMS\Form\FormField;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;
use YOOtheme\Url;

class ButtonField extends FormField
{
    protected $type = 'Button';

    /**
     * @inheritdoc
     */
    protected function getInput(): string
    {
        $template = $this->getTemplate();

        if (!$template) {
            return '<p id="alert-customizer" class="alert alert-error">Please create a YOOtheme <a href="index.php?option=com_templates">template style</a>.</p>';
        }

        $uri = Uri::getInstance();

        if ($uri->getVar('tmpl') === 'component') {
            return '';
        }

        $buttonText = Text::_('YOOtheme');
        $warningText = Text::_('Please save the module first.');

        $href = htmlspecialchars(
            Url::route('customizer', [
                'templateStyle' => $template->id,
                'format' => 'html',
                'return' => $uri->toString(['path', 'query']),
                'section' => 'joomla-modules',
            ]),
            ENT_COMPAT,
            'UTF-8',
            false,
        );

        return "<a class=\"tm-button\" href=\"{$href}\">{$buttonText}</a>
                <script>
                    document.body.addEventListener('click', function (e) {
                        if (e.target.matches('.tm-button') && !(new URL(document.location)).searchParams.has('id')) {
                            e.preventDefault();
                            window.alert('{$warningText}');
                        }
                    });
                </script>
                <style>
                    .tm-button {
                        display: block;
                        box-sizing: border-box;
                        width: 280px;
                        max-width: 100%;
                        padding: 20px 30px;
                        border-radius: 2px;
                        background: linear-gradient(140deg, #FE67D4, #4956E3);
                        box-shadow: inset 0 0 1px 0 rgba(0,0,0,0.5);
                        line-height: 10px;
                        vertical-align: middle;
                        color: #fff !important;
                        font-size: 11px;
                        font-weight: bold;
                        font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, \"Helvetica Neue\", Arial, sans-serif;
                        text-align: center;
                        text-decoration: none !important;
                        text-transform: uppercase;
                        letter-spacing: 2px;
                        -webkit-font-smoothing: antialiased;
                    }\
                </style>";
    }

    protected function getTemplate(): ?object
    {
        $templates = $this->getDatabase()
            ->setQuery(
                'SELECT id, params from #__template_styles WHERE client_id = 0 ORDER BY home DESC',
            )
            ->loadObjectList();

        foreach ($templates as $template) {
            $params = new Registry($template->params);

            if ($params->get('yootheme')) {
                return $template;
            }
        }

        return null;
    }
}
