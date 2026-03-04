<?php
/**
* @version 			SEBLOD 3.x Core
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Init
JCckDev::forceStorage();

$options2   =   JCckDev::fromJSON( $this->item->options2 );

// Set
$displayData    =   array(
                        'config'=>$config,
                        'form'=>array(
                            array(
                                'fields'=>array(
                                    JCckDev::renderForm( 'uikit_icons', $this->item->location, $config, array( 'storage_field'=>'location' ) ),
                                    JCckDev::renderForm( 
                                        'core_dev_select', @$options2['position'], $config, 
                                        array( 
                                            'label'=>'Tooltip Position', 'defaultvalue'=>'top', 
                                            'options'=>'top=top||top_left=top-left||top_right=top-right||bottom=bottom||bottom_left=bottom-left||bottom_right=bottom-right||left=left||right=right', 
                                            'storage_field'=>'json[options2][position]' 
                                        ) 
                                    )
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
                        'script'=>''
                    );

echo JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.cck_field.edit', $displayData );
?>