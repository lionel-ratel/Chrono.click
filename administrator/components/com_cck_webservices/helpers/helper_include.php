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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;

require_once JPATH_ADMINISTRATOR.'/components/'.CCK_COM.'/helpers/common/include.php';

// Helper
class Helper_Include extends CommonHelper_Include
{	
	// addDependencies
	public static function addDependencies( $view, $layout, $tmpl = '' )
	{
		$doc	=	Factory::getDocument();
		
		parent::addDependencies( $view, $layout, $tmpl );
		
		// Additional
		switch ( $view ) {
			case 'calls':
			case 'resources':
			case 'webservices':
				require_once JPATH_LIBRARIES.'/cck/joomla/html/cckactionsdropdown.php';

				JCck::loadjQuery();
				
				HTMLHelper::_( 'bootstrap.tooltip' );
				HTMLHelper::_( 'behavior.multiselect' );

				if ( !JCck::on( '4' ) ) {
					HTMLHelper::_( 'formbehavior.chosen', 'select:not(.no-chosen)' );
				}
				break;
			case CCK_NAME:
				$doc->addStyleSheet( Uri::root( true ).'/administrator/components/com_cck/assets/css/cpanel.css' );
				break;
			default:
				break;
		}
	}
	
	// addStyleSheets
	public static function addStyleSheets( $component, $paths = array() )
	{
		$paths	=	array( 'media/cck/css/definitions/all.css',
						   'administrator/components/'.CCK_ADDON.'/assets/css/admin.css',
						   'administrator/components/'.CCK_ADDON.'/assets/css/icons.css' );
		
		parent::addStyleSheets( $component, $paths );
	}
}
?>