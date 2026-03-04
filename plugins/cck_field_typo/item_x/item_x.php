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
class plgCCK_Field_TypoItem_X extends JCckPluginTypo
{
	protected static $type	=	'item_x';
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
		
	// onCCK_Field_TypoPrepareContent
	public function onCCK_Field_TypoPrepareContent( &$field, $target = 'value', &$config = array() )
	{		
		if ( self::$type != $field->typo ) {
			return;
		}
		
		// Prepare
		$typo	=	parent::g_getTypo( $field->typo_options );
		$value	=	parent::g_hasLink( $field, $typo, $field->$target );
		
		// Set
		if ( $field->typo_label ) {
			$field->label	=	self::_typo( $typo, $field, $field->label, $config );
		}
		$field->typo		=	self::_typo( $typo, $field, $value, $config );
	}
	
	// _typo
	protected static function _typo( $typo, $field, $value, &$config = array() )
	{
		$app		=	Factory::getApplication();
		$attr		=	' data-cck-remove-before-search=""';
		$value		=	$field->value; /* We may need a parameter to cast as (int) */
		$referrer	=	$app->input->getCmd( 'cck_item_x_referrer', $app->input->getCmd( 'referrer', uniqid() ) );
		$type		=	$typo->get( 'type', 'identifier' );


		// Init
		$html		=	'';
		$mode		=	(int)plgCCK_FieldItem_X::getFieldProperty( $referrer, 'mode' );
		$ui			=	plgCCK_FieldItem_X::getFieldProperty( $referrer, 'ui' );

		if ( strpos( $referrer, '.' ) !== false ) {
			$parts		=	explode( '.', $referrer );
			$referrer	=	$parts[2];

			if ( $parts[1] == 'search' ) {
				$attr	=	'';
			}
		}

		// Prepare
		$identifier_property	=	$typo->get( 'identifier_property', '' );
		$identifier_name		=	$typo->get( 'identifier_name', '' );

		// Set
		if ( strpos( $type, 'property_' ) !== false ) {
			$identifier		=	 $typo->get( 'identifier', '' );

			if ( $identifier == '' ) {
				// TODO: get from referer
				// echo '**'.$referrer.'**';
			}
			if ( !isset( $config[$identifier] ) ) {
				return '';
			}
			if ( !( $identifier_property == '-1' && $identifier_name != '' ) ) {
				$identifier_name	=	$field->storage_field;
			}

			$identifier	=	$config[$identifier];
			$options	=	array(
								'class'=>$typo->get( 'class', '' ),
								'id'=>'',
								'name'=>'',
								'required'=>$typo->get( 'required', '' ),
								'required2'=>$typo->get( 'required2', '' ),
								'validation'=>$typo->get( 'validation', '' ),
								'variation'=>'form'
							);
			
			switch ( $type ) {
				case 'property_identifier':
					$options['id']		=	$identifier.'_'.$identifier_name;
					$options['name']	=	$identifier.'['.$identifier_name.']';
					break;
				case 'property_identifier_group':
					// TODO: get from referer?
					$identifier_group	=	'products';
					
					if ( $identifier_group == '' ) {
						return;
					}
					$options['id']		=	$identifier.'_'.$identifier_group.'_'.$identifier_name;
					$options['name']	=	$identifier_group.'['.$identifier.']'.'['.$identifier_name.']';
					break;
				default:
					return '';
					break;
			}

			$html		=	self::_html( $options, $field, $config );
		} else {
			if ( !$value ) {
				return '';
			}
			if ( !( $identifier_property == '-1' && $identifier_name != '' ) ) {
				$identifier_name	=	$referrer;
			}

			$attr	=	'id="'.$value.'_'.$referrer.'" name="'.$identifier_name.( $mode ? '[]' : '' ).'" value="'.$value.'"'.$attr;

			if ( $ui ) {
				parent::g_addProcess( 'beforeRenderContent', self::$type, $config, array( 'name'=>$field->name, 'attr'=>$attr, 'referrer'=>$referrer ) );
			}

			$html	=	'<input type="hidden" '.$attr.' />';
		}

		return $html;
	}

	// _html
	protected static function _html( $options, $field, &$config )
	{
		static $i		=	0;
		static $pks		=	array();

		$app			=	Factory::getApplication();
		$pk				=	$config['pk'];
		
		if ( !isset( $pks[$pk] ) ) {
			$pks[$pk]	=	$i;
			$i++;
		}

		if ( $options['variation'] == 'form_custom_number' ) {
			$options['variation']	=	'form';
			$variation				=	'custom_number';
		} else {
			$variation				=	'';
		}

		$unset_validation	=	false;

		if ( !isset( $config['doValidation'] ) ) {
			$config['doValidation']	=	0;
			$unset_validation		=	true;
		} else {
			$doValidation	=	$config['doValidation'];
		}
		
		$field->attributes	.=	' data-cck-remove-before-search=""';
		$field->css			=	trim( $field->css.' '.$options['class'] );
		$field->label2		=	( $field->label != '' ) ? $field->label : 'clear';
		$inherit			=	array(
									'id'=>$options['id'],
									'name'=>$options['name']
								);
		
		if ( $options['variation'] == 'form_disabled' ) {
			$field->variation	=	'disabled';
		} elseif ( $options['variation'] == 'form_hidden' ) {
			$field->variation	=	'hidden';
		} else {
			if ( $options['validation'] != '' ) {
				$field->validation			=	$options['validation'];
				$field->validation_options	=	'{}';

				require_once JPATH_PLUGINS.'/cck_field_validation/'.$field->validation.'/'.$field->validation.'.php';
				JCck::callFunc_Array( 'plgCCK_Field_Validation'.$field->validation, 'onCCK_Field_ValidationPrepareForm', array( &$field, $inherit['id'], &$config ) );
			}
			if ( $options['required'] == 'required' ) {
				$config['doValidation']	=	2;
				$field->required		=	'required';
				$field->required_alert	=	'';
			} elseif ( $options['required'] == 'grouprequired' ) {
				$config['doValidation']	=	2;
				$field->required		=	'grouprequired['.$options['required2'].']';
				$field->required_alert	=	'';

				$config['validation']	=	array();
			}
		}

		if ( $variation ) {
			$field->variation	=	$variation;
		}

		/* TODO#SEBLOD4 temporary fix */
		$field->options			=	JCckDatabase::loadResult( 'SELECT options FROM #__cck_core_fields WHERE name = "'.$field->name.'"' );
		/* TODO#SEBLOD4 */

		$app->triggerEvent( 'onCCK_FieldPrepareForm', array( &$field, $field->value, &$config, $inherit ) );
		
		$field->form			=	JCck::callFunc_Array( 'plgCCK_Field'.$field->type, 'onCCK_FieldRenderForm', array( $field, &$config ) );
		$field->label			=	$field->label2 != 'clear' ? $field->label2 : '';
		$html					=	$field->form;
		$config['formWrapper']	=	1;

		if ( $config['doValidation'] ) {
			static $validation_loaded	=	0;
			
			if ( !$validation_loaded )	{
				$validation_loaded		=	1;
			} else {
				if ( $i > 1 ) {
					$config['validation']	=	null;
				}
			}
		}

		if ( $unset_validation ) {
			unset( $config['doValidation'] );
		} else {
			$config['doValidation']	=	$doValidation;
		}

		return $html;
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Special Events
	
	// onCCK_Field_TypoBeforeRenderContent
	public static function onCCK_Field_TypoBeforeRenderContent( $process, &$fields, &$storages, &$config = array() )
	{
		$name	=	$process['name'];

		if ( isset( $fields[$name] ) ) {
			if ( isset( $fields['x_pk'] ) && $fields['x_pk']->state ) {
				$checked	=	isset( $fields['x_pk'] ) && $fields['x_pk']->value ? ' checked="checked"' : '';
			} else {
				$checked	=	isset( $fields[$process['referrer'].'_pk'] ) && $fields[$process['referrer'].'_pk']->value ? ' checked="checked"' : '';
			}
			
			$fields[$name]->typo	=	'<input type="checkbox" '.$process['attr'].$checked.' />';
		}
	}
}
?>