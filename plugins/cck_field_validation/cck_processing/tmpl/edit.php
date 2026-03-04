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

echo JCckDev::renderForm( 'core_task_processing', '', $config, array( 'storage_field'=>'processing' ) );
echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Callback', 'storage_field'=>'callback_function' ) );
echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Fields', 'storage_field'=>'fieldnames' ) );

?>