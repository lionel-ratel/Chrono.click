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

// View
class CCK_ToolboxViewProcessing extends JCckBaseLegacyViewForm
{
	protected $vName	=	'processing';
	protected $vTitle	=	_C3_TEXT;
	
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
		$this->item->published	=	Helper_Admin::getSelected( $this->vName, 'state', $this->item->published, 1 );

		Helper_Admin::addToolbarEdit( $this->vName, _C3_TEXT, array( 'isNew'=>$this->isNew, 'checked_out'=>$this->item->checked_out ) );
	}
}
?>