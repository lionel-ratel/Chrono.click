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

// Plugin
class plgCCK_FieldCode_BeforeSearch extends JCckPluginField
{
	protected static $type	=	'code_beforesearch';
	protected static $path;
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Construct
	
	// onCCK_FieldConstruct
	public function onCCK_FieldConstruct( $type, &$data = array() )
	{
		if ( self::$type != $type ) {
			return;
		}
		parent::g_onCCK_FieldConstruct( $data );
		$data['display']	=	0;
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_FieldPrepareContent
	public function onCCK_FieldPrepareContent( &$field, $value = '', &$config = array() )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		
		// Set
		$field->state	=	true;
		$field->value	=	'';
	}
	
	// onCCK_FieldPrepareForm
	public function onCCK_FieldPrepareForm( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		
		// Set
		$field->form	=	'';
		$field->state	=	true;
		$field->value	=	'';
	}
	
	// onCCK_FieldPrepareSearch
	public function onCCK_FieldPrepareSearch( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
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
		
		// Process
		if ( $field->bool ) {
			if ( $field->options ) {
				$options	=	explode( '||', $field->options );
				if ( count( $options ) ) {
					parent::g_addProcess( 'beforeSearch', self::$type, $config, array( 'name'=>$name, 'mode'=>$field->bool, 'files'=>$options ) );
				}
			}
		} else {
			$options2	=	JCckDev::fromJSON( $field->options2 );
			if ( isset( $options2['code'] ) && $options2['code'] != '' ) {
				parent::g_addProcess( 'beforeSearch', self::$type, $config, array( 'name'=>$name, 'mode'=>$field->bool, 'code'=>$options2['code'] ) );
			}
		}
		
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
	
	// onCCK_FieldBeforeSearch
	public static function onCCK_FieldBeforeSearch( $process, &$fields, &$storages, &$config = array() )
	{
		$name	=	$process['name'];
		
		// Prepare
		if ( $process['mode'] ) {
			foreach ( $process['files'] as $file ) {
				if ( is_file( JPATH_SITE.'/'.$file ) ) {
					include JPATH_SITE.'/'.$file;
				}
			}
		} else {
			$code	=	$process['code'];
			eval( $code );
		}
	}
}
?>