<?php
/**
* @version 			SEBLOD Exporter 1.x
* @package			SEBLOD Exporter Add-on for SEBLOD 3.x
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;

// Controller
class CCK_ExporterController extends BaseController
{
	protected $default_view	=	'cck_exporter';
	
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
	
	// exportToCsv
	public function exportToCsv()
	{
		Session::checkToken() or exit( Text::_( 'JINVALID_TOKEN' ) );
				
		$model	=	$this->getModel( 'cck_exporter' );
		$params	=	ComponentHelper::getParams( 'com_cck_exporter' );
		$output	=	$params->get( 'output', 0 );
		
		if ( $file = $model->exportToCsv( $params ) ) {
			if ( $output > 0 ) {
				$this->setRedirect( CCK_LINK, Text::_( 'COM_CCK_SUCCESSFULLY_EXPORTED' ), 'message' );
			} else {
				$file	=	JCckDevHelper::getRelativePath( $file, false );
				$this->setRedirect( Uri::base().'index.php?option=com_cck&task=download&file='.$file );
			}
		} else {
			$this->setRedirect( CCK_LINK, Text::_( 'JERROR_AN_ERROR_HAS_OCCURRED' ), 'error' );
		}
	}
	
	// purge
	public function purge()
	{
		Session::checkToken() or exit( Text::_( 'JINVALID_TOKEN' ) );
		
		$params	=	ComponentHelper::getParams( 'com_cck_exporter' );
		if ( $params->get( 'output', 0 ) < 2 ) {
			$path	=	JPATH_SITE.'/'.$params->get( 'output_path', 'tmp/' );

			if ( is_dir( $path ) ) {
				$files	=	Folder::files( $path );
				if ( count( $files ) ) {
					foreach ( $files as $file ) {
						if ( $file != 'index.html' ) {
							File::delete( $path.$file );
						}
					}
				}
			}
		}
		
		$this->setRedirect( CCK_LINK, Text::_( 'COM_CCK_SUCCESSFULLY_PURGED' ), 'message' );
	}
}
?>