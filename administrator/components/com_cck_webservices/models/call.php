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
class CCK_WebservicesModelCall extends AdminModel
{
	protected $text_prefix	=	'COM_CCK';
	protected $vName		=	'call';
	
	// populateState
	protected function populateState()
	{
		$app	=	Factory::getApplication( 'administrator' );
		$pk		=	$app->input->getInt( 'id', 0 );
		
		$this->setState( 'call.id', $pk );
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
			$item->webservice_type	=	( $item->id ) ? JCckDatabase::loadResult( 'SELECT type FROM #__cck_more_webservices WHERE id = '.(int)$item->webservice ) : null;
		}
		
		return $item;
	}
	
	// getTable
	public function getTable( $type = 'Call', $prefix = CCK_TABLE, $config = array() )
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
		
		$request_labels				=	true;
		$request_options			=	array();

		/*
		"request_options" is used as a legacy layer for http (v1)
		*/

		if ( isset( $data['json'] ) && is_array( $data['json'] ) ) {
			if ( isset( $data['json']['options']['input'] ) ) {
				$this->_prepareDataX( $data, 'input' );

				unset( $data['json']['options']['input_params'] );

				foreach ( $data['json'] as $k=>$v ) {
					if ( is_array( $v ) ) {
						$data[$k]	=	JCckDev::toJSON( $v );
					}
				}
			} else {
				if ( @$data['json']['options'] ) {
					$data['options']	=	JCckDev::toJSON( $data['json']['options'] );
				}
				foreach ( $data['json']['request_options'] as $k=>$v ) {
					if ( !isset( $data['options_'.$k] ) ) {
						$request_labels	=	false;
						break;
					}
					if ( $data['options_'.$k] != '' ) {
						$request_options[$k]	=	$v;
					}
				}

				$data['request_options']	=	( $request_labels ) ? json_encode( $request_options ) : json_encode( $data['json']['request_options'] );
			}
		}

		if ( strpos( $data['request_options'], '"type":"field"' ) !== false || strpos( $data['request_options'], '"type":"site"' ) !== false ) {
			$data['standalone']		=	0;
		} else {
			if ( strpos( $data['request'], '{id}' ) !== false
			  || strpos( $data['request'], '$cck-' ) !== false
			  || strpos( $data['request'], '$uri-' ) !== false ) {
				$data['standalone']		=	0;
			} else {
				$data['standalone']		=	1;
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