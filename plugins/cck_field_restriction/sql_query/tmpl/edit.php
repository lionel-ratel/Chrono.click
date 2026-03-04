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
JCckDev::initScript( 'restriction', $this->item );

// Set
echo JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.admin.edit', array(
    'config'=>$config,
    'form'=>array(
        array(
            'fields'=>array(
                JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Group By', 'storage_field'=>'group_by' ) ),
                JCckDev::renderForm( 'core_bool', '', $config, array( 'label'=>'Invert', 'defaultvalue'=>'0', 'options'=>'Yes=1||No=0', 'storage_field'=>'do' ) ),
                JCckDev::renderForm( 'core_options_query', '', $config, array( 'rows'=>8, 'required'=>'required', 'storage_field'=>'query' ), array(), 'w100' )
            )
        )
    ),
    'html'=>'',
    'item'=>$this->item,
    'script'=>'',
    'type'=>'restriction'
) );
?>