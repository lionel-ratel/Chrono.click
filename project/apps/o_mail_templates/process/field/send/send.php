<?php
defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;

if ( !isset( $options ) ) {
	return false;
}

if ( !$config['pk'] ) {
	$config['message']			=	'An Error Has Occurred';
	$config['message_style']	=	'error';

	return false;
}

$content_free 	=	new JCckContentFree;
$content_free->setTable( '#__cck_store_form_o_mail_template' );

$content_free->load( $config['pk'] );

$html	=	'<div class="o-container o-grid o-rowgap-16">'
		.	'<pre>'.htmlspecialchars( $content_free->getProperty( 'subject' ) ).'</pre>'
		.	'<pre>'.htmlspecialchars( $content_free->getProperty( 'body' ) ).'</pre>'
		.	'</div>'
		;

echo $html;

$to		=	Factory::getUser()->email;
$cc		=	$content_free->getProperty( 'cc', '' );
$cc		=	explode( ';', $cc );

if ( strpos( $to, '@octopoos.' ) !== false ) {
	echo '<div class="o-container"><em>Email sent to '.$to.'</em></div>';

	require_once JPATH_SITE.'/project/helper.php';

	ProjectHelper::sendEmail(
		$to,
		$content_free->getProperty( 'subject' ),
		$content_free->getProperty( 'body' ),
		null,
		'espaceclient@absyscyborg.com',
		$cc
	);
}
?>