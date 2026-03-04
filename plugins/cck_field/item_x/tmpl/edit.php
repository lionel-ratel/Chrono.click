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

$extended   =   explode( '||', $this->item->extended );
$items      =   JCckDatabase::loadObjectList( 'SELECT title, name FROM #__cck_core_fields WHERE type = "item_x" AND bool = 2' );
$locations  =   explode( '||', $this->item->location );
$options    =   array();
$options2   =   JCckDev::fromJSON( $this->item->options2 );

foreach ( $items as $item ) {
    $options[]  =   $item->title.'='.$item->name;
}
$options    =   implode( '||', $options );
?>

<div class="seblod">
	<?php echo JCckDev::renderLegend( Text::_( 'COM_CCK_CONSTRUCTION' ), Text::_( 'PLG_CCK_FIELD_'.$this->item->type.'_DESC' ) ); ?>
    <ul class="adminformlist adminformlist-2cols">
        <?php
        echo JCckDev::renderForm( 'core_label', $this->item->label, $config );
        echo JCckDev::renderForm( 'core_extended', @$extended[0], $config, array( 'label'=>_C4_TEXT ) );
        echo JCckDev::renderForm( 'core_bool', $this->item->bool, $config, array( 'label'=>'Behavior', 'defaultvalue'=>'0', 'options'=>'Standard=optgroup||Many to One=0||Multiple=optgroup||Many to Many=1||One to Many=2' ) );
        echo JCckDev::renderForm( 'core_dev_select', @$extended[1], $config, array( 'label'=>'Bidirectional', 'selectlabel'=>'No', 'options'=>$options, 'bool8'=>0, 'storage_field'=>'extended2' ) );
        echo JCckDev::renderForm( 'core_extended', @$locations[2], $config, array( 'label'=>'CONFIG_SEARCH_ALT', 'required'=>'', 'storage_field'=>'location3' ) );

        echo JCckDev::renderSpacer( Text::_( 'COM_CCK_ADD' ) );
        echo JCckDev::renderForm( 'core_bool2', $this->item->bool2, $config, array( 'label'=>'Mode', 'defaultvalue'=>'-2', 'options'=>'None=-2||Modal Box=0' ) );
        echo JCckDev::renderForm( 'core_extended', @$locations[0], $config, array( 'label'=>_C2_TEXT, 'storage_field'=>'location' ) );
        echo JCckDev::renderForm( 'core_dev_textarea', @$options2['add_custom'], $config, array( 'label'=>'Custom Variables', 'defaultvalue'=>'', 'cols'=>92, 'rows'=>1, 'storage_field'=>'json[options2][add_custom]' ), array(), 'w100' );

        echo JCckDev::renderForm( 'core_dev_select', @$options2['identifier'], $config, array( 'label'=>'Identifier', 'defaultvalue'=>'pk', 'selectlabel'=>'', 'options'=>'ID=id||Primary Key=pk', 'storage_field'=>'json[options2][identifier]' ) );

        echo JCckDev::renderSpacer( Text::_( 'COM_CCK_SELECT' ) );
        echo JCckDev::renderForm( 'core_bool3', $this->item->bool3, $config, array( 'label'=>'Mode', 'defaultvalue'=>'0', 'options'=>'None=-2||Yes=optgroup||Checkbox List=-1||Modal Box=0' ) );
        echo JCckDev::renderForm( 'core_extended', @$locations[1], $config, array( 'label'=>_C4_TEXT, 'storage_field'=>'location2' ) );
        echo JCckDev::renderBlank();
        echo JCckDev::renderForm( 'core_dev_select', @$options2['select_task'], $config, array( 'label'=>'Config Search Task', 'defaultvalue'=>'search', 'selectlabel'=>'', 'options'=>'No=no||Yes=search', 'storage_field'=>'json[options2][select_task]' ) );
        echo JCckDev::renderForm( 'core_dev_textarea', @$options2['select_custom'], $config, array( 'label'=>'Custom Variables', 'defaultvalue'=>'', 'cols'=>92, 'rows'=>1, 'storage_field'=>'json[options2][select_custom]' ), array(), 'w100' );

        echo JCckDev::renderSpacer( Text::_( 'COM_CCK_BATCH' ) );
        echo JCckDev::renderForm( 'core_bool4', $this->item->bool4, $config, array( 'label'=>'Mode', 'defaultvalue'=>'0', 'options'=>'Disabled=0||Enabled=1' ) );
        echo JCckDev::renderBlank();

		echo JCckDev::renderSpacer( Text::_( 'COM_CCK_STORAGE' ), Text::_( 'COM_CCK_STORAGE_DESC' ) );
		echo JCckDev::getForm( 'core_storage', $this->item->storage, $config );
        ?>
    </ul>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
    $('#location').isDisabledWhen('bool2','-2');
    $('#location2').isDisabledWhen('bool3','-2');
    $('#location2,#json_options2_select_task,#json_options2_select_custom').isVisibleWhen('bool3','0');
    $('#extended2').isVisibleWhen('bool','0');
    $('#extended2').isDisabledWhen('bool','1,2');
    $('#location3').isVisibleWhen('bool','1');
    $("#adminForm").on("change", "#bool", function() {
        if ($(this).val()!="0") {
            $("#storage").val("none").prop("disabled",true);
        } else {
            $("#storage").prop("disabled",false);
        }
    });
    if ($("#bool").val()!="0") {
        $("#storage").val("none").prop("disabled",true);
    } else {
        $("#storage").prop("disabled",false);
    }
});
</script>