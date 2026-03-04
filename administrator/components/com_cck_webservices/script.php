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

use Joomla\Registry\Registry;
use Joomla\CMS\Factory;

jimport( 'cck.base.install.install' );

// Script
class com_cck_webservicesInstallerScript
{
	protected $cck;
	protected $cck_title	=	'WebServices';
	protected $cck_name		=	'cck_webservices';
	
	// install
	public function install( $parent )
	{
	}
	
	// uninstall
	public function uninstall( $parent )
	{
	}
	
	// update
	public function update( $parent )
	{		
	}
	
	// preflight
	public function preflight( $type, $parent )
	{
		$this->cck	=	(object)array( 'type'=>'component', 'element'=>'com_'.$this->cck_name );

		$app		=	Factory::getApplication();
		
		$app->cck_core				=	true;
		$app->cck_core_version_old	=	self::_getVersion();
		
		set_time_limit( 0 );
	}
	
	// postflight
	public function postflight( $type, $parent )
	{
		$app	=	Factory::getApplication();
		$db		=	Factory::getDbo();
		
		$app->cck_core_version		=	self::_getVersion();

		CCK_Install::manageAddon( $type, array( 'title'=>$this->cck_title, 'name'=>$this->cck_name ) );
		CCK_Install::import( $parent, 'admin/install', $this->cck );
		
		if ( $type == 'install' ) {
			$rule	=	'{"core.admin":{"7":1},"core.manage":{"6":1}}';
			JCckDatabase::execute( 'UPDATE #__assets SET rules = "'.JCckDatabase::escape( $rule ).'" WHERE name = "'.(string)$this->cck->element.'"' );
		}
	}
	
	// _getVersion
	protected function _getVersion( $default = '2.0.0' )
	{
		$db		=	Factory::getDbo();
		
		$db->setQuery( 'SELECT manifest_cache FROM #__extensions WHERE element = "com_cck_webservices" AND type = "component"' );
		
		$res		=	$db->loadResult();
		$registry	=	new Registry;
		$registry->loadString( $res );
		
		return $registry->get( 'version', $default );
	}
}
?>