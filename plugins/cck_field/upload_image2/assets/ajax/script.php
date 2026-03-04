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

$app 	=	Factory::getApplication();
$mode 	=	$app->input->getString( 'mode', 'upload' );
$uuid 	=	$app->input->getString( 'uuid', '' );
$fid 	=	$app->input->getInt( 'fid', 0 );
$pk 	=	$app->input->getInt( 'pk', 0 );
$count 	=	$app->input->getInt( 'chunks', 1 );

if ( $mode != 'crop' ) {
	include_once JPATH_SITE.'/plugins/cck_field/upload_image2/classes/upload_image2.php';

	try {
		$upload 	= 	new upload_image2( $uuid,	 $fid, $pk );
	} catch ( Exception $e ) {
	    $result		=	'error';
	}

	switch ( $mode ) {
		case 'merge':
			$size 	=	$app->input->getInt( 'size', 0 );
			$name 	=	$app->input->getString( 'name', '' );
			$result = 	$upload->mergeChunks( $name, $count, $size );
			break;

		case 'delete':
			$result = 	$upload->deleteFile();
			break;

		default:
			$file 	= 	$app->input->files->get( 'file', array() );
			
			if ( isset( $file['error'] ) && $file['error'] ) {		//	TODO Better
				throw new Exception( 'Upload File error: ' . $file['error'] );
			}

			$result = 	$upload->uploadFile( $file, $app->input->getInt( 'dzchunkindex', 0 ), $count );
			break;
	}

} else {

	include_once ( JPATH_SITE.'/plugins/cck_field/upload_image2/classes/cropping.php' );
	$data 			=	Factory::getApplication()->input->get( 'data', array(), 'ARRAY' );
	$action 		= 	Factory::getApplication()->input->get( 't', '', 'string' );
	$data['action']	=	$action;
	$image 			= 	new cropping( $data );
	$result 		=	$image->{$action}();
}

echo $result;
?>