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
class plgCCK_Field_LiveX extends JCckPluginLive
{
	protected static $type	=	'x';
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
		
	// onCCK_Field_LivePrepareForm
	public function onCCK_Field_LivePrepareForm( &$field, &$value = '', &$config = array(), $inherit = array() )
	{
		if ( self::$type != $field->live ) {
			return;
		}

		// Init
		$live		=	'';
		$options	=	parent::g_getLive( $field->live_options );

		// Prepare
		$event		=	'onCCK_Field_LivePrepareForm';
		$lives		=	$options->toArray();
		$mode		=	isset( $lives['mode'] ) && $lives['mode'] ? 1 : 0;
		$process	=	array();

		if ( !isset( $lives['conditions'] ) ) {
			return '';
		}
		if ( count( $lives['conditions'] ) ) {
			if ( isset( $config['process'] ) && count( $config['process'] ) ) {
				foreach ( $config['process'] as $k=>$v ) {
					$process[$k]	=	count( $v );
				}
			}

			$app			=	Factory::getApplication();
			$display		=	$field->display;
			$restriction	=	$field->restriction;
			$options2		=	$field->live_options;
			$options3		=	$field->restriction_options;

			if ( isset( $field->state ) ) {
				$state		=	$field->state;
			}

			foreach ( $lives['conditions'] as $condition ) {
				if ( isset( $condition['restriction'] ) && $condition['restriction'] != '' ) {
					$field->restriction			=	$condition['restriction'];
					$field->restriction_options	=	json_encode( $condition['restriction_options'] );	

					$res	=	JCck::callFunc_Array( 'plgCCK_Field_Restriction'.$condition['restriction'], 'onCCK_Field_RestrictionPrepareForm', array( &$field, &$config ) );

					if ( $res === false ) {
						continue;
					}
				}
				
				$val					=	'';

				if ( $condition['trigger'] == 'default' ) {
					$val					=	$condition['options']['value'];
				} else {
					$field->live			=	$condition['trigger'];
					$field->live_options	=	json_encode( $condition['options'] );

					$app->triggerEvent( 'onCCK_Field_LivePrepareForm', array( &$field, &$val, &$config ) );
				}

				if ( $mode ) {
					$live		.=	$val;
				} else {
					if ( $val != '' ) {
						$live	=	$val;
						break;
					}
				}
			}
			
			$field->display				=	$display;
			$field->live				=	'x';
			$field->live_options		=	$options2;
			$field->restriction			=	$restriction;
			$field->restriction_options	=	$options3;

			if ( isset( $state ) ) {
				$field->state			=	$state;
			}
		}

		// Set
		$value	=	(string)$live;

		if ( isset( $lives['modifier'] ) && $lives['modifier'] && $value != '' ) {
			switch ( $lives['modifier'] ) {
				case 'base64':
					$value	=	base64_encode( $value );
					break;
				default:
					break;
			}
		}
	}
}
?>