<?php
/**
* @version 			SEBLOD WebServices 1.x
* @package			SEBLOD WebServices Add-on for SEBLOD 3.x
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;

// Plugin
class plgCCK_Storage_LocationCck_Webservice extends JCckPluginLocation
{
	protected static $type			=	'cck_webservice';
	protected static $table			=	'';
	protected static $table_object	=	array();
	protected static $key			=	'id';
	
	protected static $access		=	'';
	protected static $author		=	'';
	protected static $author_object	=	'';
	protected static $bridge_object	=	'';
	protected static $child_object	=	'';
	protected static $created_at	=	'';
	protected static $custom		=	'';
	protected static $modified_at	=	'';
	protected static $parent		=	'';
	protected static $parent_object	=	'';
	protected static $status		=	'';
	protected static $to_route		=	'';
	
	protected static $context		=	'';
	protected static $context2		=	'';
	protected static $contexts		=	array();
	protected static $error			=	false;
	protected static $events		=	array(
											'afterDelete'=>'',
											'afterSave'=>'',
											'beforeDelete'=>'',
											'beforeSave'=>''
										);
	protected static $ordering		=	array();
	protected static $ordering2		=	array();
	protected static $pk			=	0;
	protected static $routes		=	array();
	protected static $sef			=	array();

	// -------- -------- -------- -------- -------- -------- -------- -------- // Construct
	
	// onCCK_Storage_LocationConstruct
	public function onCCK_Storage_LocationConstruct( $type, &$data = array() )
	{
		if ( self::$type != $type ) {
			return;
		}
		if ( is_array( $data[self::$type] ) ) {
			foreach ( $data[self::$type] as $k=>$v ) {
				$data[$k]	=	$v;
			}
		}
		$data['alterTable']		=	false;
		$data['core_table']		=	'';
		$data['core_columns']	=	array();
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_Storage_LocationPrepareContent
	public function onCCK_Storage_LocationPrepareContent( &$field, &$storage, $pk = 0, &$config = array(), &$row = null )
	{
		if ( self::$type != $field->storage_location ) {
			return;
		}
	}
	
	// onCCK_Storage_LocationPrepareForm
	public function onCCK_Storage_LocationPrepareForm( &$field, &$storage, $pk = 0, &$config = array() )
	{
		if ( self::$type != $field->storage_location ) {
			return;
		}

		// Init
		$table	=	$field->storage_table;

		static $tables	=	array();

		if ( !isset( $tables[$table] ) ) {
			$tables[$table]	=	new stdClass;
			$webservice		=	JCckWebService::getCall( $table );

			if ( is_object( $webservice ) ) {
				$fields		=	array();

				PluginHelper::importPlugin( 'cck_webservice' );
				Factory::getApplication()->triggerEvent( 'onCCK_WebserviceCall', array( &$webservice, $fields, $config ) );

				if ( isset( $webservice->response, $webservice->response[0] ) ) {
					$tables[$table]	=	$webservice->response[0];
				}

				if ( isset( $tables[$table]->id ) && (int)$tables[$table]->id ) {
					$config['pk']	=	(int)$tables[$table]->id;
				}
			}
		}

		// Set
		$storage	=	$tables[$table];
	}
	
	// onCCK_Storage_LocationPrepareItems
	public function onCCK_Storage_LocationPrepareItems( &$field, &$storages, $pks, &$config = array(), $load = false )
	{
		if ( self::$type != $field->storage_location ) {
			return;
		}
		
		$config['author']	=	0;
	}
	
	// onCCK_Storage_LocationPrepareList
	public static function onCCK_Storage_LocationPrepareList( &$params )
	{
	}
	
	// onCCK_Storage_LocationPrepareOrder
	public function onCCK_Storage_LocationPrepareOrder( $type, &$order, &$tables, &$config = array() )
	{
		if ( self::$type != $type ) {
			return;
		}
	}
	
	// onCCK_Storage_LocationPrepareSearch
	public function onCCK_Storage_LocationPrepareSearch( $type, &$query, &$tables, &$t, &$config, &$inherit, $user )
	{
		if ( self::$type != $type ) {
			return;
		}
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Store
	
	// onCCK_Storage_LocationDelete
	public static function onCCK_Storage_LocationDelete( $pk, &$config = array() )
	{
		/* TODO#SEBLOD: */
		
		return false;
	}
	
	// onCCK_Storage_LocationSearch
	public function onCCK_Storage_LocationSearch( $type, $tables, $fields, $fields_order, &$config, &$inherit, &$results )
	{
		if ( self::$type != $type ) {
			return;
		}

		// Init
		$config['doQuery']	=	false;
		$webservice_call	=	'';

		// Prepare
		if ( count( $tables ) ) {
			foreach ( $tables as $k=>$t ) {
				if ( isset( $t['location'] ) && $t['location'] == self::$type ) {
					$webservice_call	=	$k;
					break;
				}
			}
		}

		$webservice	=	JCckWebService::getCall( $webservice_call );

		if ( !is_object( $webservice ) ) {
			return;
		}
		
		if ( isset( $webservice->response_identifier ) && $webservice->response_identifier ) {
			$config['identifier']	=	$webservice->response_identifier;
		}
		
		// Process
		PluginHelper::importPlugin( 'cck_webservice' );
		Factory::getApplication()->triggerEvent( 'onCCK_WebserviceCall', array( &$webservice, $fields, $config ) );
		$inherit['query']	=	$webservice->query;

		if ( is_array( $webservice->response ) ) {
			$fallback		=	'set';
			$results		=	$webservice->response;

			if ( isset( $webservice->total ) ) {
				$config['total']	=	$webservice->total;
			}
		} else {
			$fallback		=	'get'; // TODO: validity check
		}

		// Fallback
		/*
		foreach ( $fields as $field_name=>$field ) {
			if ( $field->type == 'cck_webservice' && $field->match_mode != 'none'
			  && $field->storage == 'standard' && $field->storage_table && $field->storage_field ) {
				$options2	=	json_decode( $field->options2 );
				$identifier	=	isset( $options2->store_identifier ) && $options2->store_identifier ? $options2->store_identifier : '';

				if ( $identifier && isset( $fields[$identifier] ) && $fields[$identifier]->value ) {
					if ( $fallback == 'set' ) {
						JCckDatabase::execute( 'UPDATE '.$field->storage_table.' SET '.$field->storage_field.' = "'.JCckDatabase::escape( json_encode( $results ) ).'" WHERE id = '.(int)$fields[$identifier]->value );
					} else {
						$json	=	JCckDatabase::loadResult( 'SELECT '.$field->storage_field.' FROM '.$field->storage_table.' WHERE id = '.(int)$fields[$identifier]->value );

						if ( $json != '' ) {
							$results	=	json_decode( $json );
						}

						Factory::getApplication()->enqueueMessage( Text::_( 'COM_CCK_WEBSERVICE_SYNC_NOT_AVAILABLE' ), 'error' );
					}
				}

				break;
			}
		}
		*/
	}

	// onCCK_Storage_LocationStore
	public function onCCK_Storage_LocationStore( $type, $data, &$config = array(), $pk = 0 )
	{
		if ( static::$type != $type ) {
			return;
		}
		
		if ( isset( $config['primary'] ) && $config['primary'] != static::$type ) {
			return;
		}
		
		return 0;
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Protected
	
	//
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // SEF

	// buildRoute
	public static function buildRoute( &$query, &$segments, $config, $menuItem = null )
	{
	}
	
	// getRoute
	public static function getRoute( $item, $sef, $itemId, $config = array() )
	{
		$route		=	'';
		
		return Route::_( $route );
	}
	
	// getRouteByStorage
	public static function getRouteByStorage( &$storage, $sef, $itemId, $config = array() )
	{
		if ( isset( $storage[self::$table]->_route ) ) {
			return Route::_( $storage[self::$table]->_route );
		}
		
		if ( $sef ) {
			$storage[self::$table]->_route	=	''; /* TODO#SEBLOD: */
		} else {
			$storage[self::$table]->_route	=	''; /* TODO#SEBLOD: */
		}
		
		return Route::_( $storage[self::$table]->_route );
	}

	// parseRoute
	public static function parseRoute( &$vars, &$segments, $n, $config )
	{
	}

	// setRoutes
	public static function setRoutes( $items, $sef, $itemId )
	{
		if ( count( $items ) ) {
			foreach ( $items as $item ) {
				$item->link	=	self::getRoute( $item, $sef, $itemId );
			}
		}
	}
}
?>