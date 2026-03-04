<?php

namespace YOOtheme;

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use YOOtheme\Builder\Templates\TemplateHelper;

/**
 * @var Config $config
 * @var View $view
 * @var TemplateHelper $templates
 */
[$config, $view, $templates] = app(Config::class, View::class, TemplateHelper::class);

$liveSearch = preg_match('/^(logo|navbar|header)/', $module->position) && $templates->match([
        'type' => '_search',
        'query' => ['lang' => $app->getDocument()->language],
    ]);
$autosuggest = !$liveSearch && $params->get('show_autosuggest', 1);

$fields = [[
    'tag' => 'input',
    'name' => 'q',
    'class' => $autosuggest ? ['js-finder-search-query'] : [],
    'value' => $app->getInput()->getCmd('option') === 'com_finder' ? $app->getInput()->getString('q', '') : false,
    'placeholder' => Text::_('TPL_YOOTHEME_SEARCH'),
    'required' => true,
    'aria-label' => Text::_('TPL_YOOTHEME_SEARCH'),
]];

$uri = Uri::getInstance(Route::_($route));
$uri->delVar('q');

$query = $uri->getQuery(true);

// Search Filter
if ($filter = $config('~theme.com_finder_filter')) {
    $query += ['f' => $filter];
}

// Create hidden input elements for each part of the URI.
foreach ($query as $name => $value) {
    $fields[] = ['tag' => 'input', 'type' => 'hidden', 'name' => $name, 'value' => $value];
}

echo $view('~theme/templates/search', [

    'position' => $module->position,

    'tag' => $module->attrs,

    'attrs' => [
        'action' => Route::_($route),
        'method' => 'get',
        'role' => 'search',
        'class' => ['js-finder-searchform'],
    ],

    'fields' => $fields,

    'iconClass' => [
        'uk-position-z-index' => $autosuggest, // Needed because of `awesomplete` HTML class has a `z-index`
    ],

    'language' => $app->getDocument()->language,

]);

// This segment of code sets up the autocompleter.
if ($autosuggest) {
    $document = $app->getDocument();

    $assetManager = $document->getWebAssetManager();
    $assetManager->usePreset('awesomplete');
    $assetManager->disableStyle('awesomplete');
    $assetManager->getRegistry()->addExtensionRegistryFile('com_finder');
    $assetManager->useScript('com_finder.finder');

    Text::script('JLIB_JS_AJAX_ERROR_OTHER');
    Text::script('JLIB_JS_AJAX_ERROR_PARSE');

    $document->addScriptOptions('finder-search', ['url' => Route::_('index.php?option=com_finder&task=suggestions.suggest&format=json&tmpl=component')]);
}
