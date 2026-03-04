<?php

namespace YOOtheme;

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;

$baseUrl = Uri::getInstance();
$prefix = "~theme.modules.{$module->id}";

?>

<div class="uk-panel mod-languages">

    <?php if ($headerText) : ?>
    <div class="uk-margin"><?= $headerText ?></div>
    <?php endif ?>

    <?php if ($params->get('dropdown', 0)) :

        $is_header = in_array($module->position, ['navbar', 'navbar-push','navbar-mobile', 'header', 'header-split', 'header-mobile', 'logo', 'logo-mobile']);

        ?>

        <ul class="uk-subnav" uk-dropnav="mode: click; boundary: !.uk-container; container: body; <?= $is_header ? 'align: right;' : '' ?>">
            <li>

                <?php foreach ($list as $language) : ?>
                <?php if ($language->active) :

                    [$config, $view] = app(Config::class, View::class);

                    $icon = '';
                    if ($config("{$prefix}.language_icon")) {

                        $icon = $view->el('image', [
                            'src' => '~assets/images/language.svg',
                            'alt' => true,
                            'loading' => false,
                            'width' => $config("{$prefix}.language_icon_width") ?: 20,
                            'height' => $config("{$prefix}.language_icon_width") ?: 20,
                            'uk-svg' => true,
                            'thumbnail' => true,
                        ])();

                    } elseif ($params->get('dropdownimage', 1) && $language->image) {
                        $icon = HTMLHelper::image('mod_languages/' . $language->image . '.gif', $params->get('full_name') ? '' : $language->title_native, null, true);
                    }

                    ?>
                <a href>

                    <?php if ($icon) : ?>
                        <?= $icon ?>
                        <span class="uk-text-middle <?= $config("{$prefix}.language_icon_margin") ? 'uk-margin-xsmall-left' : '' ?>">
                    <?php endif ?>

                        <?= $params->get('full_name', 1) ? $language->title_native : strtoupper($language->sef) ?>

                    <?php if ($icon) : ?>
                        </span>
                    <?php endif ?>

                    <?php if ($config("{$prefix}.language_parent_icon")) : ?>
                    <span uk-drop-parent-icon></span>
                    <?php endif ?>

                </a>
                <?php endif ?>
                <?php endforeach ?>

                <div class="uk-dropdown" style="min-width: auto; width: max-content;">
                    <ul class="uk-nav uk-dropdown-nav">
                        <?php foreach ($list as $language) : ?>
                            <?php if (!$language->active || $params->get('show_active', 1)) : ?>
                            <li <?php // $language->active ? 'class="uk-active"' : '' ?>>
                                <a href="<?= htmlspecialchars_decode(htmlspecialchars(!$language->active ? $language->link : $baseUrl, ENT_QUOTES, 'UTF-8'), ENT_NOQUOTES) ?>">
                                    <?php if ($params->get('dropdownimage', 1) && $language->image) : ?>
                                        <?= HTMLHelper::image('mod_languages/' . $language->image . '.gif', $params->get('full_name') ? '' : $language->title_native, 'class="uk-margin-xsmall-right"', true) ?>
                                    <?php endif ?>
                                    <?= $params->get('full_name', 1) ? $language->title_native : strtoupper($language->sef) ?>
                                </a>
                            </li>
                            <?php endif ?>
                        <?php endforeach ?>
                    </ul>
                </div>

            </li>
        </ul>

    <?php else : ?>

        <ul class="<?= $params->get('inline', 1) ? 'uk-subnav' : 'uk-nav uk-nav-default' ?>">
            <?php foreach ($list as $language) : ?>
                <?php if (!$language->active || $params->get('show_active', 1)) : ?>
                <li <?php // $language->active ? 'class="uk-active"' : '' ?>>
                    <a href="<?= htmlspecialchars_decode(htmlspecialchars(!$language->active ? $language->link : $baseUrl, ENT_QUOTES, 'UTF-8'), ENT_NOQUOTES) ?>">
                        <?php if ($params->get('image', 1)) : ?>
                            <?php if ($language->image) : ?>
                                <?= HTMLHelper::image('mod_languages/' . $language->image . '.gif', $language->title_native, ['title' => $language->title_native], true) ?>
                            <?php else : ?>
                                <?= strtoupper($language->sef) ?>
                            <?php endif ?>
                        <?php else : ?>
                            <?= $params->get('full_name', 1) ? $language->title_native : strtoupper($language->sef) ?>
                        <?php endif ?>
                    </a>
                </li>
                <?php endif ?>
            <?php endforeach ?>
        </ul>

    <?php endif ?>

    <?php if ($footerText) : ?>
    <div class="uk-margin"><?= $footerText ?></div>
    <?php endif ?>

</div>
