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

use Joomla\CMS\Language\Text;

// Init
JCckDev::initScript( 'field', $this->item, array( 'hasOptions'=>true ) );
JCckDev::forceStorage();

$options    =   JCckDev::fromSTRING( $this->item->options );
$options2   =   JCckDev::fromJSON( $this->item->options2 );

// JS
$js =   'jQuery(document).ready(function($) {
            $("#json_options2_action").isVisibleWhen("bool","1");
            $("#json_options2_anchor").isVisibleWhen("bool2","1",false);
            $("#json_options2_enctype").isVisibleWhen("json_options2_method","post");
            $("#bool,#bool2,#json_options2_method,#json_options2_autocomplete,#json_options2_itemid,#json_options2_target,#bool3").isVisibleWhen("bool4","0");
        });';

// Set
$displayData    =   array(
                        'config'=>$config,
                        'form'=>array(
                            array(
                                'fields'=>array(
                                    JCckDev::renderForm( 'core_dev_bool', $this->item->bool4, $config, array( 'label'=>'Behavior', 'defaultvalue'=>'0', 'options'=>'Start=0||End=1', 'storage_field'=>'bool4' ) ),
                                    JCckDev::renderForm( 'core_bool', $this->item->bool, $config, array( 'defaultvalue'=>0, 'label'=>'Action', 'options'=>'Auto=0||Custom=1' ) ),
                                    JCckDev::renderLayoutFile(
                                        'cck'.JCck::v().'.form.field', array(
                                            'label'=>Text::_( 'COM_CCK_ACTION_ANCHOR' ),
                                            'html'=>JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.grid', array(
                                                'grid'=>'|50%',
                                                'html'=>array(
                                                    JCckDev::getForm( 'core_bool2', $this->item->bool2, $config, array( 'defaultvalue'=>0, 'options'=>'None=0||Custom=1||Form ID=2' ) ),
                                                    JCckDev::getForm( 'core_dev_text', @$options2['anchor'], $config, array( 'defaultvalue'=>'', 'size'=>8, 'storage_field'=>'json[options2][anchor]' ) )
                                                )
                                            ) )
                                        )
                                    ),
                                    JCckDev::renderForm( 'core_dev_textarea', @$options2['action'], $config, array( 'defaultvalue'=>'', 'label'=>'Action Custom', 'cols'=>92, 'rows'=>1, 'required'=>'required', 'storage_field'=>'json[options2][action]' ), array(), 'w100' ),

                                    JCckDev::renderForm( 'core_method', @$options2['method'], $config ),
                                    JCckDev::renderForm( 'core_bool', @$options2['autocomplete'], $config, array( 'label'=>'Autocomplete', 'defaultvalue'=>'0', 'storage_field'=>'json[options2][autocomplete]' ) ),
                                    JCckDev::renderForm( 'core_menuitem', @$options2['itemid'], $config, array( 'selectlabel'=>'Inherited', 'options'=>'Parent=-1', 'storage_field'=>'json[options2][itemid]' ) ),

                                    JCckDev::renderForm( 'core_bool', @$options2['canonical'], $config, array( 'label'=>'Canonical URL', 'defaultvalue'=>'0', 'options'=>'No=0||Yes=1||Yes on View Form=2', 'storage_field'=>'json[options2][canonical]' ) ),
                                    JCckDev::renderForm( 'core_options_target', @$options2['target'], $config, array( 'defaultvalue'=>'_self', 'options'=>'Target Blank=_blank||Target IFrame=iframe||Target Parent=_parent||Target Self=_self||Target Top=_top' ) ),
                                    JCckDev::renderForm( 'core_options_enctype', @$options2['enctype'], $config )
                                )
                            ),
                            array(
                                'fields'=>array(
                                    JCckDev::renderForm( 'core_options', $options, $config, array( 'label'=>'Fields', 'rows'=>1 ) ),
                                    JCckDev::renderForm( 'core_bool3', $this->item->bool3, $config, array( 'defaultvalue'=>0, 'label'=>'Exclude System Fields', 'options'=>'No=0||Yes=1||Custom=optgroup||Session Token=2' ) )
                                ),
                                'legend'=>Text::_( 'COM_CCK_CONSTRUCTION' )
                            ),
                            array(
                                'fields'=>array(
                                    JCckDev::getForm( 'core_storage', $this->item->storage, $config )
                                ),
                                'mode'=>'storage'
                            )
                        ),
                        'help'=>array(),
                        'html'=>'',
                        'item'=>$this->item,
                        'script'=>$js
                    );

echo JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.cck_field.edit', $displayData );
?>