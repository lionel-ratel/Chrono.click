<?php
defined( '_JEXEC' ) or die;

use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

if ( !isset( $options ) ) {
	return;
}

$item_id	=	JCckDatabase::loadResult( 'SELECT id FROM #__menu WHERE language="*" AND home=1' );
$base		=	Uri::getInstance()->toString( array( 'scheme', 'user', 'pass', 'host', 'port' ) );
$live 		=	base64_encode( $base.Route::_( 'index.php?Itemid='.$item_id ) );
?>