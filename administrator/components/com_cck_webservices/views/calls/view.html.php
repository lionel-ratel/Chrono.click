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
class CCK_WebservicesViewCalls extends JCckBaseLegacyViewList
{
	protected $vName	=	'call';
	protected $vTitle	=	_C2_TEXT;

	// prepareDisplay
	public function prepareDisplay()
	{
		$app				=	Factory::getApplication();
		$this->items		=	$this->get( 'Items' );
		$this->option		=	$app->input->get( 'option', '' );
		$this->pagination	=	$this->get( 'Pagination' );
		$this->state		=	$this->get( 'State' );
		
		$where				=	(int)$this->state->get( 'filter.state2', '1' );
		if ( $where > -1 ) {
			$where			=	' WHERE a.published	= '.(int)$where;
		} else {
			$where			=	'';
		}

		$this->definitions	=	JCckDatabase::loadObjectList( 'SELECT a.*, b.name AS editor'
															. ' FROM #__cck_more_webservices AS a'
															. ' LEFT JOIN #__users AS b on b.id = a.checked_out'
															. $where
															. ' ORDER BY a.title' ); //#
		$this->def_calls	=	JCckDatabase::loadObjectList( 'SELECT a.webservice, COUNT( a.webservice ) AS num FROM #__cck_more_webservices_calls AS a GROUP BY a.webservice', 'webservice' );
	}
}
?>