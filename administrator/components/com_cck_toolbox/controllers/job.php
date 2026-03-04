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

use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

// Controller
class CCK_ToolboxControllerJob extends FormController
{
	protected $text_prefix	=	'COM_CCK';

	// __construct
	public function __construct( $config = array() )
	{
		parent::__construct( $config );

		$this->registerTask( 'run', 'edit' );
	}

	// allowAdd
	protected function allowAdd( $data = array() )
	{
		$app		=	Factory::getApplication();
		$user		=	Factory::getUser();
		$folderId	=	ArrayHelper::getValue( $data, 'folder', $app->input->getInt( 'filter_folder_id' ), 'int' );
		$allow		=	null;
		
		if ( $folderId ) {
			// Folder Permissions
			$allow	=	$user->authorise( 'core.create', 'com_cck.folder.'.$folderId );
		}
		
		if ( $allow !== null ) {
			return $allow;
		}

		// Component Permissions
		return parent::allowAdd( $data );
	}

	// allowEdit
	protected function allowEdit( $data = array(), $key = 'id' )
	{
		$user		=	Factory::getUser();
		$recordId	=	(int)isset( $data[$key] ) ? $data[$key] : 0;
		$folderId	=	0;
		
		if ( $recordId ) {
			$folderId	=	(int)$this->getModel()->getItem( $recordId )->folder;
		}
		
		if ( $folderId ) {
			// Folder Permissions
			return $user->authorise( 'core.edit', 'com_cck.folder.'.$folderId );
		}

		// Component Permissions
		return parent::allowEdit( $data, $key );
	}

	// postSaveHook
	protected function postSaveHook( BaseDatabaseModel $model, $validData = array() )
	{
		$recordId	=	$model->getState( $this->context.'.id' );
		
		if ( $recordId ) {
			$model->postStore( $recordId );
		}
	}
}
?>