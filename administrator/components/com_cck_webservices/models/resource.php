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
use Joomla\CMS\Table\Table;

// Model
class CCK_WebservicesModelResource extends AdminModel
{
	protected $text_prefix	=	'COM_CCK';
	protected $vName		=	'resource';
	
	// populateState
	protected function populateState()
	{
		$app	=	Factory::getApplication( 'administrator' );
		$pk		=	$app->input->getInt( 'id', 0 );
		
		$this->setState( 'resource.id', $pk );
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
	public function getTable( $type = 'Resource', $prefix = CCK_TABLE, $config = array() )
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
		$data['format']			=	$data['representation'];
		unset( $data['representation'] );

		if ( is_array( $data['methods'] ) ) {
			$data['methods']	=	implode( ',', $data['methods'] );
		} else {
			$data['methods']	=	'';
		}

		if ( isset( $data['json'] ) && is_array( $data['json'] ) ) {
			if ( $data['json']['options']['content_type'] ) {
				$data['json']['options']['storage_location']	=	JCckDatabase::loadResult( 'SELECT storage_location FROM #__cck_core_types WHERE name = "'.$data['json']['options']['content_type'].'"' );
			}

			$this->_prepareDataX( $data, 'input' );
			$this->_prepareDataX( $data, 'output' );

			unset( $data['json']['options']['input_params'] );
			unset( $data['json']['options']['output_params'] );

			foreach ( $data['json'] as $k=>$v ) {
				if ( is_array( $v ) ) {
					$data[$k]	=	JCckDev::toJSON( $v );
				}
			}
		}

		return $data;
	}

	// _prepareDataX
	protected function _prepareDataX( &$data, $index )
	{
		if ( isset( $data['json']['options'][$index] ) ) {
			if ( isset( $data['json']['options'][$index.'_params'] ) ) {
				$options	=	array();

				if ( count( $data['json']['options'][$index.'_params'] ) ) {
					foreach ( $data['json']['options'][$index.'_params'] as $option ) {
						$options[]	=	$option;
					}
				}

				$data['json']['options'][$index.'_params']	=	$options;
			}

			$v2	=	array();

			foreach ( $data['json']['options'][$index] as $k=>$v ) {
				if ( $v != '' ) {
					$v2[$v]	=	$data['json']['options'][$index.'_params'][$k];
				}
			}

			$data['json']['options'][$index]	=	$v2;
		}
	}
}
?>