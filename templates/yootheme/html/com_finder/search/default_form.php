<?php

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

/** @var \Joomla\Component\Finder\Site\View\Search\HtmlView $this */

// This segment of code sets up the autocompleter.
if ($this->params->get('show_autosuggest', 1))
{
    $this->getDocument()->getWebAssetManager()
        ->usePreset('awesomplete')
        ->disableStyle('awesomplete');

    Text::script('JLIB_JS_AJAX_ERROR_OTHER');
    Text::script('JLIB_JS_AJAX_ERROR_PARSE');

    $this->getDocument()->addScriptOptions('finder-search', ['url' => Route::_('index.php?option=com_finder&task=suggestions.suggest&format=json&tmpl=component')]);
}

?>
<form action="<?= Route::_($this->query->toUri()) ?>" method="get" class="form-inline js-finder-searchform">

    <?= $this->getFields() ?>

    <fieldset class="word">
        <div class="uk-grid-small" uk-grid>
            <div class="uk-width-expand@s">

                <div class="uk-search uk-search-default uk-width-1-1">
                    <input id="q" class="uk-search-input<?= $this->params->get('show_autosuggest', 1) ? ' js-finder-search-query' : ''?>" type="text" name="q" placeholder="<?= Text::_('TPL_YOOTHEME_SEARCH') ?>" size="30" value="<?= $this->escape($this->query->input) ?>" aria-label="<?= Text::_('TPL_YOOTHEME_SEARCH') ?>">
                </div>

            </div>
            <div class="uk-width-auto@s">

                <div class="uk-grid-small" uk-grid>
                    <div class="uk-width-auto@s">
                        <button name="Search" type="submit" class="uk-button uk-button-primary uk-width-1-1"><?= Text::_('JSEARCH_FILTER_SUBMIT') ?></button>
                    </div>
                    <?php if ($this->params->get('show_advanced', 1)) : ?>
                        <div class="uk-width-auto@s"><a href="#advancedSearch" uk-toggle="target: #advancedSearch" class="uk-button uk-button-default uk-width-1-1"><?= Text::_('COM_FINDER_ADVANCED_SEARCH_TOGGLE') ?></a></div>
                    <?php endif ?>
                </div>

            </div>
        </div>
    </fieldset>

    <?php if ($this->params->get('show_advanced', 1)) : ?>
        <div id="advancedSearch" class="uk-margin js-finder-advanced" <?php if (!$this->params->get('expand_advanced', 0)) echo ' hidden' ?>>

            <?php if ($this->params->get('show_advanced_tips', 1)) : ?>
            <div>
                <?= Text::_('COM_FINDER_ADVANCED_TIPS_INTRO') ?>
                <?= Text::_('COM_FINDER_ADVANCED_TIPS_AND') ?>
                <?= Text::_('COM_FINDER_ADVANCED_TIPS_NOT') ?>
                <?= Text::_('COM_FINDER_ADVANCED_TIPS_OR') ?>
                <?php if ($this->params->get('tuplecount', 1) > 1) : ?>
                    <?= Text::_('COM_FINDER_ADVANCED_TIPS_PHRASE') ?>
                <?php endif ?>
                <?= Text::_('COM_FINDER_ADVANCED_TIPS_OUTRO') ?>
            </div>
            <?php endif ?>
            <div id="finder-filter-window">
                <?= HTMLHelper::_('filter.select', $this->query, $this->params) ?>
            </div>

        </div>
    <?php endif ?>

</form>
