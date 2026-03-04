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

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\String\StringHelper;

// Plugin
class plgCCK_FieldCCK_Form extends JCckPluginField
{
	protected static $type	=	'cck_form';
	protected static $path;
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Construct
	
	// onCCK_FieldConstruct
	public function onCCK_FieldConstruct( $type, &$data = array() )
	{
		if ( self::$type != $type ) {
			return;
		}
		parent::g_onCCK_FieldConstruct( $data );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_FieldPrepareContent
	public function onCCK_FieldPrepareContent( &$field, $value = '', &$config = array() )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		$field->priority	=	3;

		parent::g_onCCK_FieldPrepareContent( $field, $config );

		// Prepare
		if ( $field->state ) {
			$value				=	( $field->sorting > -1 ) ? $value : ( $field->extended ? $field->extended : $field->defaultvalue );
			$field->extended	=	$value;

			if ( $field->extended ) {
				if ( $field->options ) {
					$options	=	explode( '||', $field->options );
				} else {
					$options	=	array();
				}

				parent::g_addProcess( 'beforeRenderContent', self::$type, $config, array( 'name'=>$field->name, 'extended'=>$value, 'fieldnames'=>$options ) );
			}
		}
		
		// Set
		$field->value	=	'';
	}
	
	// onCCK_FieldPrepareForm
	public function onCCK_FieldPrepareForm( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		self::$path	=	parent::g_getPath( self::$type.'/' );
		parent::g_onCCK_FieldPrepareForm( $field, $config );
		
		// Init
		if ( count( $inherit ) ) {
			$id		=	( isset( $inherit['id'] ) && $inherit['id'] != '' ) ? $inherit['id'] : $field->name;
			$name	=	( isset( $inherit['name'] ) && $inherit['name'] != '' ) ? $inherit['name'] : $field->name;
		} else {
			$id		=	$field->name;
			$name	=	$field->name;
		}
		$value		=	( $value != '' ) ? $value : $field->defaultvalue;
		
		// Prepare
		if ( $field->sorting > -1 ) {
			// Validate
			$validate	=	'';
			if ( $config['doValidation'] > 1 ) {
				plgCCK_Field_ValidationRequired::onCCK_Field_ValidationPrepareForm( $field, $id, $config );
				parent::g_onCCK_FieldPrepareForm_Validation( $field, $id, $config );
				$validate	=	( count( $field->validate ) ) ? ' validate['.implode( ',', $field->validate ).']' : '';
			}

			$options	=	'';
			$options2	=	JCckDev::fromJSON( $field->options2 );
			if ( @$options2['forms'] != '' ) {
				$options	=	explode( '||', $options2['forms'] );
			}
			if ( $field->bool8 ) {
				$field->bool8	=	$config['doTranslation'];
			}
			if ( $field->sorting == 1 ) {
				natsort( $options );
				$optionsSorted	=	array_slice( $options, 0 );
			} elseif ( $field->sorting == 2 ) {
				natsort( $options );
				$optionsSorted	=	array_reverse( $options, true );
			} else {
				$optionsSorted	=	$options;
			}
			$opts	=	array();
			if ( trim( $field->selectlabel ) ) {
				if ( $field->bool8 ) {
					$field->selectlabel	=	Text::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $field->selectlabel ) ) );
				}
				$opts[]	=	HTMLHelper::_( 'select.option',  '', '- '.$field->selectlabel.' -', 'value', 'text' );
			}
			$optgroup	=	0;
	
			if ( count( $optionsSorted ) ) {
				foreach ( $optionsSorted as $val ) {
					if ( trim( $val ) != '' ) {
						if ( StringHelper::strpos( $val, '=' ) !== false ) {
							$opt	=	explode( '=', $val );
							if ( $opt[1] == 'optgroup' ) {
								if ( $optgroup == 1 ) {
									$opts[]	=	HTMLHelper::_( 'select.option', '</OPTGROUP>' );
								}
								if ( $field->bool8 && trim( $opt[0] ) ) {
									$opt[0]	=	Text::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $opt[0] ) ) );
								}
								$opts[]		=	HTMLHelper::_( 'select.option', '<OPTGROUP>', $opt[0] );
								$optgroup	=	1;
							} elseif ( $opt[1] == 'endgroup' && $optgroup == 1 ) {
								$opts[]		=	HTMLHelper::_( 'select.option', '</OPTGROUP>' );
								$optgroup	=	0;
							} else {
								if ( $field->bool8 && trim( $opt[0] ) ) {
									$opt[0]	=	Text::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $opt[0] ) ) );
								}
								$opts[]	=	HTMLHelper::_( 'select.option', $opt[1], $opt[0], 'value', 'text' );
							}
						} else {
							if ( $val == 'endgroup' && $optgroup == 1 ) {
								$opts[]		=	HTMLHelper::_( 'select.option', '</OPTGROUP>' );
								$optgroup	=	0;
							} else {
								$text	=	$val;
								if ( $field->bool8 && trim( $text ) ) {
									$text	=	Text::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $text ) ) );
								}
								$opts[]	=	HTMLHelper::_( 'select.option', $val, $text, 'value', 'text' );
							}
						}
					}
				}
				if ( $optgroup == 1 ) {
					$opts[]		=	HTMLHelper::_( 'select.option', '</OPTGROUP>' );
				}
			}
			
			$class	=	'inputbox select'.$validate . ( $field->css ? ' '.$field->css : '' );
			if ( $value != '' ) {
				$class	.=	' has-value';
			}
			$attr	=	'class="'.$class.'"' . ( $field->attributes ? ' '.$field->attributes : '' );
			$form	=	'';
			if ( count( $opts ) ) {
				$form	=	HTMLHelper::_( 'select.genericlist', $opts, $name, $attr, 'value', 'text', $value, $id );
			}
			
			// Set
			if ( ! $field->variation ) {  
				$field->form	=	$form;
				if ( $field->script ) {
					parent::g_addScriptDeclaration( $field->script );
				}
			} else {
				$field->text	=	parent::g_getOptionText( $value, @$options2['forms'], '', $config ); /* TODO#SEBLOD: */
				parent::g_getDisplayVariation( $field, $field->variation, $value, $field->text, $form, $id, $name, '<select', '', '', $config );
			}
			$field->value	=	$value;
		} else {
			// Set
			$field->form	=	'';
			$field->value	=	'';
		}
		
		// Return
		if ( $return === true ) {
			return $field;
		}
	}
	
	// onCCK_FieldPrepareSearch
	public function onCCK_FieldPrepareSearch( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		
		// Prepare
		self::onCCK_FieldPrepareForm( $field, $value, $config, $inherit, $return );
		
		// Return
		if ( $return === true ) {
			return $field;
		}
	}
	
	// onCCK_FieldPrepareStore
	public function onCCK_FieldPrepareStore( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		
		// Init
		if ( count( $inherit ) ) {
			$name	=	( isset( $inherit['name'] ) && $inherit['name'] != '' ) ? $inherit['name'] : $field->name;
		} else {
			$name	=	$field->name;
		}
		
		// Prepare
		$options2	=	JCckDev::fromJSON( $field->options2 );

		// Validate
		$text	=	parent::g_getOptionText( $value, @$options2['forms'], '', $config ); /* TODO#SEBLOD: */
		parent::g_onCCK_FieldPrepareStore_Validation( $field, $name, $value, $config );
		
		// Set or Return
		if ( $return === true ) {
			return $value;
		}
		$field->text	=	$text;
		$field->value	=	$value;
		parent::g_onCCK_FieldPrepareStore( $field, $name, $value, $config );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Render
	
	// onCCK_FieldRenderContent
	public static function onCCK_FieldRenderContent( $field, &$config = array() )
	{
		return parent::g_onCCK_FieldRenderContent( $field );
	}
	
	// onCCK_FieldRenderForm
	public static function onCCK_FieldRenderForm( $field, &$config = array() )
	{
		return parent::g_onCCK_FieldRenderForm( $field );
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Special Events
	
	// onCCK_FieldBeforeRenderContent
	public static function onCCK_FieldBeforeRenderContent( $process, &$fields, &$storages, &$config = array() )
	{
		$name	=	$process['name'];

		if ( !$fields[$name]->state ) {
			return;
		}

		// Prepare
		$lives	=	self::_prepare( $process, $fields );

		// Set
		$fields[$name]->value	=	self::_render( $fields[$name], 'html', $lives, $config );
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Stuff & Script
	
	// _prepare
	protected static function _prepare( $process, $fields )
	{
		$lives	=	array();

		if ( count( $process['fieldnames'] ) ) {
			foreach ( $process['fieldnames'] as $field ) {
				if ( strpos( $field, '=' ) !== false ) {
					$f	=	explode( '=', $field );
				} else {
					$f	=	array( 0=>$field, 1=>$field );
				}
				if ( isset( $fields[$f[1]] ) ) {
					$v	=	$fields[$f[1]]->value;
				} elseif ( is_numeric( $f[1] ) ) {
					$v	=	(string)$f[1];
				} else {
					$len	=	strlen( $f[1] );
					
					if ( $f[1] != '' && $f[1][0] == '"' && $f[1][($len-1)] == '"' ) {
						$v	=	substr( $f[1], 1, -1 );
					} else {
						$v	=	'';
					}
				}
				if ( is_array( $v ) ) {
					$v	=	implode( ' ', $v );
				}
				$lives[$f[0]]	=	$v;
			}
		}

		return $lives;
	}

	// _render
	protected static function _render( $field, $target, $lives, $config )
	{
		/*
		$main_config			=	$config;
		$main_field				=	$field;
		*/

		$app					=	Factory::getApplication();
		$uniqId					=	'f'.$field->id;
		$formId					=	'seblod_form_'.$uniqId;
		
		$option					=	$app->input->get( 'option', '' );
		$view					=	'';
		$preconfig				=	array();
		$preconfig['action']	=	'';
		$preconfig['client']	=	'site';
		$preconfig['formId']	=	$formId;
		$preconfig['message']	=	'';
		$preconfig['task']		=	$app->input->get( 'task', '' );
		$preconfig['type']		=	$field->extended;
		$preconfig['submit']	=	'JCck.Core.submit_'.$uniqId;
		$preconfig['url']		=	Uri::getInstance()->toString();
		
		$live					=	'';	/* TODO#SEBLOD: */
		$variation				=	''; /* TODO#SEBLOD: */
		
		JCck::loadjQuery();

		// Prepare
		jimport( 'cck.base.form.form' );
		include JPATH_SITE.'/libraries/cck/base/form/form_inc.php';
		Factory::getSession()->set( 'cck_hash_'.$formId, ApplicationHelper::getHash( '0|'.$preconfig['type'].'|0|0' ) );
		Factory::getSession()->set( 'cck_hash_'.$formId.'_context', json_encode( $config['context'] ) );
		
		ob_start();
		include __DIR__.'/tmpl/render.php';
		$buffer		=	ob_get_clean();
		
		/*
		$config		=	$main_config;
		$field		=	$main_field;
		*/

		return $buffer;
	}
}
?>