<?php
/**
* @version 			SEBLOD Toolbox 1.x
* @package			SEBLOD Toolbox Add-on for SEBLOD 3.x
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

require_once JPATH_ADMINISTRATOR.'/components/'.CCK_COM.'/helpers/common/admin.php';

// Helper
class Helper_Admin extends CommonHelper_Admin
{
	// addSubmenu
	public static function addSubmenu( $option, $vName )
	{
		$items	=	array(
						array( 'val'=>'3', 'pre'=>'', 'key'=>'' ),
						array( 'val'=>'4', 'pre'=>'', 'key'=>'' )
					);

		self::addSubmenuEntries( $option, $vName, $items );
	}

	// addToolbar
	public static function addToolbar( $vName, $vTitle ) 
	{
		$bar	=	Toolbar::getInstance( 'toolbar' );
		$canDo	=	self::getActions();
		$class	=	'cck-seblod'; // $vName.'s.png'
		
		require_once JPATH_ADMINISTRATOR.'/components/com_cck/helpers/toolbar/separator.php';
		
		ToolbarHelper::title( Text::_( $vTitle.'_MANAGER' ), $class );
		
		if ( $canDo->get( 'core.create' ) || $canDo->get( 'core.edit' ) ) {
			if ( $canDo->get( 'core.create' ) ) {
				ToolbarHelper::custom( $vName.'.add', 'new', 'new', 'JTOOLBAR_NEW', false );
			}
			if ( $canDo->get( 'core.edit' ) ) {
				ToolbarHelper::custom( $vName.'.edit', 'edit', 'edit', 'JTOOLBAR_EDIT', true );
			}
			$bar->appendButton( 'CckSeparator' );
		}
		if ( $canDo->get( 'core.edit.state' ) || $canDo->get( 'core.delete' ) ) {
			if ( $canDo->get( 'core.edit.state' ) ) {
				ToolbarHelper::custom( $vName.'s'.'.publish', 'publish', 'publish', 'COM_CCK_TURN_ON', true );
				ToolbarHelper::custom( $vName.'s'.'.unpublish', 'unpublish', 'unpublish', 'COM_CCK_TURN_OFF', true );
			}
			if ( $canDo->get( 'core.delete' ) ) {
				ToolbarHelper::custom( $vName.'s'.'.delete', 'delete', 'delete', 'JTOOLBAR_DELETE', true );
			}
			if ( $canDo->get( 'core.edit.state' ) ) {
				ToolbarHelper::custom( $vName.'s'.'.checkin', 'checkin', 'checkin', 'JTOOLBAR_CHECKIN', true);
			}
		}
	}
	
	// addToolbarEdit
	public static function addToolbarEdit( $vName, $vTitle, $vMore = '', $params = array() ) 
	{
		Factory::getApplication()->input->set( 'hidemainmenu', true );
		
		$bar		=	Toolbar::getInstance( 'toolbar' );
		$canDo		=	self::getActions();
		$class		=	'cck-seblod'; // $vName.'s.png'
		$user		=	Factory::getUser();
		$vSubtitle	=	'';
		$checkedOut	= 	! ( $vMore['checked_out'] == 0 || $vMore['checked_out'] == $user->id );
		
		if ( $vMore['isNew'] )  {
			ToolbarHelper::title( Text::_( $vTitle ).': <small><small>[ '.Text::_( 'COM_CCK_ADD' ).' ]</small></small>', $class );
			
			if ( $canDo->get('core.create') ) {
				ToolbarHelper::custom( $vName.'.apply', 'apply', 'apply', 'JTOOLBAR_APPLY', false );
				ToolbarHelper::custom( $vName.'.save', 'save', 'save', 'JTOOLBAR_SAVE', false );
				ToolbarHelper::custom( $vName.'.save2new', 'save-new', 'save-new', 'JTOOLBAR_SAVE_AND_NEW', false );
			}
			ToolbarHelper::custom( $vName.'.cancel', 'cancel', 'cancel', 'JTOOLBAR_CANCEL', false );
		} else {
			ToolbarHelper::title( Text::_( $vTitle ).': <small><small>[ '.Text::_( 'JTOOLBAR_EDIT' ).' ]</small></small>', $class );
			
			if (!$checkedOut) {
				if ( $canDo->get('core.edit') ) {
					ToolbarHelper::custom( $vName.'.apply', 'apply', 'apply', 'JTOOLBAR_APPLY', false );
					ToolbarHelper::custom( $vName.'.save', 'save', 'save', 'JTOOLBAR_SAVE', false );
					if ( $canDo->get('core.create' ) ) {
						ToolbarHelper::custom( $vName.'.save2new', 'save-new', 'save-new', 'JTOOLBAR_SAVE_AND_NEW', false );
					}
				}
			}
			ToolbarHelper::custom( $vName.'.cancel', 'cancel', 'cancel', 'JTOOLBAR_CLOSE', false );
		}
	}

	// getActions
	public static function getActions( $folderId = 0 )
	{
		$user		=	Factory::getUser();
		$result		=	new CMSObject;
		
		$assetName	=	'com_'.CCK_NAME;
		
		$actions	=	array( 'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.state', 'core.delete' );
		foreach ( $actions as $action ) {
			$result->set( $action, $user->authorise( $action, $assetName ) );
		}
		
		return $result;
	}
}
?>