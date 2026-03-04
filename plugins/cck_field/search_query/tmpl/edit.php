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

$options2       =   JCckDev::fromJSON( $this->item->options2 );

// JS
$js =	'jQuery(document).ready(function($) {
			$("#json_options2_query").isVisibleWhen("bool2","1");
			$("#json_options2_query_select,#json_options2_query_group,#json_options2_query_where,#json_options2_query_having,#json_options2_query_order_by").isVisibleWhen("bool2","0");
		});';

// Set
$displayData    =   array(
                        'config'=>$config,
                        'form'=>array(
                            array(
                                'fields'=>array(
									JCckDev::renderForm( 'core_dev_select', $this->item->bool2, $config, array( 'label'=>'Query', 'selectlabel'=>'', 'options'=>'Append=0||Standalone=1', 'storage_field'=>'bool2' ) ),
									JCckDev::renderBlank(),
									JCckDev::renderForm( 'core_options_query', @$options2['query'], $config, array( 'rows'=>8 ), array(), 'w100' ),
									JCckDev::renderForm( 'core_options_query', @$options2['query_select'], $config, array( 'rows'=>5,'label'=>'Query Part Select', 'storage_field'=>'json[options2][query_select]' ), array(), 'w100' ),
									JCckDev::renderForm( 'core_options_query', @$options2['query_group'], $config, array( 'rows'=>1,'label'=>'Query Part Group', 'storage_field'=>'json[options2][query_group]' ), array(), 'w100' ),
									JCckDev::renderForm( 'core_options_query', @$options2['query_where'], $config, array( 'rows'=>5,'label'=>'Query Part Where', 'storage_field'=>'json[options2][query_where]' ), array(), 'w100' ),
									JCckDev::renderForm( 'core_options_query', @$options2['query_having'], $config, array( 'rows'=>5,'label'=>'Query Part Having', 'storage_field'=>'json[options2][query_having]' ), array(), 'w100' ),
									JCckDev::renderForm( 'core_options_query', @$options2['query_order_by'], $config, array( 'rows'=>1,'label'=>'Query Part Order By', 'storage_field'=>'json[options2][query_order_by]' ), array(), 'w100' ),
									JCckDev::renderForm( 'core_options_query', @$options2['query_variables'], $config, array( 'rows'=>1,'label'=>'Query Variables', 'storage_field'=>'json[options2][query_variables]' ), array(), 'w100' )
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