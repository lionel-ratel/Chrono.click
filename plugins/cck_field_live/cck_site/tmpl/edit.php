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
JCckDev::initScript( 'live', $this->item );

// Set
echo JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.admin.edit', array(
    'config'=>$config,
    'form'=>array(
        array(
            'fields'=>array(
                JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Target', 'selectlabel'=>'', 'options'=>'Property=property||Options=optgroup||Basic=configuration||Custom=', 'storage_field'=>'target' ) ),
                JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Property', 'required'=>'required', 'storage_field'=>'property' ) )
            )
        )
    ),
    'html'=>'',
    'item'=>$this->item,
    'script'=>'',
    'type'=>'live'
) );
?>