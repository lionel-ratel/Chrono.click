<?php
defined( '_JEXEC' ) or die;

if ( isset( $config['client_form'] ) && $config['isNew'] ) {
	return;
}

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;

require_once JPATH_SITE.'/project/helper.php';

if ( !isset( $options ) ) {
	return;
}

$app    	=   Factory::getApplication();
$uri		=	Uri::getInstance();
$base		=	$uri->toString( array( 'scheme', 'user', 'pass', 'host', 'port' ) );
$current	=	Uri::current();
$query		=	$uri->getQuery();

if ( $query !== '' ) {
	$current	.=	'?'.$query;
}

//
$query		=	array(
					'cck='.$config['type'],
					'pk='.$config['pk'],
					'back='.base64_encode( $current )
				);
$url		=	$base
			.	str_replace( '?view=processing', '', ProjectHelper::getUrl( 'nav_items', 'customizer-open' ) )
			.	'?'.implode( '&', $query );

// Set
$field->display	=	1;
$button			=	'<a href="'.$url.'"><span uk-icon="yootheme" class="uk-icon-button uk-text-success"></span></a>';
$value			=	$button;

if ( isset( $config['client_form'] ) ) {
	$form	=	str_replace( '<a', '<a id="btn-customizer"', $button ) ;

	Factory::getDocument()->addScript( str_replace( JPATH_SITE, '', __DIR__.'/script.js' ) );
}
?>