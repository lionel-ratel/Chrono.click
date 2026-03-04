<?php
defined( '_JEXEC' ) or die;

// -- Initialize
require_once __DIR__.'/config.php';
$cck	=	CCK_Rendering::getInstance( $this->template );
if ( $cck->initialize() === false ) { return; }

$items		=	$cck->getItems();

if ( !count( $items ) ) {
	return;
}

$fieldnames	=	$cck->getFields( 'main', '', false );
$fieldnames	=	array_flip( $fieldnames );
$layout_id 	=	(int)$cck->getStyleParam( 'layout_id', 0 );
$builder	=	new JCckContentArticle;

if ( !( $layout_id && $builder->load( $layout_id )->isSuccessful() ) ) {
	echo 'No Layout Selected !';
	return;
}

$layout	=	new \YooSeblod\Integration\YooLayout( $builder->getProperty( 'json' ), $cck );
$model	=	$layout->getModel();

if ( $model === false ) {
	return;
}

$children	=	array();
$sources	=	$layout->getSources( $model['item'] );

foreach ( $items as $key => $item ) {
	$props		=	array();

	foreach ( $sources as $property => $str ) {
		if ( isset( $fieldnames[$str] ) ) {
			$props[$property]	=	$item->renderField( $str );
		} else {
			$props[$property]	=	$str;
		}
	}
	
	$children[]	=	(object)array(
						'type' => $model['item']->type,
						'props'=>(object)$props
					);	
}

$layout->setSources( $children, $model['type'] );

echo $layout->display();