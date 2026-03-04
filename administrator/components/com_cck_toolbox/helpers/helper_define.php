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

use Joomla\CMS\Language\Text;

if ( is_file( JPATH_COMPONENT.'/_VERSION.php' ) ) {
	require_once JPATH_COMPONENT.'/_VERSION.php';
	$version	=	new JCckToolboxVersion;
}

// -------- -------- -------- -------- -------- -------- -------- -------- // Core

define( 'CCK_VERSION', 			( isset( $version ) && is_object( $version ) ) ? $version->getShortVersion() : '1.x' );
define( 'CCK_NAME',				'cck_toolbox' );
define( 'CCK_TITLE',			'CCK_TOOLBOX' );
define( 'CCK_ADDON',			'com_'.CCK_NAME );
define( 'CCK_LABEL',			Text::_( CCK_ADDON.'_ADDON' ) );
define( 'CCK_COM',				'com_cck' );
define( 'CCK_MODEL',			CCK_TITLE.'Model' );
define( 'CCK_TABLE',			'CCK_Table' );
define( 'CCK_WEBSITE',			'https://www.seblod.com' );

define( '_C1_NAME',				'items' );
define( '_C2_NAME',				'plugins' );
define( '_C3_NAME',				'processings' );
define( '_C4_NAME',				'jobs' );

define( '_C1_TEXT',				'COM_CCK_TOOLBOX_ITEM' );
define( '_C2_TEXT',				'COM_CCK_TOOLBOX_PLUGIN' );
define( '_C3_TEXT',				'COM_CCK_TOOLBOX_PROCESSING' );
define( '_C4_TEXT',				'COM_CCK_TOOLBOX_JOB' );

define( 'CCK_LINK',				'index.php?option=com_'.CCK_NAME );
define( '_C1_LINK',				CCK_LINK.'&view='._C1_NAME );
define( '_C2_LINK',				CCK_LINK.'&view='._C2_NAME );
define( '_C3_LINK',				CCK_LINK.'&view='._C3_NAME );
define( '_C4_LINK',				CCK_LINK.'&view='._C4_NAME );
?>