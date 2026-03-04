<?php
defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;

if ( !isset( $options ) ) {
    return false;
}

$app    =   Factory::getApplication();

if ( !$app->isClient( 'administrator' ) ) {
    return;
}

$css    =   'ul.sortable li.t-uikit_tabs {background-color: #aac952;}'
        .   'select.inputbox{min-width: 100px;}'
        ;

Factory::getDocument()->addStyleDeclaration( $css );
?>