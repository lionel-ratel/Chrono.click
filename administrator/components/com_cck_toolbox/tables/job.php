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

use Joomla\Filesystem\File;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;

// Table
class CCK_TableJob extends Table
{
	// __construct
	public function __construct( &$db )
	{
		parent::__construct( '#__cck_more_jobs', 'id', $db );
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

		if ( is_null( $this->options ) ) {
			$this->options	=	'';
		}

		return true;
	}
	
	// delete
	public function delete( $pk = null )
	{
		if ( $this->id ) {
			if ( $this->name != '' && is_file( JPATH_SITE.'/cli/cck_job_'.$this->name.'.php' ) ) {
				File::delete( JPATH_SITE.'/cli/cck_job_'.$this->name.'.php' );
			}
		}
		
		return parent::delete();
	}
}
?>