<?php

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

?>

<?php if (!$showall) :

    $title = Text::sprintf('JLIB_HTML_PAGE_CURRENT_OF_TOTAL', $page + 1, count($list) - 1);

    // Show title only if it's a custom title, and not e.g. Page 2
    if ($page && $list[$page + 1]->title != Text::sprintf('JLIB_HTML_PAGE_CURRENT', $page + 1)) {
        $title .= ': ' . htmlspecialchars($list[$page + 1]->title, ENT_QUOTES, 'UTF-8');
    }

?>
<p class="uk-text-meta tm-page-break <?= ($page == '0') ? 'tm-page-break-first-page' : '' ?>"><?= $title ?></p>
<?php else : ?>
<div class="tm-page-break-remove-br"></div>
<?php endif ?>
