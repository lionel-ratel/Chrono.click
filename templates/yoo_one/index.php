<?php
defined( '_JEXEC' ) or die;

// -- Initialize
require_once __DIR__.'/config.php';
$cck	=	CCK_Rendering::getInstance( $this->template );
if ( $cck->initialize() === false ) { return; }

// -- Prepare
$layout_id 		=	$cck->getStyleParam( 'layout_template', '' );

if ( is_string( $layout_id ) && $layout_id === 'inherit' ) {
	$data	=	$cck->getFields( 'layout', '', false );

	if ( !isset( $data[0] ) ) {
		echo 'No Layout Selected !';
		return;
	}

	$json	=	$cck->getValue( $data[0] );

	if ( $json === '' ) {
		echo 'No Layout Selected !';
		return;
	}
} else {
	if ( !$layout_id ) {
		echo 'No Layout Selected !';
		return;
	}

	$json	=	JCckDatabase::loadResult( 'SELECT json FROM #__cck_store_item_content WHERE id='.(int)$layout_id );

	if ( $json === '' ) {
		echo 'No Layout Selected !';
		return;
	}
}

$template	=	new \YooSeblod\Integration\YooLayout( $json, $cck );

if ( is_object( $template ) ) {
	echo $template->display( transformNode: true );
}