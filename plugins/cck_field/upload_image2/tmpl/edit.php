<?php
/**
* @version 			SEBLOD 3.x More
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\CMS\Language\Text;

// Init
$options2   	=   JCckDev::fromJSON( $this->item->options2 );
$media_ext  	=   ( $this->isNew ) ? '' : ( ( isset( $options2['media_extensions'] ) ) ? $options2['media_extensions'] : 'custom' );
$thumbs_number	=	10;

if ( !JCck::on( '4.0' ) ) {
	include __DIR__.'/edit_3x.php';
	return;
}

// Set Thumbs number
$select_thumbs	=	array( 'Image=0' );

for ( $i = 1; $i <= $thumbs_number; $i++ ) {
	$select_thumbs[]	=	Text::_( 'COM_CCK_THUMB_' ).$i.'='.$i;
}

// Set Form preview
$form_preview	=	array( 'Hide=-1','Show=optgroup','Filename Title=0', 'Icon=1','Image=2' );
$nb				=	3;

for ( $i = 1; $i <= $thumbs_number; $i++ ) {
	$form_preview[]		=	Text::_( 'COM_CCK_THUMB_' ).$i.'='.$nb++;
}

// Set Crop Reference
$select_ref					=	array( 'No=-1', 'Yes=0', 'Optional=-2', Text::_( 'COM_CCK_REFERENCE' ).'=optgroup' );

for ( $i = 1; $i <= $thumbs_number; $i++ ) {
	$select_ref[]	=	Text::_( 'COM_CCK_REF_THUMB_' ).$i.'='.$i;
}

// Set Thumbs Label
$label_thumbs	=	array();

for ( $i = 1; $i <= $thumbs_number; $i++ ) {
	$label_thumbs[$i]	=	( $this->isNew ) ? Text::_( 'COM_CCK_THUMB_' ).$i : @$options2['thumb'.$i.'_label'];
}

if ( JCck::on( '4.0' ) ) {
	$parent		=	'.parent().parent()';
	$visibility	=	'';
} else {
	$parent		=	'';
	$visibility	=	',"visibility"';
}

// Thumbs
$cropping	=	'';
$js			=   '';

for ( $i = 1; $i <= $thumbs_number; $i++ ) {
	$ref	=	$select_ref;

	unset( $ref[3+$i] );

	$cropping	.=	JCckDev::renderLayoutFile(
					'cck'.JCck::v().'.form.field', array(
						'label'=>Text::_( 'COM_CCK_THUMB_' ).$i,
						'html'=>JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.grid', array(
							'grid'=>'|10%|40%',
							'html'=>array(
								JCckDev::getForm( 'core_options_thumb_process', @$options2['thumb'.$i.'_process'], $config, array( 'css'=>'process', 'defaultvalue'=>'', 'options'=>'No Process=0||Resized=optgroup||Crop Center=crop||Quotient=quotient||Shrink=shrink||Stretch=stretch||Resized Dynamic=optgroup||Crop Dynamic=crop_dynamic||Max Fit=maxfit||Shrink=shrink_dynamic||Stretch=stretch_dynamic', 'storage_field'=>'json[options2][thumb'.$i.'_process]' ) ),
								'<span class="variation_value lbl-wmk" style="margin-right: 5px;">'.Text::_( 'COM_CCK_WMK' ).'</span>',
								JCckDev::getForm( 'more_image2_watermark', @$options2['thumb'.$i.'_wmk'], $config, array( 'storage_field'=>'json[options2][thumb'.$i.'_wmk]' ) )
							)
						) )
					)
				)
			.	JCckDev::renderLayoutFile(
					'cck'.JCck::v().'.form.field', array(
						'label'=>Text::_( 'COM_CCK_WIDTH_HEIGHT' ),
						'html'=>JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.grid', array(
							'grid'=>'45%|5%|45%|5%',
							'html'=>array(
								JCckDev::getForm( 'core_options_thumb_width', @$options2['thumb'.$i.'_width'], $config, array( 'css'=>'thumb-size thumb-width', 'size'=>10, 'defaultvalue'=>'', 'storage_field'=>'json[options2][thumb'.$i.'_width]' ) ),
								'<span class="variation_value" style="margin-right: 5px;">x</span>',
								JCckDev::getForm( 'core_options_thumb_height', @$options2['thumb'.$i.'_height'], $config, array( 'css'=>'thumb-size thumb-height', 'size'=>10, 'defaultvalue'=>'', 'storage_field'=>'json[options2][thumb'.$i.'_height]' ) ),
								'<span class="variation_value">px</span>',
							)
						) )
					)
				)
			.	JCckDev::renderLayoutFile(
					'cck'.JCck::v().'.form.field', array(
						'label'=>Text::_( 'COM_CCK_CROPPING' ),
						'html'=>JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.grid', array(
							'grid'=>'|50%',
							'html'=>array(
								JCckDev::getForm( 'core_dev_select', @$options2['thumb'.$i.'_cropping'], $config, array( 'defaultvalue'=>-1, 'bool8'=>0, 'options'=> implode( '||', $ref ), 'selectlabel'=>'', 'storage_field'=>'json[options2][thumb'.$i.'_cropping]' ) ),
								JCckDev::getForm( 'core_dev_text', $label_thumbs[$i], $config, array( 'size'=>12, 'storage_field'=>'json[options2][thumb'.$i.'_label]' ) ),
							)
						) )
					)
				);

	 $js    .=  '$("#json_options2_thumb'.$i.'_width")'.$parent.'.isVisibleWhen("json_options2_thumb'.$i.'_process","addcolor,crop,crop_dynamic,maxfit,shrink,stretch,stretch_dynamic,shrink_dynamic,quotient",true'.$visibility.');'."\n"
			.   '$("#json_options2_thumb'.$i.'_label").isVisibleWhen("json_options2_thumb'.$i.'_cropping","0,-2",false);'."\n";
}

// Watermark
$watermark	=	'';

if ( function_exists( 'imagettfbbox' ) && function_exists( 'imagettftext' ) ) {
	$watermark	=	array(
						'fields'=>array(
							JCckDev::renderForm( 'core_dev_select', @$options2['add_watermark'], $config, array( 'label'=>'ADD_WATERMARK', 'defaultvalue'=>'0', 'options'=>'No=0||watermark_Image=1||Watermark_Text=2', 'selectlabel'=>'', 'storage_field'=>'json[options2][add_watermark]' ) ),
							JCckDev::renderForm( 'more_image2_watermark_position', @$options2['watermark_position'], $config, array( 'storage_field'=>'json[options2][watermark_position]' ) ),
							JCckDev::renderLayoutFile(
								'cck'.JCck::v().'.form.field', array(
									'label'=>Text::_( 'COM_CCK_WATERMARK_MARGIN' ),
									'html'=>JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.grid', array(
										'grid'=>'|50%',
										'html'=>array(
											JCckDev::getForm( 'core_dev_text', @$options2['watermark_offsetX'], $config, array( 'size'=>8, 'defaultvalue'=>0, 'storage_field'=>'json[options2][watermark_offsetX]') ),
											'<span class="variation_value" style="margin-right: 5px;">px</span>'
										)
									) )
								)
							),
							JCckDev::renderLayoutFile(
								'cck'.JCck::v().'.form.field', array(
									'label'=>Text::_( 'COM_CCK_WATERMARK_OPACITY' ),
									'html'=>JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.grid', array(
										'grid'=>'|50%',
										'html'=>array(
											JCckDev::getForm( 'more_image2_opacity', @$options2['watermark_opacity'], $config, array( 'storage_field'=>'json[options2][watermark_opacity]' ) ),
											'<span class="variation_value" style="margin-right: 5px;">%</span>'
										)
									) )
								)
							),
							JCckDev::renderLayoutFile(
								'cck'.JCck::v().'.form.field', array(
									'label'=>Text::_( 'COM_CCK_WATERMARK_IMAGE_EXTEND' ),
									'html'=>JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.grid', array(
										'grid'=>'|50%',
										'html'=>array(
											JCckDev::getForm( 'core_dev_select', @$options2['watermark_image_extend'], $config, array( 'defaultvalue'=>'0', 'options'=>'Path=0||Field=1', 'selectlabel'=>'', 'storage_field'=>'json[options2][watermark_image_extend]' ) ),
											JCckDev::getForm( 'core_dev_text', @$options2['watermark_image_path'], $config, array( 'defaultvalue'=>'', 'size'=>18, 'required'=>'required', 'storage_field'=>'json[options2][watermark_image_path]' ) )
										)
									) )
								)
							),
							JCckDev::renderLayoutFile(
								'cck'.JCck::v().'.form.field', array(
									'label'=>Text::_( 'COM_CCK_WATERMARK_IMAGE_SCALE' ),
									'html'=>JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.grid', array(
										'grid'=>'|50%',
										'html'=>array(
											JCckDev::getForm( 'core_dev_text', @$options2['watermark_image_scale'], $config, array( 'size'=>8, 'defaultvalue'=>'100', 'storage_field'=>'json[options2][watermark_image_scale]' ) ),
											'<span class="variation_value" style="margin-right: 5px;">%</span>',
										)
									) )
								)
							),
							JCckDev::renderLayoutFile(
								'cck'.JCck::v().'.form.field', array(
									'label'=>Text::_( 'COM_CCK_WATERMARK_TEXT_EXTEND' ),
									'html'=>JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.grid', array(
										'grid'=>'|50%',
										'html'=>array(
											JCckDev::getForm( 'core_dev_select', @$options2['watermark_text_extend'], $config, array( 'defaultvalue'=>'0', 'options'=>'Text=0||Field=1', 'selectlabel'=>'', 'storage_field'=>'json[options2][watermark_text_extend]' ) ),
											JCckDev::getForm( 'core_dev_text', @$options2['watermark_text'], $config, array( 'defaultvalue'=>'', 'size'=>18, 'required'=>'required', 'storage_field'=>'json[options2][watermark_text]' ) )
										)
									) )
								)
							),
							JCckDev::renderLayoutFile(
								'cck'.JCck::v().'.form.field', array(
									'label'=>Text::_( 'COM_CCK_WATERMARK_TEXT_SIZE' ),
									'html'=>JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.grid', array(
										'grid'=>'|50%',
										'html'=>array(
											JCckDev::getForm( 'core_dev_text', @$options2['watermark_text_size'], $config, array( 'size'=>8, 'defaultvalue'=>32, 'storage_field'=>'json[options2][watermark_text_size]') ),
											'<span class="variation_value" style="margin-right: 5px;">px</span>'
										)
									) )
								)
							),
							JCckDev::renderForm( 'core_dev_text', @$options2['watermark_text_color'], $config, array( 'label'=>'WATERMARK_TEXT_COLOR', 'size'=>18, 'defaultvalue'=>'#000000', 'storage_field'=>'json[options2][watermark_text_color]' ) ),
							JCckDev::renderForm( 'core_dev_text', @$options2['watermark_text_font'], $config, array( 'label'=>'WATERMARK_TEXT_FONT', 'size'=>18, 'defaultvalue'=>'', 'storage_field'=>'json[options2][watermark_text_font]' ) )
						),
						'legend'=>Text::_( 'COM_CCK_WATERMARK' )
					);
} else {
	$watermark	=	array(
						'fields'=>array(
							JCckDev::renderForm( 'core_dev_select', @$options2['add_watermark'], $config, array( 'label'=>'ADD_WATERMARK', 'defaultvalue'=>'0', 'options'=>'No=0', 'selectlabel'=>'', 'storage_field'=>'json[options2][add_watermark]' ) ),
							'<li><span>'.Text::_( 'COM_CCK_WATERMARK_FUNCTIONS_UNAVAILABLE' ).'</span></li>'
						),
						'legend'=>Text::_( 'COM_CCK_WATERMARK' )
					);
}

// JS
$js =   'jQuery(document).ready(function($) {
			$(".alias-hide,#json_options2_path,#json_options2_media_extensions,#json_options2_max_size").isVisibleWhen("json_options2_behavior","standard",true);
			$("#json_options2_file_name").isVisibleWhen("json_options2_behavior","standard",true);
			$("#json_options2_field_alias").isVisibleWhen("json_options2_behavior","alias",true);
			$("#json_options2_legal_extensions").isVisibleWhen("json_options2_media_extensions","custom",false);
			$("#json_options2_image_width")'.$parent.'.isVisibleWhen("json_options2_image_process","addcolor,crop,crop_dynamic,maxfit,shrink,stretch,stretch_dynamic,shrink_dynamic,quotient",true'.$visibility.');
			$("#json_options2_thumb1_width")'.$parent.'.isVisibleWhen("json_options2_thumb1_process","addcolor,crop,crop_dynamic,maxfit,shrink,stretch,stretch_dynamic,shrink_dynamic,quotient",true'.$visibility.');
			'.$js.'
			$("#json_options2_title_image").isVisibleWhen("json_options2_multivalue_mode","1");
			$("#json_options2_desc_image").isVisibleWhen("json_options2_multivalue_mode","1");
			$("#json_options2_path_label").isVisibleWhen("json_options2_custom_path","1",false);
			$("#json_options2_title_label").isVisibleWhen("json_options2_title_image","1",false);
			$("#json_options2_desc_label").isVisibleWhen("json_options2_desc_image","1",false);
			$("#json_options2_path_user,#json_options2_custom_path").isDisabledWhen("json_options2_storage_format","1" );
			$("#json_options2_crop_mode").isVisibleWhen("json_options2_images_cropping","1",false);
			$(".adminformlist li fieldset label").hide();
			$("#json_options2_dropdown_display,#json_options2_default_method").isVisibleWhen("json_options2_images_cropping","1",true);
			$("#json_options2_picker,#json_options2_default_color,#json_options2_palette").isVisibleWhen("json_options2_images_cropping","1",true);

			$("#json_options2_watermark_position").isVisibleWhen("json_options2_add_watermark","1,2",true);
			$("#json_options2_watermark_opacity")'.$parent.'.isVisibleWhen("json_options2_add_watermark","1,2",true);
			$("#json_options2_watermark_offsetX")'.$parent.'.isVisibleWhen("json_options2_add_watermark","1,2",true);
			$("#json_options2_watermark_offsetY")'.$parent.'.isVisibleWhen("json_options2_add_watermark","1,2",true);
			$("#json_options2_watermark_image_extend,#json_options2_watermark_image_path")'.$parent.'.isVisibleWhen("json_options2_add_watermark","1",true);
			$("#json_options2_watermark_image_scale")'.$parent.'.isVisibleWhen("json_options2_add_watermark","1",true);
			$("#json_options2_watermark_text_extend,#json_options2_watermark_text")'.$parent.'.isVisibleWhen("json_options2_add_watermark","2",true );
			$("#json_options2_watermark_text_size")'.$parent.'.isVisibleWhen("json_options2_add_watermark","2",true);
			$("#json_options2_watermark_text_color").isVisibleWhen("json_options2_add_watermark","2",true);
			$("#json_options2_watermark_text_font").isVisibleWhen("json_options2_add_watermark","2",true);

			$(".lbl-wmk,.checkboxes.wmk").isVisibleWhen("json_options2_add_watermark","1,2",false);

			$("#json_options2_images_cropping,#json_options2_default_method").on("change", function(){
				var df = $("#json_options2_default_position")'.$parent.';
				df.hide();
				if ($("#json_options2_images_cropping").val() == 1 && $("#json_options2_default_method").val() == 1) {
					df.show();
				}
			});

			$("#json_options2_images_cropping,[id$=\'_process\'],.thumb-size").on("change", function() {
				var w, h, m, a = $("#json_options2_images_cropping").val();
				for( var i = 1; i <= '.$thumbs_number.'; i++ ) {
					w = $("#json_options2_thumb"+i+"_width").val();
					h = $("#json_options2_thumb"+i+"_height").val();
					m = $("#json_options2_thumb"+i+"_process").val();

					var crop = (m != 0) && ((w != "" || h != "") && m == "maxfit") || (w != "" && h != "");

					if(  a == 1 && crop && m != 0 ) {
						$("#json_options2_thumb"+i+"_cropping").parent("li").show();
						$("#json_options2_thumb"+i+"_cropping").parent("li").prev("li").show();
					} else {
						$("#json_options2_thumb"+i+"_cropping").parent("li").hide();
						$("#json_options2_thumb"+i+"_cropping").parent("li").prev("li").hide();
					}
					if( $("#json_options2_thumb"+i+"_process").val() == 0 || $("#json_options2_add_watermark").val() == 0 ){
						$("#json_options2_thumb"+i+"_wmk").hide().prev("span.lbl-wmk").hide();
					} else {
						$("#json_options2_thumb"+i+"_wmk").show().prev("span.lbl-wmk").show();
					}
				}
			}).trigger("change");
		});';

$displayData    =   array(
						'config'=>$config,
						'form'=>array(
							array(
								'fields'=>array(
									JCckDev::renderForm( 'core_label', $this->item->label, $config ),
									JCckDev::renderForm( 'core_defaultvalue', $this->item->defaultvalue, $config ),
									JCckDev::renderForm( 'core_dev_select', @$options2['behavior'], $config, array( 'label'=>'Behavior', 'defaultvalue'=>'standard', 'options'=>'Standard=standard||Alias=alias', 'selectlabel'=>'', 'storage_field'=>'json[options2][behavior]' ) ),
									JCckDev::renderForm( 'core_dev_select', @$options2['file_name'], $config, array( 'label'=>'FileName', 'options'=>'Filename Keep Existing=existing||Filename Use Uploaded=uploaded', 'selectlabel'=>'Inherit', 'storage_field'=>'json[options2][file_name]' ) ),	// From Content=content||
									JCckDev::renderForm( 'core_dev_text', @$options2['field_alias'], $config, array( 'label'=>'Field', 'required'=>'required', 'storage_field'=>'json[options2][field_alias]' ) ),
									JCckDev::renderLayoutFile(
										'cck'.JCck::v().'.form.field', array(
											'label'=>Text::_( 'COM_CCK_MAXIMUM_SIZE' ),
											'html'=>JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.grid', array(
												'grid'=>'|33%',
												'html'=>array(
													JCckDev::getForm( 'core_options_path', @$options2['path'], $config, array( 'Label'=>'Folder', 'required'=> 'required' ) ),
													JCckDev::getForm( 'core_dev_bool', @$options2['path_type'], $config, array( 'defaultvalue'=>'0', 'options'=>'Root=0||Target=optgroup||Resources=1', 'storage_field'=>'json[options2][path_type]' ) )
												)
											) )
										)
									),
									JCckDev::renderLayoutFile(
										'cck'.JCck::v().'.form.field', array(
											'label'=>Text::_( 'COM_CCK_LEGAL_EXTENSIONS' ),
											'html'=>JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.grid', array(
												'grid'=>'|50%',
												'html'=>array(
													JCckDev::getFormFromHelper( array( 'component'=>'com_cck', 'function'=>'getMediaExtensions', 'name'=>'core_options_media_extensions' ), $media_ext, $config, array( 'storage_field'=>'json[options2][media_extensions]' ) ),
													JCckDev::getForm( 'core_options_legal_extensions', @$options2['legal_extensions'], $config, array( 'size'=>13, 'required'=>'required' ) )
												)
											) )
										)
									),
									JCckDev::renderLayoutFile(
										'cck'.JCck::v().'.form.field', array(
											'label'=>Text::_( 'COM_CCK_MAXIMUM_SIZE' ),
											'html'=>JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.grid', array(
												'grid'=>'|25%',
												'html'=>array(
													JCckDev::getForm( 'core_options_max_size', @$options2['max_size'], $config ),
													JCckDev::getForm( 'core_options_size_unit', @$options2['size_unit'], $config )
												)
											) )
										)
									),
									JCckDev::renderForm( 'core_options_force_thumb_creation', @$options2['force_thumb_creation'], $config, array( 'label'=>'FORCE_THUMB_CREATION' ) ),
									JCckDev::renderForm( 'core_options_preview_image', @$options2['content_preview'], $config, array( 'defaultvalue'=>'1', 'label'=>'DISPLAY_AS_DEFAULT', 'bool8'=>0, 'options'=> implode( '||', $select_thumbs ), 'storage_field'=>'json[options2][content_preview]' ) ),

									//
									JCckDev::renderForm( 'core_options_image_process', @$options2['image_process'], $config, array( 'label'=>'Image', 'defaultvalue'=>'', 'storage_field'=>'json[options2][image_process]' ) ),

									//
									JCckDev::renderLayoutFile(
										'cck'.JCck::v().'.form.field', array(
											'label'=>Text::_( 'COM_CCK_WIDTH_HEIGHT' ),
											'html'=>JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.grid', array(
												'grid'=>'45%|5%|45%|5%',
												'html'=>array(
													JCckDev::getForm( 'core_options_image_width', @$options2['image_width'], $config ),
													'<span class="variation_value" style="margin-right: 5px;">x</span>',
													JCckDev::getForm( 'core_options_image_height', @$options2['image_height'], $config ),
													'<span class="variation_value">px</span></li>'
												)
											) )
										)
									)
								)
							),
							array(
								'fields'=>array(
									$cropping
								),
								'legend'=>Text::_( 'COM_CCK_THUMBS' )
							),
							array(
								'fields'=>array(
									JCckDev::renderForm( 'core_dev_select', @$options2['images_cropping'], $config, array( 'label'=>'ALLOW_CROPPING', 'defaultvalue'=>'0', 'options'=>'No=0||Yes=1', 'selectlabel'=>'', 'storage_field'=>'json[options2][images_cropping]' ) ),
									JCckDev::renderForm( 'core_dev_select', @$options2['default_method'], $config, array( 'label'=>'default_method', 'defaultvalue'=>'0', 'options'=>'Expand=0||Contract=1', 'selectlabel'=>'', 'storage_field'=>'json[options2][default_method]' ) ),
									JCckDev::renderForm( 'core_dev_select', @$options2['default_position'], $config, array( 'label'=>'default_position', 'defaultvalue'=>'0', 'options'=>'Top=t||Middle=m||Bottom=b', 'selectlabel'=>'', 'storage_field'=>'json[options2][default_position]' ) ),
									JCckDev::renderForm( 'core_dev_select', @$options2['picker'], $config, array( 'label'=>'PICKER TYPE', 'defaultvalue'=>'1', 'options'=>'Colorpicker=0||Palette=1', 'selectlabel'=>'', 'storage_field'=>'json[options2][picker]' ) ),
									JCckDev::renderForm( 'core_dev_text', @$options2['default_color'], $config, array( 'label'=>'DEFAULT COLOR', 'storage_field'=>'json[options2][default_color]' ) ),
									JCckDev::renderForm( 'core_dev_textarea', @$options2['palette'], $config, array( 'label'=>'PALETTE', 'defaultvalue'=>"#FFFFFF,#000000", 'storage_field'=>'json[options2][palette]' ) )
								),
								'legend'=>Text::_( 'COM_CCK_CROPPING' )
							),
							$watermark,
							array(
								'fields'=>array(
									JCckDev::getForm( 'core_storage', $this->item->storage, $config )
								),
								'mode'=>'storage'
							)
						),
						'help'=>'',
						'html'=>'',
						'item'=>$this->item,
						'script'=>$js
					);

echo JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.cck_field.edit', $displayData );
?>