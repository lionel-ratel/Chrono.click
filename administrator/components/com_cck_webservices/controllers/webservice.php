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
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Session\Session;

// Controller
class CCK_WebservicesControllerWebservice extends FormController
{
	protected $text_prefix	=	'COM_CCK';
	protected $view_list	=	'calls';
	
	// add
	public function add()
	{
		$app	=	Factory::getApplication();

		// Parent Method
		$result	=	parent::add();

		if ( $result instanceof Exception ) {
			return $result;
		}
		
		// Additional Vars
		$app->setUserState( CCK_COM.'.edit.field.ajax_type', $app->input->getString( 'ajax_type', '' ) );
	}

	// cancel
	public function cancel( $key = null )
	{
		Session::checkToken() or exit( Text::_( 'JINVALID_TOKEN' ) );
		
		$app	=	Factory::getApplication();
		
		parent::cancel();
		
		$app->setUserState( CCK_COM.'.edit.field.ajax_type', null );
	}
	
	// edit
	public function edit( $key = null, $urlVar = null )
	{
		$app	=	Factory::getApplication();
		
		// Parent Method
		$result	=	parent::edit();

		if ( $result instanceof Exception ) {
			return $result;
		}
		
		// Additional Vars
		$app->setUserState( CCK_COM.'.edit.field.ajax_type', $app->input->getString( 'ajax_type', '' ) );
	}
	
	// postSaveHook
	protected function postSaveHook( BaseDatabaseModel $model, $validData = array() )
	{
		$app	=	Factory::getApplication();
		$task	=	$this->getTask();
		
		switch ( $task )
		{
			case 'save2new':
				$app->setUserState( CCK_COM.'.edit.field.ajax_type', $model->getItem()->type );
				break;
			default:
				$app->setUserState( CCK_COM.'.edit.field.ajax_type', null );
				break;
		}
	}
}
?>