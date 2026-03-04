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

$options	=	JCckDev::fromSTRING( $this->item->options );
$options2	=	JCckDev::fromJSON( $this->item->options2 );

// JS
$js =	'jQuery(document).ready(function($) {
			$("#json_options2_code").isVisibleWhen("bool","0");
			$("#sortable_core_options").isVisibleWhen("bool","1");
		});';

// Set
$displayData	=	array(
						'config'=>$config,
						'form'=>array(
							array(
								'fields'=>array(
									JCckDev::renderForm( 'core_bool', $this->item->bool, $config, array( 'label'=>'Mode', 'defaultvalue'=>'1', 'options'=>'Files=1||Free=0' ) ),
									JCckDev::renderForm( 'core_bool2', $this->item->bool5, $config, array( 'label'=>'Priority', 'defaultvalue'=>'5', 'options'=>'1||2||3||4||5', 'storage_field'=>'bool5' ) ),
									JCckDev::renderForm( 'core_defaultvalue_textarea', @$options2['code'], $config, array( 'label'=>'BeforeRender', 'cols'=>92, 'rows'=>8, 'maxlength'=>0, 'storage_field'=>'json[options2][code]' ), array(), 'w100' ),
									JCckDev::renderForm( 'core_options', $options, $config, array( 'label'=>'Files' ) ),
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