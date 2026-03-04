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

use Joomla\Registry\Registry;
use Joomla\CMS\Factory;
 
jimport( 'cck.base.install.install' );
 
// Script
class com_cck_toolboxInstallerScript
{
	protected $cck;
	protected $cck_title	=	'Toolbox';
	protected $cck_name		=	'cck_toolbox';

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
			$query	=	'UPDATE #__assets SET rules = "'.$db->escape( $rule ).'" WHERE name = "'.(string)$this->cck->element.'"';
			$db->setQuery( $query );
			$db->execute();
		}
	}

	// _getVersion
	protected function _getVersion( $default = '2.0.0' )
	{
		$db		=	Factory::getDbo();
		
		$db->setQuery( 'SELECT manifest_cache FROM #__extensions WHERE element = "com_cck_toolbox" AND type = "component"' );
		
		$res		=	$db->loadResult();
		$registry	=	new Registry;
		$registry->loadString( $res );
		
		return $registry->get( 'version', $default );
	}
}
?>