<?php
defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;

require_once JPATH_SITE.'/project/helper.php';

$app			=	Factory::getApplication();
$restriction 	=	false;

if ( !ProjectHelper::isOffline() ) {
	return;
}

if ( JCck::isSite( true, 'dev' ) ) {
	return;
}

$restriction	=	true;
?>