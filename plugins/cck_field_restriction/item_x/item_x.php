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

// Plugin
class plgCCK_Field_RestrictionItem_X extends JCckPluginRestriction
{
	protected static $type	=	'item_x';
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare

	// onCCK_Field_RestrictionPrepareContent
	public static function onCCK_Field_RestrictionPrepareContent( &$field, &$config )
	{
		if ( self::$type != $field->restriction ) {
			return;
		}
		
		$restriction	=	parent::g_getRestriction( $field->restriction_options );
		
		return self::_authorise( $restriction, $field, $config );
	}

	// onCCK_Field_RestrictionPrepareForm
	public static function onCCK_Field_RestrictionPrepareForm( &$field, &$config )
	{
		if ( self::$type != $field->restriction ) {
			return;
		}
		
		$restriction	=	parent::g_getRestriction( $field->restriction_options );
		
		return self::_authorise( $restriction, $field, $config );
	}
	
	// onCCK_Field_RestrictionPrepareStore
	public static function onCCK_Field_RestrictionPrepareStore( &$field, &$config )
	{
		if ( self::$type != $field->restriction ) {
			return;
		}
		
		$restriction	=	parent::g_getRestriction( $field->restriction_options );
		
		return self::_authorise( $restriction, $field, $config );
	}
	
	// _authorise
	protected static function _authorise( $restriction, &$field, &$config )
	{
		$app		=	Factory::getApplication();
		$do			=	$restriction->get( 'do', 0 );
		$state		=	1;

		$mode		=	$restriction->get( 'mode' );
		$property	=	'';
		$referrer	=	$app->input->getCmd( 'cck_item_x_referrer', $app->input->getCmd( 'referrer', uniqid() ) );
		$required	=	$restriction->get( 'required' );
		$task		=	$restriction->get( 'task' );
		$variation	=	$restriction->get( 'variation' );

		if ( $mode != '' ) {
			if ( (bool)$mode !== plgCCK_FieldItem_X::getFieldProperty( $referrer, 'mode' ) ) {
				$state	=	0;

				if ( !$do ) {
					return false;
				}
			}
		}

		if ( $task != '' ) {
			if ( !plgCCK_FieldItem_X::getFieldProperty( $referrer, 'task_'.$task ) ) {
				$state	=	0;

				if ( !$do ) {
					return false;
				}
			}
		}

		if ( $variation != '' ) {
			$referrer_variation	=	plgCCK_FieldItem_X::getFieldProperty( $referrer, 'variation' );
			
			if ( $variation == 'visible' ) {
				if ( !( $referrer_variation == 'form' || $referrer_variation == 'disabled' || $referrer_variation == 'value' ) ) {
					$state	=	0;

					if ( !$do ) {
						return false;
					}
				}
			} elseif ( $variation == 'visible_form' ) {
				if ( !( $referrer_variation == 'form' || $referrer_variation == 'disabled' ) ) {
					$state	=	0;

					if ( !$do ) {
						return false;
					}
				}
			} elseif ( $variation !== $referrer_variation  ) {
				$state	=	0;

				if ( !$do ) {
					return false;
				}
			}
		}

		if ( $required != '' ) {
			if ( (bool)$required !== plgCCK_FieldItem_X::getFieldProperty( $referrer, 'required' ) ) {
				$state	=	0;

				if ( !$do ) {
					return false;
				}
			}
		}

		/*TODO#SEBLOD4: currently OK only for one selection ... make it work for multiple */

		if ( !$state ) {
			return $do ? true : false;
		} else {
			return $do ? false : true;
		}

		// return true;
	}
}
?>