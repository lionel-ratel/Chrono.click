<?php

namespace YOOtheme;

defined('_JEXEC') or die;

/** @var View $view */
$view = app(View::class);

$attrs_container = [];

$attrs_container['class'][] = 'mod-articles-items';
$attrs_container['class'][] = $params->get('moduleclass_sfx', '');

$columns = $params->get('articles_layout') == 1 ? $params->get('layout_columns') : 1;
$attrs_container['uk-grid'] = true;
$attrs_container['class'][] = "uk-child-width-1-$columns";

// Article template
$article = fn($item) => $view('~theme/templates/article{-blog,}', [
    'layout' => 'blog',
    'article' => $item,
    'content' => $item->introtext,
    'image' => 'intro',
    'columns' => $columns,
]);

?>

<ul <?= $view->attrs($attrs_container) ?>>
    <?php foreach ($items as $item) : ?>
        <?php
            $item->params->set('show_author', $params->get('show_author'));
            $item->params->set('show_category', $params->get('show_category'));
            $item->params->set('link_category', $params->get('show_category_link'));
            $item->params->set('show_publish_date', $params->get('show_date'));
            $item->params->set('show_hits', $params->get('show_hits'));
            $item->params->set('show_tags', $params->get('show_tags'));
            $item->params->set('show_readmore', $params->get('show_readmore'));
            $item->params->set('show_readmore_title', $params->get('show_readmore_title'));
            $item->params->set('link_titles', $params->get('link_titles'));
            //$item->params->set('meta_style', $params->get('info_layout') == 1 ? 'list' : '');   // can't be overridden, the blog settings always win
            $item->introtext = $params->get('show_introtext') ? $item->introtext : '';
        ?>

        <li>
            <?= $article($item) ?>
        </li>
    <?php endforeach ?>
</ul>
