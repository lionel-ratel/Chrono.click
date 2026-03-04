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
use Joomla\CMS\Table\Table;

// Table
class CCK_TableCall extends Table
{
	// __construct
	public function __construct( &$db )
	{
		parent::__construct( '#__cck_more_webservices_calls', 'id', $db );
	}
	
	// check
	public function check()
	{
		$this->title	=	trim( $this->title );
		if ( empty( $this->title ) ) {
			return false;
		}
		if( empty( $this->name ) ) {
			$this->name	=	$this->title;
			$this->name =	JCckDev::toSafeSTRING( $this->name );
			if( trim( str_replace( '_', '', $this->name ) ) == '' ) {
				$datenow	=	Factory::getDate();
				$this->name =	$datenow->format( 'Y_m_d_H_i_s' );
			}
		}
		
		if ( is_null( $this->request ) ) {
			$this->request	=	'';
		}
		if ( is_null( $this->request_format ) ) {
			$this->request_format	=	'';
		}
		if ( is_null( $this->request_method ) ) {
			$this->request_method	=	'';
		}
		if ( is_null( $this->request_object ) ) {
			$this->request_object	=	'';
		}
		if ( is_null( $this->request_options ) ) {
			$this->request_options	=	'';
		}
		if ( is_null( $this->response ) ) {
			$this->response	=	'';
		}
		if ( is_null( $this->response_format ) ) {
			$this->response_format	=	'';
		}
		if ( is_null( $this->response_identifier ) ) {
			$this->response_identifier	=	'';
		}
		if ( is_null( $this->options ) ) {
			$this->options	=	'';
		}

		return true;
	}
}
?>