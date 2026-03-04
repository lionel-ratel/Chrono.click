<?php
use YooSeblod\Integration;

defined( '_JEXEC' ) or die;

if ( !isset( $options ) ) {
	return false;
}

$json	=	JCckDatabase::loadResult( 'SELECT json FROM #__cck_store_item_content WHERE section_type = "'.$config['type'].'"' );

$builder	=	new \YooSeblod\Integration\YooLayout( $json );
$typo		=	$builder->render();