<?php
/**
* @version 			SEBLOD 3.x More
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/
defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;

include_once JPATH_SITE.'/plugins/cck_field/upload_file2/classes/upload_file2.php';

$app 	=	Factory::getApplication();
$mode 	=	$app->input->getString( 'mode', 'upload' );
$uuid 	=	$app->input->getString( 'uuid', '' );
$fid 	=	$app->input->getInt( 'fid', 0 );
$pk 	=	$app->input->getInt( 'pk', 0 );

try {
	$upload = 	new upload_ajax( $uuid, $fid, $pk );
} catch ( Exception $e ) {
    return	false;
}

switch ( $mode ) {
	case 'merge':
		$count 	=	$app->input->getInt( 'chunks', 0 );
		$size 	=	$app->input->getInt( 'size', 0 );
		$name 	=	$app->input->getString( 'name', '' );
		$result = 	$upload->mergeChunks( $name, $count, $size );
		break;

	case 'delete':
		$result = 	$upload->deleteFile( $app->input->getString( 'name', '' ) );
		break;

	default:
		$file	=	$app->input->files->get( 'file', null );

		if ( isset( $file['error'] ) && $file['error'] ) {
			throw new Exception( 'Upload File error: ' . $file['error'] );
		}

		$result = 	$upload->uploadFile( $file, $app->input->getInt( 'dzchunkindex', 0 ) );
		break;
}

echo $result;
?>