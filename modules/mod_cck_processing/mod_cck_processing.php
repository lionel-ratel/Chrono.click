<?php
/**
* @version 			SEBLOD Toolbox 1.x
* @package			SEBLOD Toolbox Add-on for SEBLOD 3.x
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\Registry\Registry;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;

$show	=	$params->get( 'url_show', '' );
$hide	=	$params->get( 'url_hide', '' );
if ( $show && JCckDevHelper::matchUrlVars( $show ) === false ) {
	return;
}
if ( $hide && JCckDevHelper::matchUrlVars( $hide ) !== false ) {
	return;
}

$app		=	Factory::getApplication();
$data		=	'';
$processing	=	$params->get( 'processing', 0 );

Factory::getLanguage()->load( 'com_cck_default', JPATH_SITE );

if ( $processing ) {
	$processing			=	JCckDatabase::loadObject( 'SELECT id, scriptfile, options FROM #__cck_more_processings WHERE published = 1 AND id ='.(int)$processing );
	if ( is_object( $processing ) ) {
		if ( $processing->scriptfile != '' &&  is_file( JPATH_SITE.$processing->scriptfile ) ) {
			$options	=	new Registry( $processing->options );

			ob_start();
			include JPATH_SITE.$processing->scriptfile;
			$data		=	ob_get_clean();
		}
	}
}

if ( JCck::is( '4' ) ) {
	$raw_rendering	=	$params->get( 'raw_rendering', JCck::getConfig_Param( 'raw_rendering', '1' ) );
} else {
	$raw_rendering	=	$params->get( 'raw_rendering', 0 );

	if ( $raw_rendering == '' ) {
		$raw_rendering	=	0;
	}
}
$moduleclass_sfx	=	htmlspecialchars( $params->get( 'moduleclass_sfx' ) );
$class_sfx			=	( $params->get( 'force_moduleclass_sfx', 0 ) == 1 ) ? $moduleclass_sfx : '';
require ModuleHelper::getLayoutPath( 'mod_cck_processing', $params->get( 'layout', 'default' ) );
?>