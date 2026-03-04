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
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Table\Table;

// Model
class CCK_WebservicesModelWebservices extends ListModel
{
	// __construct
	public function __construct( $config = array() )
	{
		if ( empty( $config['filter_fields'] ) ) {
			$config['filter_fields']	=	array(
				'id', 'a.id',
				'title', 'a.title',
				'name', 'a.name',
				'type', 'a.type',
				'description', 'a.description',
				'published', 'a.published',
				'options', 'a.options',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
			);
		}

		parent::__construct( $config );
	}
	
	// getItems
	public function getItems()
	{
		if ( $items = parent::getItems() ) {
		}
		
		return $items;
	}
	
	// getListQuery
	protected function getListQuery()
	{
		$db		=	$this->getDbo();
		$query	=	$db->getQuery( true );	
		
		// Select
		$query->select (
			$this->getState (
				'list.select',
				'a.id as id,' .
				'a.title as title,' .
				'a.name as name,' .
				'a.type as type,' .
				'a.description as description,' .
				'a.published as published,' .
				'a.options as options,' .
				'a.checked_out as checked_out,' .
				'a.checked_out_time as checked_out_time'
			)
		);

		// From
		$query->from( '`#__cck_more_webservices` AS a' );
		
		// Join User
		$query->select( 'u.name AS editor' );
		$query->join( 'LEFT', '#__users AS u ON u.id = a.checked_out' );
		
		// Filter Type
		$type	=	$this->getState( 'filter.type' );
		if ( is_string( $type ) && $type != '' ) {
			$query->where( 'a.type = "'.(string)$type.'"' );
		}
		
		// Filter State
		$state	=	$this->getState( 'filter.state' );
		if ( is_numeric( $state ) && $state >= 0 ) {
			$query->where( 'a.published = '.(int)$state );
		}
		$query->where( 'a.published != -44' );
		
		// Filter Search
		$location	=	$this->getState( 'filter.location' );
		$search		=	$this->getState( 'filter.search' );
		if ( ! empty( $search ) ) {
			switch ( $location ) {
				case 'id':
					$where	=	( strpos( $search, ',' ) !== false ) ? 'a.id IN ('.$search.')' : 'a.id = '.(int)$search;
					$query->where( $where );
					break;
				default:
					$search	=	$db->quote( '%'.$db->escape( $search, true ).'%' );
					$query->where( 'a.'.$location.' LIKE '.$search );
					break;
			}
		}

		// Group By
		$query->group( 'a.id' );	
		
		// Order By
		$query->order( $db->escape( $this->state->get( 'list.ordering', 'a.title' ).' '.$this->state->get( 'list.direction', 'ASC' ) ) );
		
		return $query;
	}
	
	// getStoreId
	protected function getStoreId( $id = '' )
	{
		$id	.=	':' . $this->getState( 'filter.search' );
		$id	.=	':' . $this->getState( 'filter.location' );
		$id	.=	':' . $this->getState( 'filter.state' );
		$id	.=	':' . $this->getState( 'filter.type' );	
		$id	.=	':' . $this->getState( 'filter.mode' );

		return parent::getStoreId( $id );
	}

	// getTable
	public function getTable( $type = 'Webservice', $prefix = CCK_TABLE, $config = array() )
	{
		return Table::getInstance( $type, $prefix, $config );
	}

	// populateState
	protected function populateState( $ordering = null, $direction = null )
	{
		$app		=	Factory::getApplication( 'administrator' );
		$search		=	$app->getUserStateFromRequest( $this->context.'.filter.search', 'filter_search', '' );
		$location	=	$app->getUserStateFromRequest( $this->context.'.filter.location', 'filter_location', 'title' );
			
		$this->setState( 'filter.search', $search );
		$this->setState( 'filter.location', $location );
		
		$type		=	$app->getUserStateFromRequest( $this->context.'.filter.type', 'filter_type', '', 'string' );
		$this->setState( 'filter.type', $type );
		
		$state		=	$app->getUserStateFromRequest( $this->context.'.filter.state', 'filter_state', '', 'string' );
		$this->setState( 'filter.state', $state );
		
		$params		=	ComponentHelper::getParams( CCK_ADDON );
		$this->setState( 'params', $params );
		
		parent::populateState( 'a.title', 'asc' );
	}
}
?>