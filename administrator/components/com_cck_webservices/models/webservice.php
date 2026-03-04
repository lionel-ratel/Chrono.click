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
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Table\Table;

// Model
class CCK_WebservicesModelWebservice extends AdminModel
{
	protected $text_prefix	=	'COM_CCK';
	protected $vName		=	'webservice';
	
	// populateState
	protected function populateState()
	{
		$app	=	Factory::getApplication( 'administrator' );
		$pk		=	$app->input->getInt( 'id', 0 );
		
		if ( $ajaxType	=	(string)$app->getUserState( CCK_COM.'.edit.field.ajax_type' ) ) {
			$this->setState( 'ajax.type', $ajaxType );
		}
		
		$this->setState( 'webservice.id', $pk );
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
			//
		}
		
		return $item;
	}
	
	// getTable
	public function getTable( $type = 'Webservice', $prefix = CCK_TABLE, $config = array() )
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
		
		PluginHelper::importPlugin( 'cck_webservice' );
		$app->triggerEvent( 'onCCK_WebserviceConstruct', array( $data['type'], &$data ) );
		
		return $data;
	}
}
?>