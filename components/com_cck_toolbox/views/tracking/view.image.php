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

use Joomla\Registry\Registry;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView;

// View
class CCK_ToolboxViewTracking extends HtmlView
{
	// display
	public function display( $tpl = null )
	{
		$app				=	Factory::getApplication();
		$model				=	$this->getModel();
		$params				=	$app->getParams();
		$processing			=	$model->getProcessing( $params->get( 'processing', 0 ) );

		if ( $processing ) {
			if ( $processing->scriptfile != '' && is_file( JPATH_SITE.$processing->scriptfile ) ) {
				$options	=	new Registry( $processing->options );
				
				ob_start();
				include_once JPATH_SITE.$processing->scriptfile;
				ob_get_clean();
			}
		}

		$this->data			=	file_get_contents( JPATH_SITE.'/media/cck/images/'.$app->input->getCmd( 'name', 'pixel' ).'.'.$app->input->getCmd( 'type', 'png' ) );

		parent::display( $tpl );
	}
}
?>