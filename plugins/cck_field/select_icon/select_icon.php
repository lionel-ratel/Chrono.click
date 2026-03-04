<?php
/**
* @version 			SEBLOD 3.x More
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2016 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\Filesystem\Folder;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;

// Plugin
class plgCCK_Fieldselect_icon extends JCckPluginField
{
	protected static $type			=	'select_icon';
	protected static $path;
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Construct
	
	// onCCK_FieldConstruct
	public function onCCK_FieldConstruct( $type, &$data = array() )
	{
		if ( self::$type != $type ) {
			return;
		}
		if ( isset( $data['string']['location'] ) && is_array( $data['string']['location'] ) ) {
			if ( !implode( '', $data['string']['location'] ) ) {
				$data['json']['options2']['options']	=	'';
			}
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
		parent::g_onCCK_FieldPrepareContent( $field, $config );
		
		// Set
		$field->value	=	$value;
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
		
		// Validate
		$validate	=	'';
		if ( $config['doValidation'] > 1 ) {
			plgCCK_Field_ValidationRequired::onCCK_Field_ValidationPrepareForm( $field, $id, $config );
			parent::g_onCCK_FieldPrepareForm_Validation( $field, $id, $config );
			$validate	=	( count( $field->validate ) ) ? ' validate['.implode( ',', $field->validate ).']' : '';
		}
		
		// Prepare
		jimport('joomla.filesystem.folder');
		$options2		=	JCckDev::fromJSON( $field->options2 );
		$path			=	substr( $options2['path'], 0, -1 );
		$path			=	JPATH_SITE.DIRECTORY_SEPARATOR.$path;

		if ( is_dir( $path ) ) {
			
			$css 		= file_get_contents( JPATH_SITE."/".$options2['path'].$options2['file'] );
			preg_match_all( '/(?ims)([a-z0-9\s\,\.\:#_\-@]+)\{([^\}]*)\}/', $css, $arr );			
			$before 	= array();
			$class_name	= array();
			$data		= array();
			$opts		= array();
			
			foreach ($arr[0] as $i => $x) {
				$selector = trim($arr[1][$i]);				
				if ( strpos( $selector, ':' ) !== false ) {
					$before = explode( ':', $selector );
					if ( strpos( $before[0], '-' ) !== false ) {					
						$class_name = explode( '-', trim( $before[0] ), 2 );
						array_push(	$data, [
							'value' =>	str_replace( ".", "", $before[0] ),
							'text'	=>	mb_strtolower($class_name[1]),
							'attr'	=>	array( 'data-icon'=>str_replace( ".", "", $before[0] ), 'class'=>str_replace( ".", "", $before[0] ) ) 
						] );  
					}			
				}
			}

			if( $field->selectlabel ) {
				array_push(	$data, [
					'value' =>	'',
					'text'	=>	'- '.$field->selectlabel.' -'
				] );
			} else {
				array_push(	$data, [
					'value' =>	'',
					'text'	=>	'- Select an option -'
				] );
			} 

			usort($data, function($a, $b) {
			    return $a['text'] <=> $b['text'];
			});
			
			$class	=	'inputbox select'.$validate . ( $field->css ? ' '.$field->css : '' );
			
			if ( ( is_string( $value ) && $value != '' ) || ( is_array( $value ) && count( $value ) && $value[0] != '' ) ) {
				$class	.=	' has-value';
			}
			$size	=	' size=1';
			
			$attr	=	'class="'.$class.'"'.$size . ( $field->attributes ? ' '.$field->attributes : '' );

			if ( $field->script ) {
				parent::g_addScriptDeclaration( $field->script );
			}

			$options = array(
				'id' 			=>	$id,
				'list.attr' 	=> 	$attr,
				'list.translate'=>	false,
				'option.key'	=>	'value',
				'option.text'	=>	'text',
				'option.attr'	=>	'attr',
				'list.select'	=>	$value
			);
			$form =	HTMLHelper::_( 'select.genericlist', $data, $name, $options ); 	

			HTMLHelper::_( 'formbehavior.chosen', '.select.chosen' );

			if ( $options2['filedata'] >= 1 ) {
				$cssData = '';
				foreach ($arr[0] as $i => $x)
				{	
					$selectors = explode('.'.$options2['icontype'].'-', trim( $arr[1][$i] ) );
					$rules = explode(';', trim($arr[2][$i]));
					foreach ($selectors as $k => $strSelector){
						if( $k == 0 && !empty($strSelector) && $i <= 0 ) {
							$cssData .= '@font-face{'."\n".trim($arr[2][0])."\n".'}'."\n";
							$cssData .= '[class^="'.$options2['icontype'].'-"]:before, [class*=" '.$options2['icontype'].'-"]:before{'."\n".trim($arr[2][1])."\n".'}'."\n";
						}
						if( $k > 0 && !empty($strSelector) ) {
							$selector = explode(":before", $strSelector);
							$cssData .= '.'.$options2['icontype'].'-'.$selector[0].':before,[data-icon="'.$selector[0].'"]:before{'."\n".$rules[0]."\n".'}'."\n";
						}
				    } 
				}
				$fileData = JPATH_SITE."/".$options2['path']."style-data.css";
			}

			if ( $options2['filedata'] == 2 ) {
				file_put_contents( $fileData, $cssData );
			}
		}
		
		// Set
		if ( ! $field->variation ) {
			$field->form	=	$form;
			if ( $options2['filedata'] >= 1 ) {
				$fileData = Uri::root( true )."/".$options2['path']."style-data.css";
				self::_addScripts( true, array( 'filedata'=>@$fileData ), $config );
			}
		} else {
			$field->text =	parent::g_getOptionText( $value, $field->options, '', $config );
			parent::g_getDisplayVariation( $field, $field->variation, $value, $field->text, $form, $id, $name, '<select', '', '', $config );
		}
		
		$field->value	=	$value;		
		
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
		
		// Set
		$field->match_value	=	$field->match_value ? $field->match_value : ',';
		$field->value		=	$value;
		
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
		
		// Validate
		parent::g_onCCK_FieldPrepareStore_Validation( $field, $name, $value, $config );
		
		// Set or Return
		if ( $return === true ) {
			return $value;
		}
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
	protected static function _addScripts( $inline, $params = array(), &$config = array() )
	{		
		if( $params['filedata'] ) {
			$doc	=	Factory::getDocument();
			$css_s	=	$params['filedata'];
			$doc->addStyleSheet( $css_s );
		}
	}
}
?>