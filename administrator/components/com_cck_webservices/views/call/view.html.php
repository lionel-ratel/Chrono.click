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
class CCK_WebservicesViewCall extends JCckBaseLegacyViewForm
{
	protected $form;
	protected $item;
	protected $state;
	protected $vName	=	'call';
	protected $vTitle	=	_C2_TEXT;
	
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
		$this->item->options	=	json_decode( $this->item->options, true );

		Helper_Admin::addToolbarEdit( $this->vName, _C2_TEXT, array( 'isNew'=>$this->isNew, 'checked_out'=>$this->item->checked_out, 'layout'=>$this->getLayout() ) );
	}
}
?>