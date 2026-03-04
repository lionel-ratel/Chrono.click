<?php

namespace YOOtheme;

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;

$view = app(View::class);

$title_element = $params->get('item_heading', 'h4');

if ($params->get('img_intro_full') !== 'none' && !empty($item->imageSrc)) {
    $img = $view->image([$item->imageSrc, 'thumbnail' => true], ['loading' => 'lazy', 'alt' => $item->imageAlt]);
}

?>

<?php if ($params->get('item_title')) : ?>
<<?= $title_element ?>>
    <?php if ($params->get('link_titles') && $item->link != '') : ?>
        <a href="<?= $item->link ?>"><?= $item->title ?></a>
    <?php else : ?>
        <?= $item->title ?>
    <?php endif ?>
</<?= $title_element ?>>
<?php endif ?>

<?php if (isset($img)) : ?>
<div property="image" typeof="ImageObject" vocab="https://schema.org/">
    <meta property="url" content="<?= Uri::base() . HTMLHelper::cleanImageURL($item->imageSrc)->url ?>">
    <?= $img ?>
</div>
<?php endif ?>

<?php if (!$params->get('intro_only')) echo $item->afterDisplayTitle ?>

<?= $item->beforeDisplayContent ?>
<?= $item->introtext ?>
<?= $item->afterDisplayContent ?>

<?php if (isset($item->link) && $item->readmore && $params->get('readmore')) : ?>
<p><a class="uk-button uk-button-text" href="<?= $item->link ?>"><?= $item->linkText ?></a></p>
<?php endif ?>
