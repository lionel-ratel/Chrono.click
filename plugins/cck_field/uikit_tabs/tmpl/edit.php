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

// Init
JCckDev::forceStorage();

// JS
$js =   'jQuery(document).ready(function($) {
            $("#defaultvalue").isVisibleWhen("bool","0",false);
            $("#bool2,#bool3,#bool4").isVisibleWhen("bool","0");
        });';

// Set
$displayData    =   array(
                        'config'=>$config,
                        'form'=>array(
                            array(
                                'fields'=>array(
                                    JCckDev::renderForm( 'core_label', $this->item->label, $config ),
                                    JCckDev::renderForm( 'core_extended', $this->item->extended, $config, array( 'required'=>'' ) ),
                                    JCckDev::renderLayoutFile(
                                        'cck'.JCck::v().'.form.field', array(
                                            'label'=>JCckDev::getLabel( 'core_pane_behavior', $config ),
                                            'html'=>JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.grid', array(
                                                'grid'=>'|25%',
                                                'html'=>array(
                                                    JCckDev::getForm( 'core_pane_behavior', $this->item->bool, $config, array( 'required'=>'required' ) ),
                                                    JCckDev::getForm( 'core_defaultvalue', $this->item->defaultvalue, $config, array( 'size'=>3, 'attributes'=>'style="text-align:center;"' ) )
                                                )
                                            ) )
                                        )
                                    ),
                                    JCckDev::renderForm( 'core_dev_text', $this->item->location, $config, array( 'label'=>'GROUP_IDENTIFIER', 'storage_field'=>'location' ) ),
                                    JCckDev::renderForm( 'core_dev_bool', $this->item->bool2, $config, array( 'label'=>'URL', 'defaultvalue'=>'0', 'options'=>'None=0||Set Active Pane=1||Set Active Pane and URL Hash=2', 'storage_field'=>'bool2' ) ),
                                    JCckDev::renderForm( 'core_dev_bool', $this->item->bool3, $config, array( 'label'=>'Wrapper', 'defaultvalue'=>'0', 'options'=>'Default=0||Yes=1', 'storage_field'=>'bool3' ) ),
                                    JCckDev::renderForm( 'core_dev_bool', $this->item->bool4, $config, array( 'label'=>'Position', 'defaultvalue'=>'0', 'options'=>'Top=0||Left=1||Bottom=2||Right=3', 'storage_field'=>'bool4' ) )
                                )
                            ),
                            array(
                                'fields'=>array(
                                    JCckDev::getForm( 'core_storage', $this->item->storage, $config )
                                ),
                                'mode'=>'storage'
                            )
                        ),
                        'html'=>'',
                        'item'=>$this->item,
                        'script'=>$js
                    );

echo JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.cck_field.edit', $displayData );
?>