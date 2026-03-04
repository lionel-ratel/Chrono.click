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
class plgCCK_FieldCode_Js extends JCckPluginField
{
	protected static $type		=	'code_js';
	protected static $path;
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Construct
	
	// onCCK_FieldConstruct
	public function onCCK_FieldConstruct( $type, &$data = array() )
	{
		if ( self::$type != $type ) {
			return;
		}

		$data['defaultvalue']	=	Factory::getApplication()->input->post->get( 'defaultvalue', '', 'raw' );
		parent::g_onCCK_FieldConstruct( $data );
		$data['display']		=	0;
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_FieldPrepareContent
	public function onCCK_FieldPrepareContent( &$field, $value = '', &$config = array() )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		parent::g_onCCK_FieldPrepareContent( $field, $config );

		// Set
		$field->value	=	'';
		
		if ( $field->state && $field->defaultvalue != '' ) {
			$js			=	$field->defaultvalue;

			if ( (int)$field->bool > -1 ) {
				$js		=	( $field->bool ) ? '(function ($){ '.$js.' })(jQuery);' : 'jQuery(document).ready(function($){ '.$js.' });';
			}
			
			if ( strpos( $field->defaultvalue, '$cck->get' ) !== false ) {
				$matches	=	'';
				$search		=	'#\$cck\->get([a-zA-Z0-9_]*)\( ?\'([a-zA-Z0-9_,]*)\' ?\)(;)?#';
				preg_match_all( $search, $field->defaultvalue, $matches );
				
				if ( count( $matches[1] ) ) {
					parent::g_addProcess( 'beforeRenderContent', self::$type, $config, array( 'name'=>$field->name, 'matches'=>$matches, 'js'=>$field->defaultvalue ), 5 );
				}
			} else {
				if ( Factory::getApplication()->input->get( 'tmpl' ) == 'raw' ) {
					echo '<script type="text/javascript">'.$js.'</script>';
				} else {
					Factory::getDocument()->addScriptDeclaration( $js );
				}
			}
		}
	}
	
	// onCCK_FieldPrepareForm
	public function onCCK_FieldPrepareForm( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		parent::g_onCCK_FieldPrepareForm( $field, $config );

		// Set
		$field->form	=	'';
		$field->value	=	'';
		
		if ( $field->state && $field->defaultvalue != '' ) {
			$js			=	$field->defaultvalue;

			if ( (int)$field->bool > -1 ) {
				$js		=	( $field->bool ) ? '(function ($){ '.$js.' })(jQuery);' : 'jQuery(document).ready(function($){ '.$js.' });';
			}
			
			if ( strpos( $field->defaultvalue, '$cck->get' ) !== false ) {
				$matches	=	'';
				$search		=	'#\$cck\->get([a-zA-Z0-9_]*)\( ?\'([a-zA-Z0-9_,]*)\' ?\)(;)?#';
				preg_match_all( $search, $field->defaultvalue, $matches );
				
				if ( count( $matches[1] ) ) {
					parent::g_addProcess( 'beforeRenderForm', self::$type, $config, array( 'name'=>$field->name, 'matches'=>$matches, 'js'=>$field->defaultvalue ), 5 );
				}
			} else {
				if ( Factory::getApplication()->input->get( 'tmpl' ) == 'raw' ) {
					echo '<script type="text/javascript">'.$js.'</script>';
				} else {
					Factory::getDocument()->addScriptDeclaration( $js );
				}
			}
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
		self::_render( $process, $fields );
	}
	
	// onCCK_FieldBeforeRenderForm
	public static function onCCK_FieldBeforeRenderForm( $process, &$fields, &$storages, &$config = array() )
	{
		self::_render( $process, $fields );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Stuff & Script
	
	// _render
	protected static function _render( $process, $fields )
	{
		$js		=	$process['js'];
		$name	=	$process['name'];

		if ( count( $process['matches'][1] ) ) {
			foreach ( $process['matches'][1] as $k=>$v ) {
				$fieldname		=	$process['matches'][2][$k];
				$target			=	strtolower( $v );
				$value			=	'';
				if ( strpos( $fieldname, ',' ) !== false ) {
					$fieldname	=	explode( ',', $fieldname );
					if ( count( $fieldname ) == 3 ) {
						if ( $fields[$fieldname[0]]->value[$fieldname[1]][$fieldname[2]] ) {
							$value	=	$fields[$fieldname[0]]->value[$fieldname[1]][$fieldname[2]]->$target;
						}
					} else {
						if ( $fields[$fieldname[0]]->value[$fieldname[1]] ) {
							$value	=	$fields[$fieldname[0]]->value[$fieldname[1]]->$target;
						}
					}
				} else {
					$value	=	$fields[$fieldname]->$target;
				}
				$js		=	str_replace( $process['matches'][0][$k], $value, $js );
			}

			if ( (int)$fields[$name]->bool > -1 ) {
				$js		=	( $fields[$name]->bool ) ? '(function ($){ '.$js.' })(jQuery);' : 'jQuery(document).ready(function($){ '.$js.' });';
			}

			if ( Factory::getApplication()->input->get( 'tmpl' ) == 'raw' ) {
				echo '<script type="text/javascript">'.$js.'</script>';
			} else {
				Factory::getDocument()->addScriptDeclaration( $js );
			}
		}
	}
}
?>