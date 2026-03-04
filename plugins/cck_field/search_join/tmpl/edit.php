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

// Init
JCckDev::forceStorage();

$html		=	'';
$options2	=	json_decode( $this->item->options2, true );

for ( $i = 0; $i < 10; $i++ ) {
	$html	.=	JCckDev::renderLayoutFile(
					'cck'.JCck::v().'.form.field', array(
						'label'=>Text::_( 'COM_CCK_JOIN' ).' ('.( $i + 1 ).')',
						'html'=>JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.grid', array(
									'grid'=>'|75%',
									'html'=>array(
										JCckDev::getForm( 'core_dev_select', @$options2['joins'][$i]['mode'], $config, array( 'defaultvalue'=>'LEFT', 'selectlabel'=>'', 'options'=>'Table=0||Query=1', 'storage_field'=>'json[options2][joins]['.$i.'][mode]', 'size'=>12, 'css'=>'mini valign' ) ),
										JCckDev::getForm( 'core_dev_text', @$options2['joins'][$i]['aka'], $config, array( 'defaultvalue'=>'', 'storage_field'=>'json[options2][joins]['.$i.'][aka]', 'size'=>38, 'css'=>'mini input-xlarge', 'attributes'=>'placeholder="Optionally AKA: \'aka_table1\', \'aka_table2\', ..."' ) )
									)
								) )
					)
				);

	$html	.=	JCckDev::renderLayoutFile(
					'cck'.JCck::v().'.form.field', array(
						'label'=>'&nbsp;',
						'html'=>JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.grid', array(
									'grid'=>'|50%|25%',
									'html'=>array(
										JCckDev::getForm( 'core_dev_select', @$options2['joins'][$i]['type'], $config, array( 'defaultvalue'=>'LEFT', 'selectlabel'=>'', 'options'=>'INNER=optgroup||INNER=INNER||OUTER=optgroup||LEFT=LEFT||RIGHT=RIGHT', 'storage_field'=>'json[options2][joins]['.$i.'][type]', 'size'=>12, 'css'=>'mini valign', 'attributes'=>'' ) ),
										JCckDev::getForm( 'core_dev_text', @$options2['joins'][$i]['table'], $config, array( 'storage_field'=>'json[options2][joins]['.$i.'][table]', 'size'=>60, 'css'=>'left mr-3 text-center', 'maxlength'=>'1024', 'attributes'=>'style="max-width:600px;"' ) ),
										JCckDev::getForm( 'core_dev_text', @$options2['joins'][$i]['column'], $config, array( 'storage_field'=>'json[options2][joins]['.$i.'][column]', 'size'=>12, 'css'=>'mini valign text-center' ) )
									)
								) )
					)
				);

	$html	.=	JCckDev::renderLayoutFile(
					'cck'.JCck::v().'.form.field', array(
						'label'=>'&nbsp;',
						'html'=>JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.grid', array(
									'grid'=>'|25%|25%',
									'html'=>array(
										JCckDev::getForm( 'core_dev_text', @$options2['joins'][$i]['table2'], $config, array( 'storage_field'=>'json[options2][joins]['.$i.'][table2]','size'=>50, 'css'=>'right text-center', 'attributes'=>'' ) )	 ,
										JCckDev::getForm( 'core_dev_text', @$options2['joins'][$i]['column2'], $config, array( 'storage_field'=>'json[options2][joins]['.$i.'][column2]', 'size'=>12, 'css'=>'mini valign text-center' ) ),
										JCckDev::getForm( 'core_dev_text', @$options2['joins'][$i]['and'], $config, array( 'storage_field'=>'json[options2][joins]['.$i.'][and]', 'size'=>12, 'css'=>'mini valign text-center', 'attributes'=>'placeholder="AND"' ) )
									)
								) )
					)
				);
}

// Set
$displayData    =   array(
                        'config'=>$config,
                        'form'=>array(
                            array(
                                'fields'=>array(
									$html
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