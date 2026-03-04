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
class CCK_TableStack extends Table
{
	// __construct
	public function __construct( &$db )
	{
		parent::__construct( '#__cck_more_webservices_stack', 'id', $db );
	}
	
	// check
	public function check()
	{
		if ( !$this->id ) {
			$this->stacked	=	Factory::getDate()->toSql();
		}
		
		return true;
	}

	// updateStatus
	public function updateStatus( $status )
	{
		if ( !$this->id ) {
			return false;
		}

		$data	=	array(
						'executed'=>Factory::getDate()->toSql(),
					);

		if ( $status === -2 ) {
			$data['published']	=	-2;
		} elseif ( $status ) {
			$data['published']	=	2;
		} else {
			$data['attempts']	=	(int)$this->attempts + 1;
		}

		$this->bind( $data );
		$this->check();
		
		return $this->store();
	}
}
?>