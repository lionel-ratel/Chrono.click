<?php
defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;

require_once JPATH_SITE.'/project/helper.php';

$app    	=   Factory::getApplication();
$base		=	Uri::getInstance()->toString( array( 'scheme', 'user', 'pass', 'host', 'port' ) );
$cck		=	$app->input->getString( 'cck', '' );
$pk			=	$app->input->getInt( 'pk', 0 );
$back		=	$app->input->getString( 'back', '' );

if ( !( $cck && $pk && $back ) ) {
	return;
}

$site		=	JCck::getSite();
$site_conf	=	( is_object( $site ) ) ? new Registry( $site->configuration ) : new Registry;

$content    =   new JCckContentArticle;

if ( !$content->load( $pk )->isSuccessful() ) {
	$app->redirect( base64_decode( $back ) );
	return;
}

$content->setOptions( array( 'event_triggers'=>0 ) );

$json	=	trim( trim( $content->getProperty( 'json' ) ), "\xEF\xBB\xBF" );

if ( !( str_starts_with( $json, '<!--' ) && str_ends_with( $json, '-->' ) ) ) {
	$json	=	'<!-- '.$json.' -->';
}

$content->setProperty( 'fulltext', $json )->store();

$url_site	=	$base
			.	ProjectHelper::getUrl( 'nav_items', 'customizer-edit' )
			.	'/'.$content->getProperty( 'alias' );

// Customizer Store
$url_return	=	$base.str_replace( '?view=processing', '', ProjectHelper::getUrl( 'nav_items', 'customizer-store' ) )
			.	'?cck='.$cck.'&pk='.$pk.'&back='.$back;

// Redirect to customizer
$query		=	array(		
					'f=1',		
					'p=customizer',
					'templateStyle='.$site_conf->get( 'template_style' ),
					'format=html',
					'site='.rawurlencode( $url_site ),
					'return='.rawurlencode( $url_return )
				);

$app->redirect( $base.'/component/ajax/?'.implode( '&', $query ) );
?>