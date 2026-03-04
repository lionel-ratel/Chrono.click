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
use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Language\Text;

// Cck_ToolboxRouter
class Cck_ToolboxRouter extends RouterView
{
	// build
	public function build( &$query )
	{
		return array();
	}

	// parse
	public function parse( &$segments )
	{
		$app		=	Factory::getApplication();
		$count		=	count( $segments );
		$last		=	( $count > 1 ) ? $count - 1 : 0;
		$menu		=	$app->getMenu();
		$menuItem	=	$menu->getActive();
		$vars		=	array(
							'layout'=>'default',
							'options'=>'com_cck_toolbox',
							'view'=>$menuItem->query['view']
						);

		if ( $menuItem->query['option'] == 'com_cck_toolbox' ) {
			if ( $menuItem->query['view'] == 'tracking' ) {
				$vars['format']	=	'image';

				if ( isset( $segments[$last] ) && strpos( $segments[$last], '.' ) !== false ) {
					$data			=	explode( '.', $segments[$last] );
					$vars['name']	=	$data[0];
					$vars['type']	=	$data[1];
				} else {
					$vars['name']	=	'pixel';
					$vars['type']	=	'png';
				}
			} elseif ( $count ) {
				throw new Exception( Text::_( 'JERROR_PAGE_NOT_FOUND' ), 404 );
			}
		}

		return $vars;
	}
}

// Cck_ToolboxBuildRoute
function Cck_ToolboxBuildRoute( &$query )
{
	$app	=	Factory::getApplication();
	$router	=	new Cck_ToolboxRouter( $app, $app->getMenu() );

	return $router->build( $query );
}

// Cck_ToolboxParseRoute
function Cck_ToolboxParseRoute( $segments )
{
	$app	=	Factory::getApplication();
	$router	=	new Cck_ToolboxRouter( $app, $app->getMenu() );

	return $router->parse( $segments );
}
?>