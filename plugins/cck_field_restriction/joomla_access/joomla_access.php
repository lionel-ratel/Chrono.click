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

// Plugin
class plgCCK_Field_RestrictionJoomla_Access extends JCckPluginRestriction
{
	protected static $type	=	'joomla_access';
	
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
		$access		=	$restriction->get( 'access', '' );
		$do			=	$restriction->get( 'do', 0 );
		$user		=	Factory::getUser();
		$viewlevels	=	$user->getAuthorisedViewLevels();
		
		if ( $access == '' ) {
			$location	=	$config['location'];
			if ( $location ) {
				require_once JPATH_SITE.'/plugins/cck_storage_location/'.$location.'/'.$location.'.php';
				$properties	=	array( 'access', 'key', 'table' );
				$properties	=	JCck::callFunc( 'plgCCK_Storage_Location'.$location, 'getStaticProperties', $properties );
				if ( $properties['access'] && $properties['key'] && $properties['table'] ) {
					$access	=	JCckDatabase::loadResult( 'SELECT '.$properties['access'].' FROM '.$properties['table'].' WHERE '.$properties['key'].' = '.(int)$config['pk'] );
				}
			}
		}
		
		if ( $access ) {
			if ( !in_array( $access, $viewlevels ) ) {
				return ( $do ) ? true : false;
			}
		}
		
		return ( $do ) ? false : true;
	}
}
?>