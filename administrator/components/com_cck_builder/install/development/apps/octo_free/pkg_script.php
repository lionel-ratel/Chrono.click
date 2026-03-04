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
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Table\Menu as MenuTable;
use Joomla\Database\DatabaseInterface;

// Script
class pkg_app_cck_%prefix%_%name%InstallerScript extends JCckInstallerScriptApp
{
	public function install( $parent )
	{
		dumpVar( $parent, '--install--');
	}

	// uninstall
	public function uninstall( $parent )
	{
		dumpVar( $parent, '--uninstall--');
	}

	// update
	public function update( $parent )
	{
		dumpVar( $parent, '--update--');
	}

	// preflight
	public function preflight( $type, $parent )
	{
		if ( !defined( 'DS' ) ) {
			define( 'DS', DIRECTORY_SEPARATOR );
		}
		set_time_limit( 0 );

		dumpVar( $type, '--preflight- Type--');
		dumpVar( $parent, '--preflight- Parent--');
	}

	// postflight
	public function postflight( $type, $parent )
	{
		dumpVar( $type, '--postflight- Type--');
		dumpVar( $parent, '--postflight- Parent--');
	}
}
?>