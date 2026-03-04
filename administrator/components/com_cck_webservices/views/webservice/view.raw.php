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

// View
class CCK_WebservicesViewWebservice extends JCckBaseLegacyViewForm
{
	protected $form;
	protected $item;
	protected $state;
	protected $vName	=	'webservice';
	
	// display
	public function display( $tpl = null )
	{		
		switch ( $this->getlayout() ) {
			case 'delete':
				$this->prepareDelete();
				break;
			case 'edit':
		case 'edit2':
				$this->prepareDisplay();
				break;
			default:
				break;
		}
		
		parent::display( $tpl );
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Display
	
	// prepareDisplay
	protected function prepareDisplay()
	{
		$app			=	Factory::getApplication();
		$model 			=	$this->getModel();
		$this->form		=	$this->get( 'Form' );
		$this->item		=	$this->get( 'Item' );
		$this->option	=	$app->input->get( 'option', '' );
		$this->state	=	$this->get( 'State' );
		
		// Check Errors
		if ( count( $errors	= $this->get( 'Errors' ) ) ) {
			throw new Exception( implode( "\n", $errors ), 500 );
		}
		
		$this->isNew			=	( @$this->item->id > 0 ) ? 0 : 1;
		$this->item->published	=	Helper_Admin::getSelected( $this->vName, 'state', ( ( ( $this->isNew ) ? $this->state->get( 'ajax.state' ) : $this->item->published ) ), 1 );
		$this->item->type		=	Helper_Admin::getSelected( $this->vName, 'type', $app->input->getString( 'ajax_type', $this->state->get( 'ajax.type', $this->item->type ) ), 'http' );
		
		Helper_Admin::addToolbarEdit( $this->vName, _C1_TEXT, array( 'isNew'=>$this->isNew, 'checked_out'=>$this->item->checked_out, 'layout'=>$this->getLayout() ) );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Delete
	
	// prepareDelete
	protected function prepareDelete()
	{
		Helper_Admin::addToolbarDelete( $this->vName, strtoupper( $this->vName ), 0 );
	}
}
?>