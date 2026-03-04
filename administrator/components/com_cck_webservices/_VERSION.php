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

// JCckWebservicesVersion
final class JCckWebservicesVersion extends JCckVersionObject
{
	public $RELEASE = '6.0';
	
	public $DEV_LEVEL = '4';
	
	public $DEV_STATUS = 'Beta';

	public $API_VERSION = array( 'v1'=>'1.0.0', 'v2'=>'1.0.0' );

	// getApiVersion
	public function getApiVersion( $version )
	{
		return ( isset( $this->API_VERSION[$version] ) ) ? $this->API_VERSION[$version] : '0';
	}
}
?>