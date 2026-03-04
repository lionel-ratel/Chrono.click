<?php

namespace YOOtheme;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\AuthenticationHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;

$joomla = Factory::getApplication();
$extraButtons = AuthenticationHelper::getLoginButtons('form-login');

?>
<!DOCTYPE html>
<html lang="<?= $this->language ?>" dir="<?= $this->direction ?>">
    <head>
        <meta charset="<?= $this->getCharset() ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <jdoc:include type="head" />
    </head>
    <body class="uk-flex uk-flex-middle" uk-height-viewport>

        <main id="tm-main" class="tm-offline uk-container uk-container-small uk-text-center">

            <jdoc:include type="message" />

            <?php if ($joomla->get('offline_image', '')) : ?>
                <?= HTMLHelper::image($joomla->get('offline_image'), htmlspecialchars($joomla->get('sitename'), ENT_COMPAT, 'UTF-8')) ?>
            <?php endif ?>

            <h1><?= htmlspecialchars($joomla->get('sitename'), ENT_COMPAT, 'UTF-8') ?></h1>

            <?php if ($joomla->get('display_offline_message', 1) == 1 && str_replace(' ', '', $joomla->get('offline_message')) != '') : ?>

                <p><?= $joomla->get('offline_message') ?></p>

            <?php elseif ($joomla->get('display_offline_message', 1) == 2) : ?>

                <p><?= Text::_('JOFFLINE_MESSAGE') ?></p>

            <?php endif ?>

            <form class="uk-panel uk-margin" action="<?= Route::_('index.php') ?>" method="post">

                <div class="uk-margin">
                    <input class="uk-input" type="text" name="username" autocomplete="username" placeholder="<?= Text::_('JGLOBAL_USERNAME') ?>" aria-label="<?= Text::_('JGLOBAL_USERNAME') ?>">
                </div>

                <div class="uk-margin">
                    <input class="uk-input" type="password" name="password" autocomplete="current-password" placeholder="<?= Text::_('JGLOBAL_PASSWORD') ?>" aria-label="<?= Text::_('JGLOBAL_PASSWORD') ?>">
                </div>

                <?php foreach ($extraButtons as $button) :
                    $dataAttributeKeys = array_filter(array_keys($button), fn($key) => substr($key, 0, 5) == 'data-');
                    ?>
                    <div class="mod-login__submit form-group">
                        <button type="button" class="uk-button uk-button-secondary <?= $button['class'] ?? '' ?>"
                        <?php foreach ($dataAttributeKeys as $key) : ?>
                            <?= $key ?>="= $button[$key] ?>"
                        <?php endforeach ?>
                        <?php if ($button['onclick']) : ?>
                            onclick="<?= $button['onclick'] ?>"
                        <?php endif ?>
                        title="<?= Text::_($button['label']) ?>"
                        id="<?= $button['id'] ?>"
                        >
                        <?php if (!empty($button['icon'])) : ?>
                            <span class="<?= $button['icon'] ?>"></span>
                        <?php elseif (!empty($button['image'])) : ?>
                            <?= $button['image'] ?>
                        <?php elseif (!empty($button['svg'])) : ?>
                            <?= $button['svg'] ?>
                        <?php endif ?>
                        <?= Text::_($button['label']) ?>
                        </button>
                    </div>
                <?php endforeach ?>

                <div class="uk-margin">
                    <button class="uk-button uk-button-primary uk-width-1-1" type="submit" name="Submit"><?= Text::_('JLOGIN') ?></button>
                </div>

                <div class="uk-margin">
                    <label>
                        <input type="checkbox" name="remember" value="yes" placeholder="<?= Text::_('JGLOBAL_REMEMBER_ME') ?>">
                        <?= Text::_('JGLOBAL_REMEMBER_ME') ?>
                    </label>
                </div>

                <input type="hidden" name="option" value="com_users">
                <input type="hidden" name="task" value="user.login">
                <input type="hidden" name="return" value="<?= base64_encode(Uri::base()) ?>">
                <?= HTMLHelper::_('form.token') ?>

            </form>

        </main>

    </body>
</html>
