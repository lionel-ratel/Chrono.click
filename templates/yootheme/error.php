<?php

// no direct access
defined('_JEXEC') or die;

/** @var Joomla\CMS\Document\ErrorDocument $this */

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\Event\Event;
use YOOtheme\File;

$error = $this->error->getCode();
$message = htmlspecialchars($this->error->getMessage(), ENT_QUOTES, 'UTF-8');

if ($error == 404) {
    Factory::getApplication()->getDispatcher()->dispatch('onLoad404', new Event('onLoad404'));
}

$this->setBase(Uri::root());
$this->setMetaData('viewport', 'width=device-width, initial-scale=1');
$this->setTitle($error . ' - ' . $message);

if (!class_exists(File::class)) {
    $wa = $this->getWebAssetManager();
    $wa->registerAndUseStyle('template.system.error', 'media/system/css/system-site-error.css');

    if ($this->direction == 'rtl') {
        $wa->registerAndUseStyle('template.system.error_rtl', 'media/system/css/system-site-error_rtl.css');
    }
}
?>
<!DOCTYPE html>
<html lang="<?= $this->language ?>" dir="<?= $this->direction ?>">
    <head>
        <jdoc:include type="metas" />
        <jdoc:include type="styles" />
        <jdoc:include type="scripts" />
    </head>
    <body>

        <div class="tm-page">
            <main id="tm-main">

                <?php if ($buffer = $this->getBuffer('component')) : ?>
                <?= $buffer ?>
                <?php else : ?>

                <div class="uk-section uk-section-default uk-flex uk-flex-center uk-flex-middle uk-text-center" uk-height-viewport>
                    <div>
                        <h1 class="uk-heading-xlarge"><?= $error ?></h1>
                        <p class="uk-h3"><?= $message ?></p>
                        <a class="uk-button uk-button-primary" href="<?= $this->baseurl ?>/index.php"><?= Text::_('JERROR_LAYOUT_HOME_PAGE') ?></a>

                        <?php if ($this->debug) : ?>
                        <div class="uk-margin-large-top">
                            <?= $this->renderBacktrace() ?>

                            <?php if ($this->error->getPrevious()) : ?>

                                <?php $loop = true ?>

                                <?php $this->setError($this->_error->getPrevious()) ?>

                                <?php while ($loop === true) : ?>
                                    <p><strong><?= Text::_('JERROR_LAYOUT_PREVIOUS_ERROR') ?></strong></p>
                                    <p>
                                        <?= htmlspecialchars($this->_error->getMessage(), ENT_QUOTES, 'UTF-8') ?>
                                        <br/><?= htmlspecialchars($this->_error->getFile(), ENT_QUOTES, 'UTF-8') ?>: <?= $this->_error->getLine() ?>
                                    </p>
                                    <?= $this->renderBacktrace() ?>
                                    <?php $loop = $this->setError($this->_error->getPrevious()) ?>
                                <?php endwhile ?>

                                <?php $this->setError($this->error) ?>

                            <?php endif ?>
                        </div>
                        <?php endif ?>

                    </div>
                </div>

                <?php endif ?>

            </main>
        </div>

    </body>
</html>
