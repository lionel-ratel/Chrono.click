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

use Joomla\Filesystem\Folder;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;

// Script
class pkg_cck_toolboxInstallerScript
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
	}
	
	// postflight
	public function postflight( $type, $parent )
	{
		$app	=	Factory::getApplication();
		$db		=	Factory::getDbo();
		
		$db->setQuery( 'UPDATE #__extensions SET enabled = 1 WHERE type = "plugin" AND element = "cck_toolbox"' );
		$db->execute();

		$params =	ComponentHelper::getParams( 'com_cck_toolbox' );
		$root	=	JPATH_ADMINISTRATOR.'/components/com_cck_toolbox/';

		// SQL
		if ( $type == 'install' ) {
			// Set Initial Version
			$params->set( 'initial_version', $app->cck_core_version );
			$db->setQuery( 'UPDATE #__extensions SET params = "'.$db->escape( $params ).'" WHERE name = "com_cck_toolbox"' );
			$db->execute();

			// Delete Front-end Modules
			$query	=	'DELETE a.* FROM #__modules AS a WHERE a.module IN ("mod_cck_processing")';
			$db->setQuery( $query );
			$db->execute();

			// Publish Plugins
			$query		=	'UPDATE #__extensions SET enabled = 1 WHERE element LIKE "cck_processing"';
			$db->setQuery( $query );
			$db->execute();
		} else {
			$latest		=	$params->get( 'latest_update', '' );
			$now		=	'';
			$path		=	$root.'/install/upgrades';

			$files		=	Folder::files( $path, '.', true, true, array( 'index.html' ) );
			$versions	=	array();

			if ( count( $files ) ) {
				foreach ( $files as $file ) {
					if ( is_file( $file ) ) {
						$datetime	=	'';
						$name		=	substr( basename( $file ), 0, -4 );
						$pos		=	strpos( $name, '-' );
						$version	=	'';

						if ( $pos !== false ) {
							$parts		=	explode( '-', $name );
							$datetime	=	substr( $name, $pos + 1 );
							$version	=	substr( $name, 0, $pos );
						} else {
							$version	=	$name;
						}

						if ( version_compare( $version, $app->cck_core_version_old, '<' ) ) {
							continue;
						} elseif ( version_compare( $version, $app->cck_core_version_old, '=' ) ) {
							if ( $datetime == '' ) {
								continue;
							}
							if ( $latest != '' ) {
								$date1		=	new DateTime( $datetime, new DateTimeZone( 'UTC' ) );
								$date1->setTime( 00, 00, 00 );
								$date2		=	new DateTime( $latest, new DateTimeZone( 'UTC' ) );
								$date2->setTime( 00, 00, 00 );

	        					if ( $date1 <= $date2 ) {
	        						continue;
	        					} else {
	        						$now	=	$datetime;
	        					}
        					}
						}
						if ( version_compare( $version, $app->cck_core_version, '>' ) ) {
							continue;
						}

						$idx	=	(float)str_replace( '.', '', $version );

						if ( !isset( $versions[$idx] ) ) {
							$versions[$idx]	=	array();
						}
						$versions[$idx][]	=	$file;
					}
				}
			}
			ksort( $versions );
			
			if ( count( $versions ) ) {
				foreach ( $versions as $version ) {
					if ( count( $version ) ) {
						foreach ( $version as $item ) {
							if ( is_file( $item ) ) {
								$buffer		=	file_get_contents( $item );
								$queries	=	$db->splitSql( $buffer );
								
								foreach ( $queries as $query ) {
									$query	=	trim( $query );
									if ( $query != '' && $query[0] != '#' ) {
										$db->setQuery( $query );
										$db->execute();
									}
								}
							}
						}
					}
				}
			}

			$date1		=	new DateTime( $now, new DateTimeZone( 'UTC' ) );
			$date1->setTime( 00, 00, 00 );
			$date2		=	new DateTime( 'now', new DateTimeZone( 'UTC' ) );
			$date2->setTime( 00, 00, 00 );
			
			if ( $date1 < $date2 ) {
				$now	=	'now';
			}

			$params->set( 'latest_update', Factory::getDate( $now )->format( 'Y-m-d' ) );
			$db->setQuery( 'UPDATE #__extensions SET params = "'.$db->escape( $params ).'" WHERE name = "com_cck_toolbox"' );
			$db->execute();
		}
	}
}
?>