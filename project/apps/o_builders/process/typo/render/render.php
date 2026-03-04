<?php
use YooSeblod\Integration;

defined( '_JEXEC' ) or die;

if ( !isset( $options ) ) {
	return false;
}

$builder	=	new \YooSeblod\Integration\YooLayout( $value );
$typo		=	$builder->render();