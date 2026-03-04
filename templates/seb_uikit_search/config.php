<?php
defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;

global $user;

$app		=	Factory::getApplication();
$path_lib	=	JPATH_SITE.'/libraries/cck/rendering/rendering.php';
$user		=	JCck::getUser();

if ( ! file_exists( $path_lib ) ) {
	print( '/libraries/cck/rendering/rendering.php file is missing.' );
	die;
}

require_once $path_lib;
?>