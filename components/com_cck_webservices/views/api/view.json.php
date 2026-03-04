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
		$this->data	=	json_encode( $data );
		
		if ( $callback = (string)$app->input->getCmd( 'callback' ) ) {
			$this->data	=	$callback.'('.$this->data.')';

			$app->setHeader( 'Content-Type', 'text/javascript' );
		}

		// Set
		if ( JCckWebservice::getConfig_Param( 'resources_cors' ) ) {
			$app->setHeader( 'Access-Control-Allow-Origin', JCckWebservice::getConfig_Param( 'resources_cors_origin', '*' ), true );

			if ( JCckWebservice::getConfig_Param( 'resources_cors_credentials' ) ) {
				$app->setHeader( 'Access-Control-Allow-Credentials', 'true', true );
			}
		}
		if ( $access_control_allow_headers = JCckWebservice::getConfig_Param( 'resources_cors_headers', '' ) ) {
			$app->setHeader( 'Access-Control-Allow-Headers', $access_control_allow_headers, true );
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