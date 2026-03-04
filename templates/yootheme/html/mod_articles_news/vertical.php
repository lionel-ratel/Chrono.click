<?php

defined('_JEXEC') or die;

use Joomla\CMS\Helper\ModuleHelper;

?>

<?php if ($list) : ?>
<ul class="uk-list uk-list-divider newsflash">
    <?php foreach ($list as $item) : ?>
    <li><?php include ModuleHelper::getLayoutPath('mod_articles_news', '_item') ?></li>
    <?php endforeach ?>
</ul>
<?php endif ?>
