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
class plgCCK_Field_RestrictionCck_Processing extends JCckPluginRestriction
{
	protected static $type	=	'cck_processing';
	
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
		$name		=	$field->name;
		$processing	=	$restriction->get( 'processing', '' );
		$priority	=	(int)$restriction->get( 'priority', '' );

		if ( $priority ) {
			if ( isset( $config['client_form'] ) ) {
				$event	=	'beforeRenderForm';
			} else {
				$event	=	'beforeRenderContent';
			}
			
			parent::g_addProcess( $event, self::$type, $config, array( 'name'=>$name, 'restriction'=>$restriction ), $priority );

			return true;
		}

		$do		=	$restriction->get( 'do', 0 );

		// Prepare
		if ( $processing && JCckToolbox::getConfig()->get( 'processing', 0 ) ) {
			$processing	=	JCckDatabase::loadObject( 'SELECT scriptfile, options FROM #__cck_more_processings WHERE id = '.(int)$processing.' AND published = 1' );

			if ( is_object( $processing ) && $processing->scriptfile && is_file( JPATH_SITE.$processing->scriptfile ) ) {
				$options		=	new Registry( $processing->options );
				$restriction 	=	true;
				
				include JPATH_SITE.$processing->scriptfile;

				if ( $restriction ) {
					$do		=	( $do ) ? false : true;
				} else {
					$do		=	( $do ) ? true : false;
				}
			}
		} else {
			//
		}

		if ( $do ) {
			return true;
		} else {
			$field->display	=	0;
			// $field->state	=	0;
			return false;
		}
	}

	// _authoriseBeforeEvent
	protected static function _authoriseBeforeEvent( $process, &$fields, &$storages, &$config = array() )
	{
		$name			=	$process['name'];
		$restriction	=	$process['restriction'];
		
		$do				=	$restriction->get( 'do', 0 );
		$processing		=	$restriction->get( 'processing', '' );

		// Prepare
		if ( $processing && JCckToolbox::getConfig()->get( 'processing', 0 ) ) {
			$processing	=	JCckDatabase::loadObject( 'SELECT scriptfile, options FROM #__cck_more_processings WHERE id = '.(int)$processing.' AND published = 1' );

			if ( $processing->scriptfile && is_file( JPATH_SITE.$processing->scriptfile ) ) {
				$options		=	new Registry( $processing->options );
				$restriction 	=	true;
				
				include JPATH_SITE.$processing->scriptfile;

				if ( $restriction ) {
					$do		=	( $do ) ? false : true;
				} else {
					$do		=	( $do ) ? true : false;
				}
			}
		} else {
			//
		}

		if ( $do ) {
			return true;
		} else {
			$fields[$name]->display	=	0;
			$fields[$name]->state	=	0;
			return false;
		}
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Special Events
	
	// onCCK_Field_RestrictionBeforeRenderContent
	public static function onCCK_Field_RestrictionBeforeRenderContent( $process, &$fields, &$storages, &$config = array() )
	{
		return self::_authoriseBeforeEvent( $process, $fields, $storages, $config );
	}

	// onCCK_Field_RestrictionBeforeRenderForm
	public static function onCCK_Field_RestrictionBeforeRenderForm( $process, &$fields, &$storages, &$config = array() )
	{
		return self::_authoriseBeforeEvent( $process, $fields, $storages, $config );
	}
}
?>