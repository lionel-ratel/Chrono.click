<?php
defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

// Script
class pkg_jdumpInstallerScript
{
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
		$db		=	Factory::getDbo();
	
		$db->setQuery( 'SELECT extension_id FROM #__extensions WHERE name = "pkg_dump"' );
		$extension_id	=	(int)$db->loadResult();
		
		if ( !$extension_id ) {
			return;
		}
		
		require_once JPATH_ADMINISTRATOR.'/components/com_installer/models/manage.php';

		$extension_id	=	array( $extension_id );
		$model        	=	BaseDatabaseModel::getInstance( 'Manage', 'InstallerModel' );
		
		$model->remove( $extension_id );
	}

	// postflight
	public function postflight( $type, $parent )
	{
	}
}
?>