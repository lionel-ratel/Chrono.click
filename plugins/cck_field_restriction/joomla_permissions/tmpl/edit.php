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

JCckDev::initScript( 'restriction', $this->item );

// Set
echo JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.admin.edit', array(
    'config'=>$config,
    'form'=>array(
        array(
            'fields'=>array(
				JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Permissions', 'type'=>'checkbox', 'selectlabel'=>'', 'options'=>'Edit=edit||Edit Own=edit.own||Edit Own (Related Content)=edit.own.content||Export=export||Process=process', 'bool'=>1, 'bool8'=>0, 'storage_field'=>'permissions' ) ),
				JCckDev::renderForm( 'core_form', '', $config, array( 'selectlabel'=>'Inherited', 'required'=>'' ) ),
				JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Permissions Content', 'type'=>'checkbox', 'selectlabel'=>'', 'options'=>'Edit=edit||Edit Own=edit.own', 'bool'=>1, 'bool8'=>0, 'storage_field'=>'permissions_content' ), array(), 'w100' ),
                JCckDev::renderForm( 'core_bool', '', $config, array( 'label'=>'Invert', 'type'=>'radio', 'defaultvalue'=>'0', 'options'=>'No=0||Yes=1', 'css'=>'btn-group', 'storage_field'=>'do' ) )
            )
        )
    ),
    'html'=>'',
    'item'=>$this->item,
    'script'=>'',
    'type'=>'restriction'
) );
?>