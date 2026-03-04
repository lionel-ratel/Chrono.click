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

use Joomla\CMS\MVC\Controller\BaseController;

// Controller
class CCK_ToolboxController extends BaseController
{
	protected $text_prefix	=	'COM_CCK_TOOLBOX';
	
	// display
	public function display( $cachable = false, $urlparams = false )
	{
		parent::display( true );
	}
}
?>