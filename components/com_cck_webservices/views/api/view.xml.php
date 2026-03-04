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

use Joomla\Registry\Registry;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView;

// View
class CCK_WebservicesViewApi extends HtmlView
{
	// display
	public function display( $tpl = null )
	{
		$app		=	Factory::getApplication();
		$data		=	$this->get( 'Data' );
		
		// Prepare
		$xml		=	new JCckDevXml( '<root />' );

		if ( JCckWebservice::getConfig_Param( 'resources_output', '' ) == 'wrapped' ) {
			$xml->addChild( 'code', $data['code'] );
			$xml->addChild( 'datetime', $data['datetime'] );
			$xml->addChild( 'status', $data['status'] );	

			$xml_items	=	$xml->addChild( 'data' );

			if ( count( $data['data'] ) ) {
				foreach ( $data['data'] as $data_item ) {
					$item	=	$xml_items->addChild( 'item' );

					if ( is_object( $data_item ) ) {
						foreach ( get_object_vars( $data_item ) as $k=>$v ) {
							if ( is_string( $v ) ) {
								$item->addChild( $k, $v );
							}
						}
					}
				}
			}
		} else {
			if ( count( $data ) ) {
				foreach ( $data as $data_item ) {
					$item	=	$xml->addChild( 'item' );

					if ( is_object( $data_item ) ) {
						foreach ( get_object_vars( $data_item ) as $k=>$v ) {
							if ( is_string( $v ) ) {
								$item->addChild( $k, $v );
							}
						}
					}
				}
			}
		}
				
		$this->data	=	$xml->asXML();

		// Set
		if ( JCckWebservice::getConfig_Param( 'resources_cors' ) ) {
			$app->setHeader( 'Access-Control-Allow-Origin', '*', true );
		}
		if ( JCckWebservice::getConfig_Param( 'resources_offline' ) ) {
			if ( !isset( $app->cck_app ) ) {
				$app->cck_app		=	array();
			}
			if ( !isset( $app->cck_app['Header'] ) ) {
				$app->cck_app['Header']		=	array();
			}

			$app->cck_app['Header']['Status']	=	'200 OK'; /* TODO#SEBLOD: proper status code */
		} else {
			if ( Factory::getConfig()->get( 'offline' ) ) {
				$app->close();
			} elseif ( JCck::isSite() ) {
				$site	=	JCck::getSite();

				if ( is_object( $site ) ) {
					$params	=	new Registry( $site->configuration );

					if ( $params->get( 'offline') ) {
						$app->close();
					}
				}
			}
		}

		parent::display( $tpl );
	}
}
?>