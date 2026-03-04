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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;

// Plugin
class plgCCK_Field_LinkUpload_Image2_Cropping extends JCckPluginLink
{
	protected static $type	=	'upload_image2_cropping';
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
		
	// onCCK_Field_LinkPrepareContent
	public static function onCCK_Field_LinkPrepareContent( &$field, &$config = array() )
	{		
		if ( self::$type != $field->link ) {
			return;
		}
		
		// Prepare
		$link	=	parent::g_getLink( $field->link_options );
		
		// Set
		$field->link	=	'';
		self::_link( $link, $field, $config );
	}
	
	// _link
	protected static function _link( $link, &$field, &$config )
	{
		if ( !is_file( JPATH_SITE.'/plugins/cck_field/upload_image2/assets/js/upload_image2.js' ) ) {
			return false;
		}

		if ( $link->get( 'owner', 0 ) ) {
			$fieldname 	= 	$link->get( 'fieldname', '' );
			$owner		=	JCckDatabase::loadObject( 'SELECT id, storage_field, options2 FROM #__cck_core_fields WHERE name="'.$fieldname.'"' );
			$item 		=	JCckContent::getInstance( $config['id'] );
			$fid 		=	$owner->id;
			$value 		=	$item->getProperty( $owner->storage_field );
			$options2 	=	$owner->options2;
		} else {
			$fid 		=	$field->id;
			$value 		=	$field->value;
			$options2 	=	$field->options2;
		}

		list( $value, $version )	=	self::_getNameVersion( $value );

		$thumbs 					=	$link->get( 'thumbs', '' );
		$field->link_title 			=	Text::_( 'COM_CCK_CROP' );
		$field->link_attributes		=	' data-thumb="0" data-pk="'.$config['pk'].'" data-fid="'.$fid.'" data-value="'.$value.'" data-thumbs="'.$thumbs.'" data-force="true"';
		$field->link_onclick 		=	'JCck.More.CropX.getArea(this);return false;';
		$field->link_class 			=	'getcrop hasTooltip';
		$field->link 				=	'javascript:void(0);';

		self::_addScripts();

		if ( $link->get( 'force_to_crop', 0 ) ) {
			$options2 			=	json_decode( $options2, true );
			parent::g_addProcess( 'beforeRenderContent', self::$type, $config, array( 'name'=>$field->name, 'fid'=>$fid, 'thumbs'=>trim( $thumbs ) ) );
		}
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Special Events
	
	// onCCK_Field_LinkBeforeRenderContent
	public static function onCCK_Field_LinkBeforeRenderContent( $process, &$fields, &$storages, &$config = array() )
	{
		if ( $process['thumbs'] == '' ) {
			return;
		}

		$js 		=	'function checkCrop_'.$config['pk'].' (field, rules, i, options) {						
							var response =  jQuery.ajax({
								type: "GET",
								async: false,
								url: "'.JCckDevHelper::getAbsoluteUrl( 'auto', 'task=ajax&format=raw&'.Session::getFormToken().'=1&referrer=plugin.cck_field_link.upload_image2_cropping&file=plugins/cck_field_link/upload_image2_cropping/assets/ajax/script.php' ).'",
								data: ({cid:'.$config['id'].', fid:'.$process['fid'].', crops:"'.$process['thumbs'].'"})
							}).responseText;
							if (response != false) {return response;}						
						};';

		$validate_class 	=	' class="crop-validation validate[required,funcCall[checkCrop_'.$config['pk'].']]"';
		$attr 				=	' style="width:0;height:0;"';
		$validation 		=	'<input type="text" id="checkCrop_'.$config['pk'].'" name="checkCrop_'.$config['pk'].'" value="'.$config['pk'].'"'.$validate_class.$attr.'>'
							.	'<script type="text/javascript">'.$js.'</script>';

		$fields[$process['name']]->html 	.=	$validation;
	}

	// _addScripts
	protected static function _addScripts()
	{
		JCck::loadjQuery();

		$app 	= 	Factory::getApplication();

		if ( isset( $app->cck_crop ) ) {
			return;
		}

		$app->cck_crop 	= 	1;
		$cdn			=	'';

		if ( method_exists( 'JCck', 'getCdn' ) ) {
			$cdn			=	JCck::getCdn();	
		}

		$url_root	=	substr( ( $cdn ? $cdn.'/' : Uri::root() ), 0, -1 );
		$crop_link 	=	'format=raw&task=ajax&'.Session::getFormToken().'=1&mode=crop'
					. 	'&referrer=plugin.cck_field.upload_image2'
					.	'&file=plugins/cck_field/upload_image2/assets/ajax/script.php';
		$crop_link 	=	JCckDevHelper::getAbsoluteUrl( 'auto', $crop_link );	

		$doc	=	Factory::getDocument();
		$doc->addStyleSheet( $url_root.'/plugins/cck_field/upload_image2/assets/css/upload_image2.css' );
		$doc->addScript( $url_root.'/plugins/cck_field/upload_image2/assets/js/upload_image2.js' );
		$doc->addScriptDeclaration( 'jQuery(document).ready(function($){JCck.More.CropX.link ="'.$crop_link.'";});' );
	}

	// _getNameVersion
	protected static function _getNameVersion( $value )
	{
		$version	=	'';

		if ( strpos( $value, '?v=' ) !== false ) {
			$tmp		=	explode( '?v=', $value );
			$value		=	$tmp[0];
			$version	=	$tmp[1];
		}

		return array( $value, $version );
	}	
}
?>