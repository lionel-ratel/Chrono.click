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
class CCK_WebservicesModelIntegration_App extends AdminModel
{
	protected $text_prefix	=	'COM_CCK';
	protected $vName		=	'integration_app';
	
	// populateState
	protected function populateState()
	{
		$app	=	Factory::getApplication( 'administrator' );
		$pk		=	$app->input->getInt( 'id', 0 );
		
		$this->setState( 'integration_app.id', $pk );
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
	public function getTable( $type = 'Integration_App', $prefix = CCK_TABLE, $config = array() )
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
		$data['auth_id']		=	(int)$data['auth_id'];
		
		if ( !$data['run_as_mode'] ) {
			$data['run_as']	=	0;
		}
		if ( is_array( $data['methods'] ) ) {
			$data['methods']	=	implode( ',', $data['methods'] );
		} else {
			$data['methods']	=	'';
		}
		
		/* TODO#SEBLOD: call generic->store = JSON */
		if ( isset( $data['json'] ) && is_array( $data['json'] ) ) {
			foreach ( $data['json'] as $k => $v ) {
				if ( is_array( $v ) ) {
					$data[$k]	=	JCckDev::toJSON( $v );
				}
			}
		}

		if ( $data['type'] == 'platform' ) {
			if ( !$data['id'] ) {
				if ( JCck::on( '4' ) ) {
					require_once JPATH_SITE.'/libraries/cck/base/app_crypt.php';
				} else {
					require_once JPATH_SITE.'/libraries/cck/base/app_crypt3.php';
				}

				$crypt	=	new JCckAppCrypt;
				$crypt	=	$crypt->init( true );

				$data['nonce']	=	$crypt['nonce'];

				$app->enqueueMessage( 'Public Key: '.$crypt['public'].'<br>Private Key: '.$crypt['private'] );
			} else {
				unset( $data['nonce'] );
			}
		}

		return $data;
	}
}
?>