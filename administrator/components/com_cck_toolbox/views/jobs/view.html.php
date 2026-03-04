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

use Joomla\CMS\Language\Text;

// View
class CCK_ToolboxViewJobs extends JCckBaseLegacyViewList
{
	protected $vName	=	'job';
	protected $vTitle	=	_C4_TEXT;

	// getSortFields
	protected function getSortFields()
	{
		return array(
					'folder_title'=>Text::_( 'COM_CCK_APP_FOLDER' ),
					'a.id'=>Text::_( 'COM_CCK_ID' ),
					'a.published'=>Text::_( 'COM_CCK_STATUS' ),
					'a.title'=>Text::_( 'COM_CCK_TITLE' )
				);
	}
}
?>