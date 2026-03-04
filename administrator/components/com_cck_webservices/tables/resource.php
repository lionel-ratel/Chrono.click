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
class CCK_TableResource extends Table
{
	// __construct
	public function __construct( &$db )
	{
		parent::__construct( '#__cck_more_webservices_resources', 'id', $db );
	}
	
	// check
	public function check()
	{
		$this->title	=	trim( $this->title );
		if ( empty( $this->title ) ) {
			return false;
		}
		if ( empty( $this->name ) ) {
			$this->name	=	$this->title;
			$this->name =	JCckDev::toSafeSTRING( $this->name );
			if( trim( str_replace( '_', '', $this->name ) ) == '' ) {
				$datenow	=	Factory::getDate();
				$this->name =	$datenow->format( 'Y_m_d_H_i_s' );
			}
		} elseif ( ( strpos( $this->name, '/' ) !== false ) && ( strpos( $this->name, '/' ) == 0 ) ) {
			$this->name	=	substr( $this->name, 1 );
		}

		if ( is_null( $this->format ) ) {
			$this->format	=	'';
		}
		if ( is_null( $this->options2 ) ) {
			$this->options2	=	'';
		}
		
		return true;
	}
}
?>