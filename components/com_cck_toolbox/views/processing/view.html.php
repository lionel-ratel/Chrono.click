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
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;

// View
class CCK_ToolboxViewProcessing extends HtmlView
{
	// display
	public function display( $tpl = null )
	{
		$app				=	Factory::getApplication();
		$config				=	Factory::getConfig();
		$data				=	'';
		$model				=	$this->getModel();
		$option				=	$app->input->get( 'option', '' );
		$params				=	$app->getParams();
		$processing			=	$model->getProcessing( $params->get( 'processing', 0 ) );

		JCck::loadjQuery();

		// Page
		$menus	=	$app->getMenu();
		$menu	=	$menus->getActive();

		if ( is_object( $menu ) ) {
			$menu_params	=	new Registry;
			$menu_params->loadString( $menu->getParams() );
			if ( ! $menu_params->get( 'page_title' ) ) {
				$params->set( 'page_title', $menu->title );
			}
		} else {
			$params->set( 'page_title', 'List' );
		}
		
		// Set Title
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

		$this->pageclass_sfx	=	htmlspecialchars( $params->get( 'pageclass_sfx' ) );
		$this->raw_rendering	=	$params->get( 'raw_rendering', JCck::getConfig_Param( 'raw_rendering', '1' ) );

		// Prepare
		if ( $processing ) {
			if ( $processing->scriptfile != '' && is_file( JPATH_SITE.$processing->scriptfile ) ) {
				$options	=	new Registry( $processing->options );
				
				ob_start();
				include_once JPATH_SITE.$processing->scriptfile;
				$data		=	ob_get_clean();
			}
		}

		// Set Meta
		$description	=	$params->get( 'menu-meta_description' );
		
		if ( $description == '' ) {
			$description	=	$params->get( 'page_desc', @$processing->description );
			$description	=	strip_tags( $description );
			$description	=	JCckDevHelper::truncate( $description, 200 );
		}

		if ( $description ) {
			$this->document->setDescription( $description );
		}
		if ( $params->get( 'menu-meta_keywords' ) ) {
			$this->document->setMetadata( 'keywords', $params->get('menu-meta_keywords' ) );
		}
		if ( $params->get( 'robots' ) ) {
			$this->document->setMetadata( 'robots', $params->get( 'robots' ) );
		}

		// // Set
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
			$this->title				=	( isset( $processing->title ) ) ? $processing->title : '';
		}

		$this->show_page_desc			=	$params->get( 'show_page_desc' );
		if ( $this->show_page_desc == '' ) {
			$this->show_page_desc		=	$options->get( 'show_page_desc', '1' );
			$this->description			=	@$processing->description;
		} elseif ( $this->show_page_desc ) {
			$this->description			=	$params->get( 'page_desc', @$processing->description );
		} else {
			$this->description			=	'';
		}

		// Force Titles to be hidden
		if ( $app->input->get( 'tmpl' ) == 'raw' ) {
			$params->set( 'show_page_heading', 0 );
			$this->show_page_title	=	false;
		}

		$this->class_desc	=	$params->get( 'class_page_desc', '' );
		$this->data			=	&$data;
		$this->option		=	&$option;
		$this->params		=	&$params;
		$this->processing	=	&$processing;
		$this->tag_desc		=	$params->get( 'tag_page_desc', 'div' );

		parent::display( $tpl );
	}
}
?>