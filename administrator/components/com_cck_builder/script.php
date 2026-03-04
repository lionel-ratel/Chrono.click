<?php
/**
* @version 			SEBLOD Builder 1.x
* @package			SEBLOD Builder  Add-on for SEBLOD 3.x
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;

// Script
class com_cck_builderInstallerScript extends JCckInstallerScriptComponent
{
	protected $cck;
	protected $cck_title	=	'Builder';
	protected $cck_name		=	'cck_builder';
	
	// install
	public function install( $parent )
	{
		parent::install( $parent );
	}
	
	// uninstall
	public function uninstall( $parent )
	{
		parent::uninstall( $parent );
	}
	
	// update
	public function update( $parent )
	{
		parent::update( $parent );
	}
	
	// preflight
	public function preflight( $type, $parent )
	{
		parent::preflight( $type, $parent );
	}
	
	// postflight
	public function postflight( $type, $parent )
	{
		$db		=	Factory::getDbo();
		
		$db->setQuery( 'SELECT manifest_cache FROM #__extensions WHERE element = "com_cck"' );
		$res	=	$db->loadResult();
		$reg	=	new JRegistry;
		$reg->loadString( $res );
		$v		=	substr( $reg->get( 'version', '2.0.0' ), 0, 5 );
		
		
		CCK_Install::manageAddon( $type, array( 'title'=>$this->cck_title, 'name'=>$this->cck_name ) );
		CCK_Install::import( $parent, 'admin/install', $this->cck );
		
		if ( $type == 'install' ) {
			$rule	=	'{"core.admin":{"7":1},"core.manage":{"6":1}}';
			$query	=	'UPDATE #__assets SET rules = "'.$db->escape( $rule ).'" WHERE name = "'.(string)$this->cck->element.'"';
			$db->setQuery( $query );
			$db->execute();
		}
	}
}
?>