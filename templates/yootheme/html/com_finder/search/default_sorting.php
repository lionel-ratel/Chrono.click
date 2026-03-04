<?php

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

/** @var \Joomla\Component\Finder\Site\View\Search\HtmlView $this */

$activeField = array_values(array_filter($this->sortOrderFields, function ($sortOrderField) {
    return $sortOrderField->active;
}))[0];
?>

<div class="uk-margin uk-margin-medium-bottom">
    <button type="button" class="uk-button uk-button-default"><?= Text::_('COM_FINDER_SORT_BY') ?> <?= $this->escape($activeField->label) ?></button>
    <div uk-dropdown="mode: click">
        <ul class="uk-nav uk-dropdown-nav">
        <?php foreach ($this->sortOrderFields as $sortOrderField) : ?>
            <li<?= $sortOrderField->active ? ' class="uk-active"' : '' ?>>
                <a href="<?= Route::_($sortOrderField->url) ?>">
                    <?= $this->escape($sortOrderField->label) ?>
                </a>
            </li>
        <?php endforeach ?>
        </ul>
    </div>
</div>
