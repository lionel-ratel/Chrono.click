<?php

use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die;

if ($params->def('prepare_content', 1)) {
    $module->content = HTMLHelper::_('content.prepare', $module->content, '', 'mod_custom.content');
}

if (!$module->content) {
    return;
}

$image = $params->get('backgroundimage') ? HTMLHelper::image($params->get('backgroundimage'), null, [], false, 1) : false;

?>

<div class="uk-margin-remove-last-child custom" <?= $image ? " style=\"background-image:url({$image})\"" : '' ?>><?= $module->content ?></div>
