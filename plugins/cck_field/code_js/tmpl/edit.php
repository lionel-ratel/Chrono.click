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

JCckDev::forceStorage();

// Set
$displayData    =   array(
                        'config'=>$config,
                        'form'=>array(
                            array(
                                'fields'=>array(
                                    JCckDev::renderForm( 'core_dev_select', $this->item->bool, $config, array( 'label'=>'Behavior', 'selectlabel'=>'', 'defaultvalue'=>'0', 'options'=>'Document Ready=0||Standard=1||Raw=-1', 'storage_field'=>'bool' ) ),
                                    JCckDev::renderLayoutFile(
                                        'cck'.JCck::v().'.form.field', array(
                                            'label'=>Text::_( 'COM_CCK_JS' ),
                                            'html'=>JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.grid', array(
                                                'grid'=>'',
                                                'html'=>array(
                                                    JCckDev::getForm( 'core_defaultvalue_textarea', $this->item->defaultvalue, $config, array( 'cols'=>92, 'rows'=>8, 'maxlength'=>0 ) )
                                                )
                                            ) )
                                        )
                                    ),
                                )
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