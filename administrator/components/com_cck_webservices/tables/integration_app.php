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

use Joomla\CMS\Table\Table;

// Table
class CCK_TableIntegration_App extends Table
{
	// __construct
	public function __construct( &$db )
	{
		parent::__construct( '#__cck_more_webservices_apps', 'id', $db );
	}

	// check
	public function check()
	{
		if ( is_null( $this->options ) ) {
			$this->options	=	'';
		}
		if ( is_null( $this->type ) ) {
			$this->type	=	'';
		}
		
		return true;
	}
}
?>