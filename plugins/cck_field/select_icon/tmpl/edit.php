<?php
/**
* @version 			SEBLOD 3.x More
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2016 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\CMS\Language\Text;

$options2	=	JCckDev::fromJSON( $this->item->options2 );
?>

<div class="seblod">
    <?php echo JCckDev::renderLegend( Text::_( 'COM_CCK_CONSTRUCTION' ), Text::_( 'PLG_CCK_FIELD_'.$this->item->type.'_DESC' ) ); ?>
    <ul class="adminformlist adminformlist-2cols">
        <?php
        echo JCckDev::renderForm( 'core_label', $this->item->label, $config );
        echo JCckDev::renderForm( 'core_defaultvalue', $this->item->defaultvalue, $config );
		echo JCckDev::renderForm( 'core_options_path', @$options2['path'], $config, array( 'required'=>'required','defaultvalue'=>'templates/octo/css/fonts/project/' ) );	
		echo JCckDev::renderForm( 'core_selectlabel', $this->item->selectlabel, $config, array( 'defaultvalue'=>'Select' ) );
		echo JCckDev::renderForm( 'core_dev_text', @$options2['file'], $config, array( 'label'=>'CSS File','defaultvalue'=>'style.css', 'required'=>'required', 'storage_field'=>'json[options2][file]' ) );
        echo JCckDev::renderForm( 'core_dev_select', @$options2['icontype'], $config, array( 'label'=>'Type', 'defaultvalue'=>'', 'options'=>'Octo=octo||Icon=icon', 'storage_field'=>'json[options2][icontype]' ) );
        echo JCckDev::renderForm( 'core_dev_select', @$options2['filedata'], $config, array( 'label'=>'CSS Data File', 'defaultvalue'=>'', 'options'=>'Generate Load=2||Load=1', 'storage_field'=>'json[options2][filedata]' ) );
        echo JCckDev::renderSpacer( Text::_( 'COM_CCK_STORAGE' ), Text::_( 'COM_CCK_STORAGE_DESC' ) );
        echo JCckDev::getForm( 'core_storage', $this->item->storage, $config );
        ?>
    </ul>
</div>