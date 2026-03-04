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
class plgCCK_Field_RestrictionSql_Query extends JCckPluginRestriction
{
	protected static $type	=	'sql_query';
	
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
		$do			=	$restriction->get( 'do', 0 );
		$event		=	( $config['client'] == 'admin' || $config['client'] == 'site' || $config['client'] == 'search' ) ? 'beforeRenderForm' : 'beforeRenderContent';
		$group_by	=	$restriction->get( 'group_by', '' );
		$query		=	$restriction->get( 'query', '' );

		if ( $query != '' ) {
			$query	=	JCckDevHelper::replaceLive( $query, '', $config );
			if ( isset( $config['pks'] ) && isset( $config['ids'] ) ) {
				$query	=	str_replace( '[ids]', $config['ids'], $query );
				$query	=	str_replace( '[pks]', $config['pks'], $query );	
			}
			$query	=	str_replace( '[id]', @$config['id'], $query );
			$query	=	str_replace( '[pk]', @$config['pk'], $query );

			if ( $query != '' && strpos( $query, '$cck->get' ) !== false ) {
				$matches	=	'';
				$search		=	'#\$cck\->get([a-zA-Z0-9_]*)\( ?\'([a-zA-Z0-9_,]*)\' ?\)(;)?#';
				preg_match_all( $search, $query, $matches );
				if ( count( $matches[1] ) ) {
					parent::g_addProcess( $event, self::$type, $config, array( 'name'=>$field->name, 'matches'=>$matches, 'query'=>$query, 'restriction'=>$restriction ) );
				}
			} else {
				if ( isset( $group_by ) && $group_by != '' ) {
					static $cache	=	array();
					
					if ( !isset( $cache[$field->name] ) ) {
						$cache[$field->name]	=	JCckDatabase::loadObjectList( $query, $group_by );
					}
					if ( isset( $cache[$field->name][$config['pk']] ) ) {
						if ( $cache[$field->name][$config['pk']]->value ) {
							$do	=	( $do ) ? false : true;
						} else {
							$do	=	( $do ) ? true : false;
						}
					}
				} else {
					if ( JCckDatabase::loadResult( $query ) ) {
						$do		=	( $do ) ? false : true;
					} else {
						$do		=	( $do ) ? true : false;
					}
				}

				return $do;
			}
		}

		return true;
	}

	// _authoriseBeforeRender
	protected static function _authoriseBeforeRender( $process, &$fields, &$storages, &$config = array() )
	{
		$do		=	$process['restriction']->get( 'do', 0 );
		$name	=	$process['name'];
		$query	=	$process['query'];

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
				$query		=	str_replace( $process['matches'][0][$k], $value, $query );
			}
			if ( strpos( $query, '$cck->get' ) === false ) {
				if ( JCckDatabase::loadResult( $query ) ) {
					$fields[$name]->authorised	=	( $do ) ? false : true;
				} else {
					$fields[$name]->authorised	=	( $do ) ? true : false;
				}
				if ( !$fields[$name]->authorised ) {
					$fields[$name]->display		=	0;
					$fields[$name]->state		=	0;

					return false;
				} else {
					return true;
				}
			}
		}
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Special Events
	
	// onCCK_Field_RestrictionBeforeRenderContent
	public static function onCCK_Field_RestrictionBeforeRenderContent( $process, &$fields, &$storages, &$config = array() )
	{
		return self::_authoriseBeforeRender( $process, $fields, $storages, $config );
	}

	// onCCK_Field_RestrictionBeforeRenderForm
	public static function onCCK_Field_RestrictionBeforeRenderForm( $process, &$fields, &$storages, &$config = array() )
	{
		return self::_authoriseBeforeRender( $process, $fields, $storages, $config );
	}
}
?>