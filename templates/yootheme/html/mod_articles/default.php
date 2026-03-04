<?php

namespace YOOtheme;

defined('_JEXEC') or die;

use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Language\Text;

if (!$list) {
    return;
}

$groupHeading = 'h4';

if ($module->showtitle) {
    $modTitle = $params->get('header_tag');

    if ($modTitle == 'h1') {
        $groupHeading = 'h2';
    } elseif ($modTitle == 'h2') {
        $groupHeading = 'h3';
    }
}

$layoutSuffix = $params->get('title_only', 0) ? '_titles' : '_items';

?>

<?php if ($grouped) : ?>
    <?php foreach ($list as $groupName => $items) : ?>
        <div class="mod-articles-group">
            <<?= $groupHeading ?>><?= Text::_($groupName) ?></<?= $groupHeading ?>>
            <?php require ModuleHelper::getLayoutPath('mod_articles', $params->get('layout', 'default') . $layoutSuffix) ?>
        </div>
    <?php endforeach ?>
<?php else : ?>
    <?php $items = $list ?>
    <?php require ModuleHelper::getLayoutPath('mod_articles', $params->get('layout', 'default') . $layoutSuffix) ?>
<?php endif;
