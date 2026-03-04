<?php

namespace YOOtheme\Theme\Joomla;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Document\Document;
use Joomla\CMS\Document\HtmlDocument;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\User\User;
use Joomla\Database\DatabaseDriver;
use YOOtheme\Config;
use YOOtheme\Event;
use YOOtheme\Http\Request;
use YOOtheme\Url;

class CustomizerController
{
    public static function index(
        Request $request,
        User $user,
        Config $config,
        Document $document,
        CMSApplication $joomla
    ): void {
        $request->abortIf(
            !$document instanceof HtmlDocument || !$config('theme.id'),
            400,
            'Bad Request',
        );

        $document->getWebAssetManager()->useScript('keepalive');

        // init customizer
        Event::emit('customizer.init');

        // init config
        $config->add('customizer', [
            'config' => $config('~theme'),
            'return' => $request->getQueryParam('return') ?: Url::to('administrator/index.php'),
        ]);

        // api key editable?
        if (
            !$user->authorise('core.edit', 'com_installer') ||
            !$user->authorise('core.manage', 'com_installer')
        ) {
            $config->del('customizer.sections.settings.fields.settings.items.api-key');
        }

        // disable theme active
        $config->set('theme.active', false);

        // set system template
        $joomla->set('theme', 'system');
        $joomla->getInput()->set('tmpl', 'component');

        /** @var HtmlDocument $document */
        $document->setTitle("Website Builder - {$joomla->get('sitename')}");

        $document->addHeadLink(
            HTMLHelper::image('joomla-favicon.svg', '', [], true, 1),
            'icon',
            'rel',
            ['type' => 'image/svg+xml'],
        );
        $document->addHeadLink(
            HTMLHelper::image('favicon.ico', '', [], true, 1),
            'alternate icon',
            'rel',
            ['type' => 'image/vnd.microsoft.icon'],
        );
        $document->addHeadLink(
            HTMLHelper::image('joomla-favicon-pinned.svg', '', [], true, 1),
            'mask-icon',
            'rel',
            ['color' => '#000'],
        );

        $document->setBuffer('<div id="customizer"></div>', ['type' => 'component']);
    }

    public static function save(
        Request $request,
        User $user,
        Config $config,
        DatabaseDriver $db
    ): string {
        $request->abortIf(
            !$user->authorise('core.edit', 'com_templates'),
            403,
            'Insufficient User Rights.',
        );

        // get config values
        $values = Event::emit('config.save|filter', $request->getParam('config', []));

        // fetch current style params
        $params = $db
            ->setQuery(
                sprintf('SELECT params FROM #__template_styles WHERE id = %d', $config('theme.id')),
            )
            ->loadResult();

        // prepare style params
        $params =
            ['config' => json_encode($values, JSON_UNESCAPED_SLASHES)] +
            (json_decode($params, true) ?: []);

        // update style params
        $style = (object) [
            'id' => $config('theme.id'),
            'params' => json_encode($params, JSON_UNESCAPED_SLASHES),
        ];

        $db->updateObject('#__template_styles', $style, 'id');

        return 'success';
    }
}
