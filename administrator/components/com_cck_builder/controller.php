<?php
/**
* @version 			SEBLOD Builder 1.x
* @package			SEBLOD Builder Add-on for SEBLOD 3.x
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Session\Session;

// Controller
class CCK_BuilderController extends BaseController
{
	protected $default_view	=	'cck_builder';
		
	// createApp
	public function createApp()
	{
		Session::checkToken() or jexit( Text::_( 'JINVALID_TOKEN' ) );
		
		$model	=	$this->getModel( 'cck_builder' );
		$params	=	ComponentHelper::getParams( 'com_cck_builder' );
		$output	=	$params->get( 'output', 0 );
		
		if ( $file = $model->createApp( $params ) ) {
			if ( $output > 0 ) {
				$this->setRedirect( CCK_LINK, Text::_( 'COM_CCK_SUCCESSFULLY_CREATED' ), 'message' );
			} else {
				$file	=	JCckDevHelper::getRelativePath( $file, false );
				$this->setRedirect( JUri::base().'index.php?option=com_cck&task=download&file='.$file );
			}
		} else {
			$this->setRedirect( CCK_LINK, Text::_( 'JERROR_AN_ERROR_HAS_OCCURRED' ), 'error' );
		}
	}

	// display
	public function display( $cachable = false, $urlparams = false )
	{
		$app	=	Factory::getApplication();
		$id		=	$app->input->getInt( 'id' );
		$layout	=	$app->input->get( 'layout', 'default' );
		$view	=	$app->input->get( 'view', $this->default_view );
		
		if ( !( $layout == 'edit' || $layout == 'edit2' ) ) {
			Helper_Admin::addSubmenu( $this->default_view, $view );
		}
		
		parent::display();
		
		return $this;
	}
}
?>