<?php
defined( '_JEXEC' ) or die;

if ( !( $context == 'com_templates.style' && $table->template == 'yootheme' ) ) {
	return;
}

$query			=	'SELECT custom_data FROM #__extensions'
				.	' WHERE type="plugin" AND element="yootheme" AND folder="system"';

$custom_data	=	json_decode( JCckDatabase::loadResult( $query ) );
$templates		=	$custom_data->templates;

$added		=	array();
$existings	=	array();
$items		=	JCckDatabase::loadObjectList( 'SELECT id, code FROM #__cck_store_form_o_builder' );

foreach ( $items as $item ) {
	$existings[$item->code]	=	$item->id;
}

$content_free	=	new JCckContentFree;
$content_free->setTable( '#__cck_store_form_o_builder' );
$content_free->setOptions( array( 'check_permissions'=>0 ) );

foreach ( $templates as $key => $template ) {
	if ( $content_free->find( 'o_builder', array( 'code'=>$key ) )->loadOne()->isSuccessful() ) {
		$content_free->setProperty( 'json', json_encode( $template ) )
					 ->setProperty( 'title', $template->name )
					 ->store();
	} else {
		$data	=	array(
						'code'=>$key,
						'json'=>json_encode( $template ), 
						'title'=>$template->name,
						'type'=>'template',
						'language'=>'*',
						'published'=>1
					);

		$content_free->create( 'o_builder', $data );
	}

	if ( $content_free->isSuccessful() ) {
		if ( isset( $existings[$key] ) ) {
			unset( $existings[$key] );
		}
	}
}

// Archived
if ( count( $existings ) ) {
	foreach ( $existings as $key => $value ) {
		if ( $content_free->load( $value )->isSuccessful() ) {
			$content_free->setProperty( 'published', 2 )->store();
		}
	}
}
?>