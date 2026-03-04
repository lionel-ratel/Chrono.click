<?php
/**
* @version 			SEBLOD 3.x More
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;

// Plugin
class plgCCK_Field_ValidationCck_Processing extends JCckPluginValidation
{
	protected static $type	=	'cck_processing';
	protected static $regex	=	'';
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare

	// onCCK_Field_ValidationPrepareForm
	public static function onCCK_Field_ValidationPrepareForm( &$field, $fieldId, &$config )
	{
		if ( self::$type != $field->validation ) {
			return;
		}

		$name		=	'processing_'.$fieldId;
		$validation	=	parent::g_getValidation( $field->validation_options );

		if ( !$validation->processing ) {
			return;
		}

		$processing	=	JCckDatabaseCache::loadObject( 'SELECT scriptfile, options FROM #__cck_more_processings WHERE id = '.(int)$validation->processing.' AND published = 1' );

		if ( !( is_object( $processing ) && $processing->scriptfile && is_file( JPATH_SITE.$processing->scriptfile ) ) ) {
			return;			
		}
		if ( $processing->scriptfile[0] === '/' ) {
			$processing->scriptfile	=	substr( $processing->scriptfile, 1 );
		}

		$extraData	=	'';

		$alert		=	self::_alert( $validation, 'alert', $config );
		$alert2		=	self::_alert( $validation, 'alert2', $config );
		$alert3		=	self::_alert( $validation, 'alert3', $config );
		$prefix		=	JCck::getConfig_Param( 'validation_prefix', '* ' );

		if ( isset( $validation->fieldnames ) && $validation->fieldnames ) {
			$extraData	.=	'avWhere='.str_replace( '||', ',', $validation->fieldnames );
		}

		$rule		=	'
					"'.$name.'":{
						"url": "'.JCckDevHelper::getAbsoluteUrl( 'auto', 'task=ajax&format=raw&'.Session::getFormToken().'=1&referrer=processing.'.str_replace( '/', '_', substr( $processing->scriptfile, 0, -4 ) ).'&file='.$processing->scriptfile ).'",
						"extraCallback": "'.$validation->callback_function.'",
						"extraData": "'.$extraData.'",
						"alertText": "'.$prefix.$alert.'",
						"alertTextOk": "'.$prefix.$alert2.'",
						"alertTextLoad": "'.$prefix.$alert3.'"}
						';
		
		$config['validation'][$name]	=	$rule;
		$field->validate[]				=	'ajax['.$name.']';
	}
	
	// onCCK_Field_ValidationPrepareStore
	public static function onCCK_Field_ValidationPrepareStore( &$field, $name, $value, &$config )
	{
	}

	// _alert
	protected static function _alert( $validation, $target, $config )
	{
		if ( isset( $validation->$target ) && $validation->$target != '' ) {
			$alert	=	$validation->$target;
			if ( $config['doTranslation'] ) {
				if ( trim( $alert ) ) {
					$alert	=	Text::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $alert ) ) );
				}
			}
		} else {
			static $already	=	0;
			if ( !$already ) {
				Factory::getLanguage()->load( 'plg_cck_field_validation_'.self::$type, JPATH_ADMINISTRATOR, null, false, true );
				$already	=	1;
			}
			$alert	=	Text::_( 'PLG_CCK_FIELD_VALIDATION_'.self::$type.'_'.$target );
		}
		
		return $alert;
	}
}
?>