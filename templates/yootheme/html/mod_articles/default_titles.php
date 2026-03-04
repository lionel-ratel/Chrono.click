<?php

defined('_JEXEC') or die;

?>
<ul class="mod-articles uk-nav uk-nav-default">
    <?php foreach ($items as $item) : ?>
        <li <?= $item->active ? 'class="uk-active"' : '' ?> itemscope itemtype="https://schema.org/Article">
            <a href="<?= $item->link; ?>" itemprop="url">
                <span itemprop="name">
                    <?= $item->title ?>
                </span>
            </a>
        </li>
    <?php endforeach ?>
</ul>
