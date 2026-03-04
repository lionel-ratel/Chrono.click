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

// Set
$displayData    =   array(
                        'config'=>$config,
                        'form'=>array(
                            array(
                                'fields'=>array(
									JCckDev::renderForm( 'core_search_operators', $this->item->defaultvalue, $config )
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

/*
Get some dev. fields packaged as well..
JCckDev::renderForm( 'search_operator_open_toggle'
JCckDev::renderForm( 'search_operator_open'
JCckDev::renderForm( 'search_operator_and'
JCckDev::renderForm( 'search_operator_or'
JCckDev::renderForm( 'search_operator_close'
JCckDev::renderForm( 'search_operator_close_toggle'
*/
?>