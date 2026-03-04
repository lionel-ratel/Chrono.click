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
class plgCCK_Field_RestrictionX extends JCckPluginRestriction
{
	protected static $type	=	'x';
	
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
		$do				=	true;
		
		if ( isset( $config['task'] ) ) {
			$events		=	array(
								'admin'=>'onCCK_Field_RestrictionPrepareStore',
								'site'=>'onCCK_Field_RestrictionPrepareStore',
								'content'=>'onCCK_Field_RestrictionPrepareContent',
								'intro'=>'onCCK_Field_RestrictionPrepareContent',
								'item'=>'onCCK_Field_RestrictionPrepareContent',
								'list'=>'onCCK_Field_RestrictionPrepareContent'
							);
		} else {
			$events		=	array(
								'admin'=>'onCCK_Field_RestrictionPrepareForm',
								'content'=>'onCCK_Field_RestrictionPrepareContent',
								'intro'=>'onCCK_Field_RestrictionPrepareContent',
								'item'=>'onCCK_Field_RestrictionPrepareContent',
								'list'=>'onCCK_Field_RestrictionPrepareContent',
								'search'=>'onCCK_Field_RestrictionPrepareForm',
								'site'=>'onCCK_Field_RestrictionPrepareForm',
							);
		}
		$process		=	array();
		$restrictions	=	$restriction->toArray();
		$todo			=	array();
		
		if ( !isset( $restrictions['conditions'] ) ) {
			return $do;
		}
		if ( count( $restrictions['conditions'] ) ) {
			if ( isset( $config['process'] ) && count( $config['process'] ) ) {
				foreach ( $config['process'] as $k=>$v ) {
					$process[$k]	=	count( $v );
				}
			}
			$options	=	$field->restriction_options;
			
			foreach ( $restrictions['conditions'] as $condition ) {
				$field->restriction			=	$condition['trigger'];
				$field->restriction_options	=	json_encode( $condition['options'] );

				$res	=	JCck::callFunc_Array( 'plgCCK_Field_Restriction'.$condition['trigger'], $events[$config['client']], array( &$field, &$config ) );
				
				if ( $res === false ) {
					$field->restriction			=	'x';
					$field->restriction_options	=	$options;
							
					return false;
				} else {
					if ( isset( $config['process'] ) && count( $config['process'] ) ) {
						foreach ( $config['process'] as $k=>$v ) {
							$count	=	count( $v );

							if ( $count > @$process[$k] ) {
								if ( !isset( $todo[$k] ) ) {
									$todo[$k]	=	array();
								}
								$todo[$k][]	=	array_pop( $config['process'][$k] );
								break;
							}
						}
					}
				}
			}
			if ( count( $todo ) ) {
				foreach ( $todo as $k=>$v_restrictions ) {
					$priority		=	3;
					$priority_base	=	10;

					foreach ( $v_restrictions as $v_restriction ) {
						if ( isset( $v_restriction->priority ) && $v_restriction->priority && $v_restriction->priority < $priority_base ) {
							$priority_base	=	$v_restriction->priority;
						}
					}
					if ( $priority_base > $priority && $priority_base != 10 ) {
						$priority	=	$priority_base;
					}

					parent::g_addProcess( $k, self::$type, $config, array( 'name'=>$field->name, 'event'=>$k, 'restrictions'=>$v_restrictions ), $priority );
				}
			}
			$field->restriction			=	'x';
			$field->restriction_options	=	$options;
		}

		return $do;
	}

	// _authoriseBeforeEvent
	protected static function _authoriseBeforeEvent( $process, &$fields, &$storages, &$config = array() )
	{
		$do		=	true;
		$name	=	$process['name'];

		if ( count( $process['restrictions'] ) ) {
			foreach ( $process['restrictions'] as $k=>$p ) {
				if ( is_object( $p ) ) {
					if ( !JCck::callFunc_Array( 'plg'.$p->group.$p->type, 'on'.$p->group.$process['event'], array( $p->params, &$fields, &$storages, &$config ) ) ) {
						$do	=	false;
						
						break;
					}	
				}
			}
		}
		if ( !$do ) {
			if ( isset( $fields[$name] ) ) {
				$fields[$name]->display	=	0;
				$fields[$name]->state	=	0;	
			}

			return false;
		} else {
			return true;
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

	// onCCK_Field_RestrictionBeforeSearch
	public static function onCCK_Field_RestrictionBeforeSearch( $process, &$fields, &$storages, &$config = array() )
	{
		return self::_authoriseBeforeEvent( $process, $fields['search'], $storages, $config );
	}

	// onCCK_Field_RestrictionBeforeStore
	public static function onCCK_Field_RestrictionBeforeStore( $process, &$fields, &$storages, &$config = array() )
	{
		return self::_authoriseBeforeEvent( $process, $fields, $storages, $config );
	}
}
?>