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

require_once __DIR__.'/classes/uikit_tabs.php';

// Plugin
class plgCCK_FieldUikit_Tabs extends JCckPluginField
{
	protected static $type		=	'uikit_tabs';
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

	// onCCK_FieldConstruct_SearchContent
	public static function onCCK_FieldConstruct_SearchContent( &$field, $style, $data = array(), &$config = array() )
	{
		$data['markup']			=	null;
		$data['markup_class']	=	null;

		parent::onCCK_FieldConstruct_SearchContent( $field, $style, $data, $config );
	}

	// onCCK_FieldConstruct_SearchSearch
	public static function onCCK_FieldConstruct_SearchSearch( &$field, $style, $data = array(), &$config = array() )
	{
		$data['match_mode']		=	null;
		$data['markup']			=	null;
		$data['markup_class']	=	null;
		$data['validation']		=	null;
		$data['variation']		=	null;
		
		parent::onCCK_FieldConstruct_SearchSearch( $field, $style, $data, $config );
	}

	// onCCK_FieldConstruct_TypeContent
	public static function onCCK_FieldConstruct_TypeContent( &$field, $style, $data = array(), &$config = array() )
	{
		$data['markup']			=	null;
		$data['markup_class']	=	null;

		parent::onCCK_FieldConstruct_TypeContent( $field, $style, $data, $config );
	}
	
	// onCCK_FieldConstruct_TypeForm
	public static function onCCK_FieldConstruct_TypeForm( &$field, $style, $data = array(), &$config = array() )
	{
		$data['markup']			=	null;
		$data['markup_class']	=	null;
		$data['validation']		=	null;
		$data['variation']		=	null;

		parent::onCCK_FieldConstruct_TypeForm( $field, $style, $data, $config );
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_FieldPrepareContent
	public function onCCK_FieldPrepareContent( &$field, $value = '', &$config = array() )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		parent::g_onCCK_FieldPrepareContent( $field, $config );
		
		// Init
		$id			=	$field->name;
		$value		=	(int)$field->defaultvalue;
		$value		=	( $value ) ? $value - 1 : 0;
		$group_id	=	( $field->location != '' ) ? $field->location : 'cck_tabs1';

		// Prepare
		$html		=	'';
		if ( $field->state ) {
			parent::g_addProcess( 'beforeRenderContent', self::$type, $config, array( 'name'=>$field->name, 'group_id'=>$group_id, 'id'=>$id, 'wrapper'=>$field->bool3, 'label'=>$field->label, 'position'=>$field->bool4, 'url_actions'=>$field->bool2, 'value'=>$value ), 5 );
		}

		// Set
		$field->html	=	$html;
		$field->value	=	$field->label;
		$field->label	=	'';
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
		} else {
			$id		=	$field->name;
		}
		$value		=	( $value != '' ) ? (int)$value : (int)$field->defaultvalue;
		$value		=	( $value ) ? $value - 1 : 0;
		$group_id	=	( $field->location != '' ) ? $field->location : 'cck_tabs1';
		
		// Prepare
		$form		=	'';

		if ( $field->state ) {
			parent::g_addProcess( 'beforeRenderForm', self::$type, $config, array( 'name'=>$field->name, 'group_id'=>$group_id, 'id'=>$id, 'wrapper'=>$field->bool3, 'label'=>$field->label, 'position'=>$field->bool4, 'url_actions'=>$field->bool2, 'value'=>$value ), 5 );
		}

		// Set
		$field->form	=	$form;
		$field->value	=	$field->label;
		$field->label	=	'';
		
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
	public static function onCCK_FieldRenderContent( &$field, &$config = array() )
	{
		$field->markup	=	'none';

		return parent::g_onCCK_FieldRenderContent( $field, 'html' );
	}
	
	// onCCK_FieldRenderForm
	public static function onCCK_FieldRenderForm( &$field, &$config = array() )
	{
		$field->markup	=	'none';

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
		
		self::_prepare( 'html', $process, $fields );
	}

	// onCCK_FieldBeforeRenderForm
	public static function onCCK_FieldBeforeRenderForm( $process, &$fields, &$storages, &$config = array() )
	{
		$name	=	$process['name'];

		if ( !$fields[$name]->state ) {
			return;
		}
		
		self::_prepare( 'form', $process, $fields );
	}

	// _prepare
	protected static function _prepare( $target, $process, &$fields )
	{
		$layout			=	Factory::getApplication()->input->get( 'tmpl' );
		$name			=	$process['name'];
		$params			=	array(
								'id'=>$process['id'],
								'label'=>$process['label'],
								'name'=>$process['name'],
								'position'=>(int)$process['position'],
								'selector'=>$process['group_id'],
								'type'=>(int)$process['type'],
								'wrapper'=>$process['wrapper']
							);

		static $navs	=	array();

		$css	=	trim( $fields[$name]->css );

		if ( $css !== '' ) {
			$css	=	' '.$css;
		}		
		
		if ( $fields[$name]->bool == 2 ) {
			$html	=	JCckDevUikitTabs::end( $params, $navs );
		} elseif ( $fields[$name]->bool == 1 ) {
			$navs[$process['group_id']][]	=	$process['label'];
			$html	=	JCckDevUikitTabs::open( $params, $css );
		} else {
			$navs[$process['group_id']][]	=	$process['label'];
			$html	=	JCckDevUikitTabs::start( $params, $process['value'], $css );
		}

		$fields[$name]->$target	=	$html;
	}
}
?>