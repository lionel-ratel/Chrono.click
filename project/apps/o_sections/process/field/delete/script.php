<?php
defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;

$app	=	Factory::getApplication();
$pk		=	$app->input->getInt( 'id', 0 );

if ( !$pk ) {
	return;
}

// require_once JPATH_SITE.'/project/helper.php';

$content_free	=	new JCckContentFree;

$content_free->setTable( '#__cck_store_form_o_section' );
$content_free->setOptions( array( 'check_permissions'=>0 ) );
$content_free->delete( $pk );

echo true;
?>