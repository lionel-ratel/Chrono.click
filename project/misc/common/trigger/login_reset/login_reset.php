<?php
defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;

if ( isset( $user['activation'] ) && $user['activation'] != '' ) {
	Factory::getApplication()->setUserState( 'reset_email', $user['email'] );
}
?>