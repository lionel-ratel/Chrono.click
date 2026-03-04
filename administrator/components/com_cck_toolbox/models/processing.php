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

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;

// Model
class CCK_ToolboxModelProcessing extends AdminModel
{
	protected $text_prefix	=	'COM_CCK';
	protected $vName		=	'processing';

	// canDelete
	protected function canDelete( $record )
	{
		$user	=	Factory::getUser();
		
		if ( ! empty( $record->folder ) ) {
			// Folder Permissions
			return $user->authorise( 'core.delete', 'com_cck.folder.'.(int)$record->folder );
		}

		// Component Permissions
		return parent::canDelete( $record );
	}

	// canEditState
	protected function canEditState( $record )
	{
		$user	=	Factory::getUser();

		if ( ! empty( $record->folder ) ) {
			// Folder Permissions
			return $user->authorise( 'core.edit.state', 'com_cck.folder.'.(int)$record->folder );
		}

		// Component Permissions
		return parent::canEditState( $record );
	}
	
	// populateState
	protected function populateState()
	{
		$app	=	Factory::getApplication( 'administrator' );
		$pk		=	$app->input->getInt( 'id', 0 );
		
		$this->setState( 'processing.id', $pk );
	}
	
	// getForm
	public function getForm( $data = array(), $loadData = true )
	{
		$form	=	$this->loadForm( CCK_ADDON.'.'.$this->vName, $this->vName, array( 'control' => 'jform', 'load_data' => $loadData ) );
		if ( empty( $form ) ) {
			return false;
		}
		
		return $form;
	}
	
	// getItem
	public function getItem( $pk = null )
	{
		if ( $item = parent::getItem( $pk ) ) {
			if ( isset( $item->scriptfile ) && $item->scriptfile[0] == '/' ) {
				$item->scriptfile	=	substr( $item->scriptfile, 1 );
			}
		}
		
		return $item;
	}
	
	// getTable
	public function getTable( $type = 'Processing', $prefix = CCK_TABLE, $config = array() )
	{
		return Table::getInstance( $type, $prefix, $config );
	}
	
	// loadFormData
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data	=	Factory::getApplication()->getUserState( CCK_ADDON.'.edit.'.$this->vName.'.data', array() );

		if ( empty( $data ) ) {
			$data	=	$this->getItem();
		}

		return $data;
	}
	
	// prepareTable
	protected function prepareTable( $table )
	{
		$data	=	$this->prepareData();
		
		$table->bind( $data );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Store
	
	// prepareData
	protected function prepareData()
	{
		$app					=	Factory::getApplication();
		$data					=	$app->input->post->getArray();
		$data['description']	=	$app->input->post->get( 'description', '', 'raw' );
		$data['json']			=	$app->input->post->get( 'json', '', 'raw' );

		if ( isset( $data['scriptfile'] ) ) {
			if ( $data['scriptfile'][0] != '/' ) {
				$data['scriptfile']	=	'/'.$data['scriptfile'];
			}
			if ( strpos( $data['scriptfile'], '.php' ) === false ) {
				$length	=	strlen( $data['scriptfile'] );
				if ( $data['scriptfile'][$length - 1] != '/' ) {
					$data['scriptfile']	.=	'/';
				}
				$data['scriptfile']	.=	$data['name'].'/'.$data['name'].'.php';
			}
		}
		
		/* TODO#SEBLOD: call generic->store = JSON */
		if ( isset( $data['json'] ) && is_array( $data['json'] ) ) {
			foreach ( $data['json'] as $k => $v ) {
				if ( is_array( $v ) ) {
					$data[$k]	=	JCckDev::toJSON( $v );
				}
			}
		}
		
		return $data;
	}
}
?>