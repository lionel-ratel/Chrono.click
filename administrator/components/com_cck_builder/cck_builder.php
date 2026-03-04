<?php
/**
* @version 			SEBLOD Builder 1.x
* @package			SEBLOD Builder Add-on for SEBLOD 3.x
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;

if ( ! Factory::getUser()->authorise( 'core.manage', 'com_cck_builder' ) ) {
	return JError::raiseWarning( 404, Text::_( 'JERROR_ALERTNOAUTHOR' ) );
}

$lang	=	Factory::getLanguage();
$lang->load( 'com_cck_default', JPATH_SITE );
$lang->load( 'com_cck_core' );

require_once JPATH_COMPONENT.'/helpers/helper_define.php';
require_once JPATH_COMPONENT.'/helpers/helper_display.php';
require_once JPATH_COMPONENT.'/helpers/helper_include.php';
require_once JPATH_COMPONENT.'/helpers/helper_admin.php';

$controller	=	BaseController::getInstance( 'CCK_Builder' );
$controller->execute( Factory::getApplication()->input->get( 'task' ) );
$controller->redirect();
?>