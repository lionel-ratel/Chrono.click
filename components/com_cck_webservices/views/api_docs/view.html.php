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
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Router\Route;

// View
class CCK_WebservicesViewApi_Docs extends HtmlView
{
	// display
	public function display( $tpl = null )
	{
		$app	=	Factory::getApplication();
		$config	=	Factory::getConfig();
		$params	=	$app->getParams();

		$api_endpoint	=	(int)$params->get( 'resources_menu_item', 0 );
		$api_endpoint	=	$api_endpoint ? Route::_( 'index.php?Itemid='.$api_endpoint ) : '/api';
		$api_version	=	(string)$params->get( 'resources_version', 1 );

		// Page
		$menus	=	$app->getMenu();
		$menu	=	$menus->getActive();
		$home	=	( isset( $menu->home ) && $menu->home ) ? true : false;
		
		if ( is_object( $menu ) ) {
			$menu_params	=	new Registry;
			$menu_params->loadString( $menu->getParams() );
			if ( ! $menu_params->get( 'page_title' ) ) {
				$params->set( 'page_title', $menu->title );
			}
		} else {
			$params->set( 'page_title', 'List' );
		}
		$title	=	$params->get( 'page_title' );
		
		if ( empty( $title ) ) {
			$title	=	$config->get( 'sitename' );
		} elseif ( $config->get( 'sitename_pagetitles', 0 ) == 1 ) {
			$title	=	Text::sprintf( 'JPAGETITLE', $config->get( 'sitename' ), $title );
		} elseif ( $config->get( 'sitename_pagetitles', 0 ) == 2 ) {
			$title	=	Text::sprintf( 'JPAGETITLE', $title, $config->get( 'sitename' ) );
		}
		$config		=	null;
		$this->document->setTitle( $title );
		
		if ( $params->get( 'menu-meta_description' ) ) {
			$this->document->setDescription( $params->get( 'menu-meta_description' ) );
		}
		if ( $params->get( 'menu-meta_keywords' ) ) {
			$this->document->setMetadata( 'keywords', $params->get('menu-meta_keywords' ) );
		}
		if ( $params->get( 'robots' ) ) {
			$this->document->setMetadata( 'robots', $params->get( 'robots' ) );
		}
		$this->pageclass_sfx	=	htmlspecialchars( $params->get( 'pageclass_sfx' ) );

		// Set
		if ( !is_object( @$options ) ) {
			$options	=	new Registry;
		}
		$this->show_page_title			=	$params->get( 'show_page_title' );
		if ( $this->show_page_title == '' ) {
			$this->show_page_title		=	$options->get( 'show_page_title', '1' );
			$this->tag_page_title		=	$options->get( 'tag_page_title', 'h1' );
			$this->class_page_title		=	$options->get( 'class_page_title', JCck::getConfig_Param( 'title_class', '' ) );
		} elseif ( $this->show_page_title ) {
			$this->tag_page_title		=	$params->get( 'tag_page_title', 'h1' );
			$this->class_page_title		=	$params->get( 'class_page_title', JCck::getConfig_Param( 'title_class', '' ) );
		}
		if ( $params->get( 'display_page_title', '' ) == '3' ) {
			$this->title				=	Text::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $params->get( 'title_page_title', '' ) ) ) );
		} elseif ( $params->get( 'display_page_title', '' ) == '1' ) {
			$this->title				=	$params->get( 'title_page_title', '' );
		} elseif ( $params->get( 'display_page_title', '' ) == '0' ) {
			$this->title				=	$menu->title;
		} else {
			$this->title				=	'API';
		}

		$this->api_endpoint	=	&$api_endpoint;
		$this->api_version	=	&$api_version;
		$this->items		=	$this->get( 'Items' );
		$this->params		=	&$params;

		parent::display( $tpl );
	}

	// _getHtmlTableSection
	protected function _getHtmlTableSection( $section )
	{
		switch ( $section ) {
			case 'body':
				$html	=	'<tbody>';
				break;
			case 'end':
				$html	=	'</tbody></table>';
				break;
			case 'head':
				$html	=	'<thead>'
						.	'<tr>'
						.	'<th>'.Text::_( 'COM_CCK_PARAMETER' ).'</th>'
						.	'<th width="40%">'.Text::_( 'COM_CCK_VALUE' ).'</th>'
						.	'<th width="30%">'.Text::_( 'COM_CCK_TYPE' ).'</th>'
						.	'</tr>'
						.	'</thead>'
						;
				break;
			case 'table':
				$html	=	'<table class="'.( JCck::is( '4.0' ) ? 'o-table table' : 'table table-striped' ).'">';
				break;
			default:
				$html	=	'';
				break;
		}

		return $html;
	}

	// _setAuth
	protected function _setAuth()
	{
		$auth	=	JCckDatabase::loadObject( 'SELECT type, options FROM #__cck_more_webservices_auths WHERE published = 1' );

		if ( !is_object( $auth ) ) {
			return true;
		}

		$app		=	Factory::getApplication();
		$http_auth	=	'';
		$options	=	json_decode( $auth->options, true );

		switch ( $auth->type ) {
			case 'api_key':
				if ( $options['mode'] ) {
					//
				} else {
					//
				}

				break;
			case 'basic_auth':
				$http_auth	=	'Basic '.base64_encode( $options['username'].':'.$options['password'] );

				break;
			case 'token_auth':
				$http_auth	=	'Bearer '.$options['token'];

				break;
			default:
				break;
		}

		Factory::getDocument()->addScriptOptions( 'cck_ws_auth', $http_auth );
	}
}
?>