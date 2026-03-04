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
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;

// Controller
class CCK_WebservicesController extends BaseController
{
	protected $default_view	=	'cck_webservices';
	
	// display
	public function display( $cachable = false, $urlparams = false )
	{
		$app	=	Factory::getApplication();
		$id		=	$app->input->getInt( 'id' );
		$layout	=	$app->input->get( 'layout', 'default' );
		$view	=	$app->input->get( 'view', $this->default_view );
		
		if ( !( $layout == 'edit' || $layout == 'edit2' || $layout == 'call' ) ) {
			Helper_Admin::addSubmenu( $this->default_view, $view );
		}
		
		if ( ( $view == 'resource' && $layout == 'edit' && ! $this->checkEditId( CCK_ADDON.'.edit.resource', $id ) ) ||
			 ( $view == 'webservice' && $layout == 'edit' && ! $this->checkEditId( CCK_ADDON.'.edit.webservice', $id ) ) ||
			 ( $view == 'call' && ( $layout == 'edit' || $layout == 'call' ) && ! $this->checkEditId( CCK_ADDON.'.edit.call', $id ) ) ) {
			$this->setMessage( Text::sprintf( 'JLIB_APPLICATION_ERROR_UNHELD_ID', $id ), 'error' );
			$this->setRedirect( Route::_( CCK_LINK.'&view='.$view.'s', false ) );
			
			return false;
		}
		
		require_once JPATH_ADMINISTRATOR.'/components/com_cck/helpers/helper_folder.php';

		parent::display();
		
		return $this;
	}
}
?>