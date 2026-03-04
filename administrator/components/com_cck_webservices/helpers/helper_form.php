<?php
/**
* @version 			SEBLOD WebServices 1.x
* @package			SEBLOD WebServices Add-on for SEBLOD 3.x
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\CMS\HTML\HTMLHelper;

require_once JPATH_ADMINISTRATOR.'/components/com_cck/helpers/common/form.php';

// Helper
class Helper_Form extends CommonHelper_Form
{
	// getWebservicePlugins
	public static function getWebservicePlugins( &$field, $value, $name, $id, $config )
	{
		$field->label	=	'Type';
		$options		=	array();
		
		if ( trim( $field->selectlabel ) ) {
			$options	=	array( HTMLHelper::_( 'select.option',  '', '- '.$field->selectlabel.' -' ) );
		}

		$options	=	array_merge( $options, Helper_Admin::getPluginOptions( 'webservice', 'cck_', false, false, true ) );
		
		return HTMLHelper::_( 'select.genericlist', $options, $name, 'class="inputbox select" '.$field->attributes, 'value', 'text', $value, $id );
	}
}
?>