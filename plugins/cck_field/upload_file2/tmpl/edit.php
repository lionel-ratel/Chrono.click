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
$options2   =   JCckDev::fromJSON( $this->item->options2 );
$media_ext  =   ( $this->isNew ) ? '' : ( ( isset( $options2['media_extensions'] ) ) ? $options2['media_extensions'] : 'custom' );

if ( !JCck::on( '4.0' ) ) {
    include __DIR__.'/edit_3x.php';
    return;
}

// JS
$js =   'jQuery(document).ready(function($) {
            $("#json_options2_legal_extensions").isVisibleWhen("json_options2_media_extensions","custom",false);
        });';

// Set
$displayData    =   array(
                        'config'=>$config,
                        'form'=>array(
                            array(
                                'fields'=>array(
                                            JCckDev::renderForm( 'core_label', $this->item->label, $config ),
                                            JCckDev::renderForm( 'core_defaultvalue', $this->item->defaultvalue, $config ),
                                            JCckDev::renderForm( 'core_bool', $this->item->bool, $config, array( 'label'=>'Behavior', 'defaultvalue'=>'0', 'options'=>'Standard=0||Multiple=1' ) ),

                                            JCckDev::renderLayoutFile(
                                                'cck'.JCck::v().'.form.field', array(
                                                    'label'=>Text::_( 'COM_CCK_FOLDER' ),
                                                    'html'=>JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.grid', array(
                                                        'grid'=>'|25%|25%',
                                                        'html'=>array(
                                                            JCckDev::getForm( 'core_options_path', @$options2['path'], $config, array( 'required'=> 'required', 'size'=>22 ) ),
                                                            JCckDev::getForm( 'core_options_path_content', @$options2['path_content'], $config ),
                                                            JCckDev::getForm( 'core_dev_bool', @$options2['path_type'], $config, array( 'defaultvalue'=>'0', 'options'=>'Root=0||Target=optgroup||Resources=1', 'storage_field'=>'json[options2][path_type]' ) )
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
                                            JCckDev::renderForm( 'core_dev_select', @$options2['forbidden_extensions'], $config, array( 'label'=>'Forbidden Extensions', 'selectlabel'=>'Inherited', 'options'=>'None=0||Whitelist=1', 'storage_field'=>'json[options2][forbidden_extensions]' ) ),
                                ),
                            ),
                            array(
                                'fields'=>array(
                                    JCckDev::getForm( 'core_storage', $this->item->storage, $config )
                                ),
                                'mode'=>'storage'
                            )
                        ),
                        'help'=>array( 'field', 'seblod-2-x-upload-file-field' ),
                        'html'=>'',
                        'item'=>$this->item,
                        'script'=>$js
                    );

echo JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.cck_field.edit', $displayData );
?>