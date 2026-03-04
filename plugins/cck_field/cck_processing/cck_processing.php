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

use Joomla\Registry\Registry;

// Plugin
class plgCCK_FieldCck_Processing extends JCckPluginField
{
	protected static $type		=	'cck_processing';
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
		parent::g_onCCK_FieldPrepareContent( $field, $config );
		
		// Prepare
		$field->display	=	0;
		$name			=	$field->name;
		$value			=	$field->defaultvalue;

		if ( JCckToolbox::getConfig()->get( 'processing', 0 ) ) {
			if ( $field->bool5 ) {
				parent::g_addProcess( 'beforeRenderContent', self::$type, $config, array( 'name'=>$name, 'processing'=>(int)$value ), $field->bool5 );
			} else {
				$processing	=	JCckDatabaseCache::loadObject( 'SELECT scriptfile, options FROM #__cck_more_processings WHERE id = '.(int)$value.' AND published = 1' );

				if ( $processing->scriptfile && is_file( JPATH_SITE.$processing->scriptfile ) ) {
					$options	=	new Registry( $processing->options );

					include JPATH_SITE.$processing->scriptfile;
				}
			}
		}

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
		
		// Prepare
		$field->display	=	0;
		$form			=	'';
		$name			=	$field->name;

		if ( JCckToolbox::getConfig()->get( 'processing', 0 ) ) {
			if ( $field->bool5 ) {
				parent::g_addProcess( 'beforeRenderForm', self::$type, $config, array( 'name'=>$name, 'processing'=>(int)$field->defaultvalue ), $field->bool5 );
			} else {
				$processing	=	JCckDatabase::loadObject( 'SELECT scriptfile, options FROM #__cck_more_processings WHERE id = '.(int)$field->defaultvalue.' AND published = 1' );

				if ( is_object( $processing ) && $processing->scriptfile && is_file( JPATH_SITE.$processing->scriptfile ) ) {
					$options	=	new Registry( $processing->options );
					
					include JPATH_SITE.$processing->scriptfile;
				}
			}
		}

		// Set
		$field->form	=	$form;
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
		self::_processing( $process, $fields, $storages, $config );
	}

	// onCCK_FieldBeforeRenderForm
	public static function onCCK_FieldBeforeRenderForm( $process, &$fields, &$storages, &$config = array() )
	{
		self::_processing( $process, $fields, $storages, $config );
	}

	// _process
	protected static function _processing( $process, &$fields, &$storages, &$config = array() )
	{
		$name		=	$process['name'];
		$value		=	$process['processing'];

		$processing	=	JCckDatabaseCache::loadObject( 'SELECT scriptfile, options FROM #__cck_more_processings WHERE id = '.(int)$value.' AND published = 1' );

		if ( is_object( $processing ) && $processing->scriptfile && is_file( JPATH_SITE.$processing->scriptfile ) ) {
			$options	=	new Registry( $processing->options );

			include JPATH_SITE.$processing->scriptfile;
		}
	}
}
?>