<?php
defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;

if ( !isset( $options ) ) {
	return;
}

$live 	=	Factory::getApplication()->input->server->get( 'HTTP_REFERER', '', 'RAW' );
?>