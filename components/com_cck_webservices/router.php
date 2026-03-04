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
use Joomla\CMS\Component\Router\RouterView;

// Cck_WebservicesRouter
class Cck_WebservicesRouter extends RouterView
{
	// build
	public function build( &$query )
	{
		return array();
	}

	// parse
	public function parse( &$segments )
	{
		$app	=	Factory::getApplication();
		$count	=	count( $segments );
		$i		=	1;
		$last	=	( $count > 1 ) ? $count - 1 : 0;
		$vars	=	array(
						'layout'=>'default',
						'option'=>'com_cck_webservices',
						'output'=>'',
						'version'=>$segments[0],
						'view'=>'api'
					);
		
		// Wrapped or not?
		if ( (int)JCckWebservice::getConfig_Param( 'resources_format_request', 0 ) == 1 ) {
			if ( $last && isset( $segments[$last] ) && strpos( $segments[$last], '.' ) !== false ) {
				$data				=	explode( '.', $segments[$last] );
				$segments[$last]	=	$data[0];
				if ( $data[1][0] == 'w' ) {
					$vars['output']	=	'wrapped';
				}
			}
		}

		array_shift( $segments );
		
		// TODO
		/*
		if ( $vars['version'] == 'v0' ) {
			$vars['entry_point']=	$segments[$i];
			$i++;
		}
		*/

		$keys	=	array( 'version', 'resource', 'id', 'relation', 'relation_id' );

		// Set vars
		for ( ; $i < $count; $i++ ) {
			$s	=	array_shift( $segments );

			if ( isset( $keys[$i] ) ) {
				$vars[$keys[$i]]	=	$s;
			}
		}

		/*
		/posts
		/products
		/products/:id
		/products/:id/stores
		/products/:id/stores/:id
		/products/:id/tags
		/products/:id/tags/:id
		/stores
		/stores/:id
		*/

		return $vars;
	}
}

// Cck_WebservicesBuildRoute
function Cck_WebservicesBuildRoute( &$query )
{
	$app	=	Factory::getApplication();
	$router	=	new Cck_WebservicesRouter( $app, $app->getMenu() );

	return $router->build( $query );
}

// Cck_WebservicesParseRoute
function Cck_WebservicesParseRoute( $segments )
{
	$app	=	Factory::getApplication();
	$router	=	new Cck_WebservicesRouter( $app, $app->getMenu() );

	return $router->parse( $segments );
}
?>