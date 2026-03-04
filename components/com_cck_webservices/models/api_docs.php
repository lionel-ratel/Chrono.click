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
use Joomla\CMS\MVC\Model\ListModel;

// Model
class CCK_WebservicesModelApi_Docs extends ListModel
{
	public function getItems()
	{
		$and	=	'';
		$app	=	Factory::getApplication();

		if ( $app_folder = $app->input->getInt( 'app', 0 ) ) {
			$and	=	' AND folder = '.$app_folder;
		}

		$and	.=	' AND methods LIKE "%GET%"';

		$query	=	'SELECT title, name, type, methods, options'
				.	' FROM #__cck_more_webservices_resources'
				.	' WHERE published = 1'
				.	$and
				.	' ORDER BY name, title'
				;

		$i		=	0;
		$items	=	JCckDatabase::loadObjectList( $query );
		$list	=	array();

		if ( !count( $items ) ) {
			return $list;
		}

		foreach ( $items as $item ) {
			if ( strpos( $item->methods, ',' ) !== false ) {
				$methods	=	explode( ',', $item->methods );

				foreach ( $methods as $method ) {
					$list[$i]			=	clone $item;
					$list[$i]->methods	=	$method;
					$i++;
				}
			} else {
				$list[$i++]	=	$item;

				if ( $item->methods == 'GET' ) {
					$list[$i]			=	clone $item;
					$i++;
				}
			}
		}

		return $list;
	}
}
?>