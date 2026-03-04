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

use Joomla\CMS\MVC\Controller\BaseController;

// Controller
class CCK_WebservicesController extends BaseController
{
	protected $text_prefix	=	'COM_CCK_WEBSERVICES';
	
	// display
	public function display( $cachable = false, $urlparams = false )
	{
		parent::display( true );
	}
}
?>