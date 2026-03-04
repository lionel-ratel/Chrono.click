<?php
defined( '_JEXEC' ) or die;

if ( !isset( $options ) ) {
	return;
}

$site	=	JCck::getSite();
$user	=	JFactory::getUser();

// Prepare
/*
if ( JCck::isGuest() ) {
*/
	$hosts	=	array(
					'501'=>'https://www.innio.com',
					'502'=>'https://careers.innio.com'
				);
/*
} else {
	$hosts	=	array(
					'501'=>'https://www.newprod.innio.com',
					'502'=>'https://careers.newprod.innio.com'
				);
}
*/

// Set
if ( strpos( $fields[$name]->link, '{site_url@501}' ) !== false ) {
	$fields[$name]->link	=	str_replace( '{site_url@501}', '', $fields[$name]->link );
	$fields[$name]->link	=	JRoute::_( $fields[$name]->link );
	$fields[$name]->link	=	$hosts['501'].$fields[$name]->link;
}
if ( strpos( $fields[$name]->link, '{site_url@502}' ) !== false ) {
	$fields[$name]->link	=	str_replace( '{site_url@502}', '', $fields[$name]->link );
	$fields[$name]->link	=	JRoute::_( $fields[$name]->link );

	if ( (int)$site->id !== 502 ) {
		$fields[$name]->link	=	$hosts['502'].$fields[$name]->link;
	}
}
?>