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

use Joomla\CMS\MVC\Controller\FormController;

// Controller
class CCK_WebservicesControllerIntegration_App extends FormController
{
	protected $text_prefix	=	'COM_CCK';
	protected $view_list	=	'integrations';
}
?>