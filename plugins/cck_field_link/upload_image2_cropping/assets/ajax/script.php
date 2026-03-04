<?php
/**
* @version 			SEBLOD 3.x More
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\Filesystem\File;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

$app 			=	Factory::getApplication();
$error 			=	true;
$not_cropped 	=	array();
$lang   		=	Factory::getLanguage();

$lang->load( 'plg_cck_field_link_upload_image2_cropping', JPATH_ADMINISTRATOR, null, false, true );

$prefix			=	JCck::getConfig_Param( 'validation_prefix', '* ' );		
$content 		=	JCckContent::getInstance( $app->input->get( 'cid', 0, 'INT' ) );
$field 			=	JCckDatabase::loadObject( 'SELECT options2, storage_field FROM #__cck_core_fields WHERE id='.$app->input->get( 'fid', 0, 'INT' ) );
$options2 		=	json_decode( $field->options2, true );
$image 			=	JPATH_SITE.'/'.$options2['path'].$content->getPk().'/'.$content->getProperty( $field->storage_field );
$image_json		=	str_replace( File::getExt( $image ), 'json', $image );

if ( is_file( $image_json ) ) {
	$json 		=	json_decode( file_get_contents( $image_json ), true );
	$crops 		=	$app->input->get( 'crops', '', 'STRING' );
	$thumbs 	=	( strpos( $crops, ',' ) !== false ) ? explode( ',', $crops ) : (array)$crops; 

	foreach ( $thumbs as $thumb ) {
		if ( !isset( $json[$thumb] ) ) {
			$label 			=	Text::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $options2['thumb'.$thumb.'_label'] ) ) );
			$not_cropped[] 	=	$prefix.Text::sprintf( 'PLG_CCK_FIELD_LINK_UPLOAD_IMAGE2_CROPPING_MUST_BE_CROPPED', $label );
		}
	}
	$error 	=	( !empty( $not_cropped ) ) ? true : false;
}

if ( $error ) {
	if ( empty( $not_cropped ) ) {
		echo $prefix.Text::_( 'PLG_CCK_FIELD_LINK_UPLOAD_IMAGE2_CROPPING_MUST_BE_CROPPED_2' );
	} else {
		echo implode( "<br />", $not_cropped );
	}
} else {
	echo false;
}
?>