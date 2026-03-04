<?php
defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

$app		=	Factory::getApplication();
$item_id	=	$app->input->getInt( 'Itemid', 0 );

/* Againt attacks */
if ( $app->input->getMethod() == 'POST' && $app->input->get( 'view' ) == 'registration' ) {
	die;
}
/* End */

if ( $app->isClient( 'administrator' ) ) {
	return;
}

//
$uri		=	Uri::getInstance();
$base		=	$uri->toString( array( 'scheme', 'user', 'pass', 'host', 'port' ) );
$path		=	$uri->getPath();
$query		=	$uri->getQuery();

if ( strpos( $query, 'option=com_jdump' ) !== false ) {
	return;
}

/*
require_once __DIR__.'/helper.php';

if ( strpos( $path, '/component/users/login' ) !== false ) {
	if ( !$item_id ) {
		$item_id	=	146;	// To Improve
	}

	ProjectHelper::redirect( $base.Route::_( 'index.php?Itemid='.(string)$item_id ) );
	return;
}
*/
?>