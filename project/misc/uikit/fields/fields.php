<?php
use YooSeblod\Integration;

defined( '_JEXEC' ) or die;

if ( !isset( $options ) ) {
    return false;
}

if ( $context !== 'com_cck.field' ) {
    return;
}

YooSeblod\Integration\YooUikit::initField( $item );
?>