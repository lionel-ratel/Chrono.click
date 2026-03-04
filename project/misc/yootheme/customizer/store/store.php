<?php
defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use YooSeblod\Integration;

if ( !isset( $options ) ) {
	return false;
}

require_once JPATH_SITE.'/project/helper.php';

$app		=	Factory::getApplication();
$base		=	Uri::getInstance()->toString( array( 'scheme', 'user', 'pass', 'host', 'port' ) );
$cck		=	$app->input->getString( 'cck', '' );
$pk			=	$app->input->getInt( 'pk', 0 );
$back		=	$app->input->getString( 'back', '' );

if ( !( $cck && $pk && $back ) ) {
	return;
}

$content    =   new JCckContentArticle;


if ( $content->load( $pk )->isSuccessful() ) {
	$json	=	trim( trim( $content->getProperty( 'fulltext' ) ), "\xEF\xBB\xBF" );
	$json	=	trim( str_replace( array( '<!--', '-->' ), '', $json ) );	

	$content->setOptions( array( 'check_permissions'=>0, 'event_triggers'=>0 ) );
	$content->setProperty( 'json', $json )
			->setProperty( 'introtext', '::cck::'.$content->getId().'::/cck::' )
			->setProperty( 'fulltext', '::cck::'.$content->getId().'::/cck::' )
			->store();
}

$app->redirect( base64_decode( $back ) );
?>