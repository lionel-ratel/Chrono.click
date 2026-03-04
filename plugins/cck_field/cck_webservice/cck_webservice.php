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
use Joomla\CMS\Language\Text;

// Plugin
class plgCCK_FieldCck_Webservice extends JCckPluginField
{
	protected static $type		=	'cck_webservice';
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
	
	// onCCK_FieldConstruct_SearchSearch
	public static function onCCK_FieldConstruct_SearchSearch( &$field, $style, $data = array(), &$config = array() )
	{
		$data['label']			=	null;
		$data['live']			=	null;
		$data['markup']			=	null;
		$data['markup_class']	=	null;
		$data['validation']		=	null;
		$data['variation']		=	null;
		
		if ( !isset( $config['construction']['match_mode'][self::$type] ) ) {
			$data['match_mode']	=	array(
										'none'=>HTMLHelper::_( 'select.option', 'none', Text::_( 'COM_CCK_DISABLED' ) ),
										''=>HTMLHelper::_( 'select.option', '', Text::_( 'COM_CCK_ENABLED' ) )
									);

			$config['construction']['match_mode'][self::$type]	=	$data['match_mode'];
		} else {
			$data['match_mode']									=	$config['construction']['match_mode'][self::$type];
		}

		parent::onCCK_FieldConstruct_SearchSearch( $field, $style, $data, $config );
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_FieldPrepareContent
	public function onCCK_FieldPrepareContent( &$field, $value = '', &$config = array() )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		parent::g_onCCK_FieldPrepareContent( $field, $config );

		$name			=	$field->name;
		$options2		=	JCckDev::fromJSON( $field->options2 );

		parent::g_addProcess( 'beforeRenderContent', self::$type, $config, array( 'name'=>$name, 'webservice_call'=>$options2['call'], 'webservice'=>$field->extended ) );

		// Set
		$field->value	=	$options2['call'];
	}
	
	// onCCK_FieldPrepareForm
	public function onCCK_FieldPrepareForm( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		
		// Set
		$field->form	=	'';
		$field->value	=	'';
	}
	
	// onCCK_FieldPrepareSearch
	public function onCCK_FieldPrepareSearch( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		
		/*
		$name			=	$field->name;
		$options2		=	JCckDev::fromJSON( $field->options2 );

		parent::g_addProcess( 'beforeRenderForm', self::$type, $config, array( 'name'=>$name, 'webservice_call'=>$options2['call'], 'webservice'=>$field->extended ) );
		*/

		// Set
		$field->form	=	'';
		$field->value	=	'';
		
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
		
		// Process
		$options2		=	JCckDev::fromJSON( $field->options2 );
		
		parent::g_addProcess( 'beforeStore', self::$type, $config, array( 'mode'=>$field->bool, 'name'=>$name, 'webservice'=>$field->extended, 'webservice_call'=>@$options2['call'] ) );
		
		// Set or Return
		if ( $return === true ) {
			return $value;
		}
		$field->value	=	$value;
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
		$response 	=	JCckWebservice::call( $process['webservice_call'], array(), $fields );

		if ( is_object( $response ) ) {
			foreach ( get_object_vars( $response ) as $key => $value ) {
				$fields[$process['name']]->{'response_'.strtolower( $key )} 	=	$value;
			}
		} else {
			foreach ( $response as $key => $value ) {
				$fields[$process['name']]->{'response_'.strtolower( $key )} 	=	$value;
			}
		}
	}

	// onCCK_FieldBeforeRenderForm
	public static function onCCK_FieldBeforeRenderForm( $process, &$fields, &$storages, &$config = array() )
	{
		// OK
	}

	// onCCK_FieldBeforeStore
	public static function onCCK_FieldBeforeStore( $process, &$fields, &$storages, &$config = array() )
	{
		$name			=	$process['name'];

		if ( !$fields[$process['name']]->state ) {
			return;
		}
		
		// Process
		if ( (int)$process['mode'] == 2 ) {
			$response 	=	JCckWebservice::call( $process['webservice_call'], array(), $fields, true );
		} elseif ( (int)$process['mode'] ) {
			$response 	=	JCckWebservice::stack( $process['webservice_call'], array(), $fields );
		} else {
			$response 	=	JCckWebservice::call( $process['webservice_call'], array(), $fields );
		}
		
		// Set
		$fields[$process['name']]->value			=	$response;
		$storage_field								=	$fields[$name]->storage_field;
		$storage_table								=	$fields[$name]->storage_table;
		$storages[$storage_table][$storage_field]	=	$response;
	}
}
?>