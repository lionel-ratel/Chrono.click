<?php

defined('_JEXEC') or die;

use Joomla\CMS\Helper\ModuleHelper;

?>

<?php if ($list) : ?>
<ul class="newsflash uk-child-width-1-<?= count($list) ?>@m" uk-grid>
    <?php foreach ($list as $item) : ?>
    <li><?php include ModuleHelper::getLayoutPath('mod_articles_news', '_item') ?></li>
    <?php endforeach ?>
</ul>
<?php endif ?>
