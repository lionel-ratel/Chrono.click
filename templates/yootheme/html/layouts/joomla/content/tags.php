<?php

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\Component\Tags\Site\Helper\RouteHelper;
use Joomla\Registry\Registry;

?>
<?php if (!empty($displayData)) : ?>
    <?php foreach ($displayData as $i => $tag) : ?>
        <?php if (in_array($tag->access, Factory::getApplication()->getIdentity()->getAuthorisedViewLevels())) : ?>
            <?php $seperator = $i !== array_key_last($displayData) ? ',' : '' ?>
            <?php $tagParams = new Registry($tag->params) ?>
            <?php $tagClass = trim(str_replace(['label-info', 'label'], '', $tagParams->get('tag_link_class', ''))) ?>
            <a href="<?= Route::_(RouteHelper::getComponentTagRoute($tag->tag_id . ':' . $tag->alias)) ?>" class="<?= $tagClass ?>" property="keywords" vocab="https://schema.org/"><?= $this->escape($tag->title) ?></a><?= $seperator ?>
        <?php endif ?>
    <?php endforeach ?>
<?php endif ?>
