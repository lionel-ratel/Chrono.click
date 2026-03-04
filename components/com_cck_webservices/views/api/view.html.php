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

use Joomla\CMS\MVC\View\HtmlView;

// View
class CCK_WebservicesViewApi extends HtmlView
{
	// display
	public function display( $tpl = null )
	{
		$this->data		=	'You must specify the API version, currently: "/v1 or /v2"';
		
		parent::display( $tpl );
	}
}
?>