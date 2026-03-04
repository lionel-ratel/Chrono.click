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

if ( JCck::is( '4' ) ) {
	$opts_raw	=	array( 'label'=>'Raw Rendering', 'options'=>'Inherited SL=-1||No=0||Yes=1', 'defaultvalue'=>'-1' );
} else {
	$opts_raw	=	array( 'label'=>'Raw Rendering', 'defaultvalue'=>'0' );
}

JCckDev::initScript( 'field', $this->item, array( 'hasOptions'=>true, 'fieldPicker'=>true ) );
$options	=	JCckDev::fromSTRING( $this->item->options );
$options2	=	JCckDev::fromJSON( $this->item->options2 );
?>

<div class="seblod">
	<?php echo JCckDev::renderLegend( Text::_( 'COM_CCK_CONSTRUCTION' ), Text::_( 'PLG_CCK_FIELD_'.$this->item->type.'_DESC' ) ); ?>
    <ul class="adminformlist adminformlist-2cols">
        <?php
        echo JCckDev::renderForm( 'core_label', $this->item->label, $config );
		echo JCckDev::renderForm( 'core_extended', $this->item->extended, $config, array( 'label'=>_C4_TEXT ) );
		echo JCckDev::renderForm( 'core_options_display', $this->item->sorting, $config, array( 'defaultvalue'=>'-1', 'options'=>'Hide=-1' ) );
		echo JCckDev::renderForm( 'core_extended', $this->item->location, $config, array( 'label'=>'CONFIG_SEARCH_ALT', 'required'=>'', 'storage_field'=>'location' ) );
		echo JCckDev::renderForm( 'core_options', $options, $config, array( 'label'=>'Fields', 'rows'=>1 ), array( 'after'=>$this->item->init['fieldPicker'] ) );
		echo '<li><label>'.Text::_( 'COM_CCK_COUNT' ).' / '.Text::_( 'COM_CCK_START' ).'</label>'
		 .	 JCckDev::getForm( 'core_dev_text', $this->item->bool4, $config, array( 'label'=>'', 'defaultvalue'=>'0', 'size'=>'3', 'storage_field'=>'bool4' ) )
		 .	 JCckDev::getForm( 'core_dev_text', ( (string)$this->item->bool5 == '0' ? '' : (string)$this->item->bool5 ), $config, array( 'label'=>'', 'defaultvalue'=>'', 'size'=>'3', 'storage_field'=>'bool5' ) )
		 .	'</li>';

		echo JCckDev::renderSpacer( Text::_( 'COM_CCK_OUTPUT' ), Text::_( 'COM_CCK_OUTPUT' ), 2 );
		echo JCckDev::renderForm( 'core_bool', $this->item->bool, $config, $opts_raw );
		echo '<li><label>'.Text::_( 'COM_CCK_CONFIG_SHOW_PAGINATION' ).'</label>'
		 .	 JCckDev::getForm( 'core_show_pagination', $this->item->bool3, $config, array( 'defaultvalue'=>-2, 'label'=>'CONFIG_SHOW_PAGINATION', 'options'=>'Hide=-2||Infinite=optgroup||Click=2||Load=8', 'storage_field'=>'bool3' ) )
		 .	 JCckDev::getForm( 'core_pagination', @$options2['pagination'], $config, array( 'label'=>'', 'selectlabel'=>'Inherited', 'defaultvalue'=>'', 'storage_field'=>'json[options2][pagination]' ) )
		 .	'</li>';

		echo JCckDev::renderForm( 'core_dev_text', @$options2['class_pagination'], $config, array( 'label'=>'CONFIG_PAGINATION_CLASS', 'defaultvalue'=>'pagination', 'size'=>16, 'storage_field'=>'json[options2][class_pagination]' ) );
		echo JCckDev::renderForm( 'core_dev_text', @$options2['callback_pagination'], $config, array( 'label'=>'Config Pagination Callback', 'storage_field'=>'json[options2][callback_pagination]' ) );
		echo JCckDev::renderForm( 'core_dev_select', @$options2['show_form'], $config, array( 'defaultvalue'=>'', 'label'=>'CONFIG_SHOW_SEARCH_FORM', 'selectlabel'=>'Auto', 'options'=>'Hide=0||Prepare=-1||Show=1', 'storage_field'=>'json[options2][show_form]' ) );
		echo JCckDev::renderBlank( '<input type="hidden" id="blank_li3" value="" />' );
		echo JCckDev::renderForm( 'core_dev_select', @$options2['show_more'], $config, array( 'defaultvalue'=>0, 'label'=>'CONFIG_SHOW_MORE', 'selectlabel'=>'', 'options'=>'No=0||Yes=1||Only if results=2||Only if more results=3', 'storage_field'=>'json[options2][show_more]' ) );
		echo JCckDev::renderForm( 'core_menuitem', @$options2['show_link_more'], $config, array( 'label'=>'CONFIG_SHOW_MORE_LINK', 'selectlabel'=>'None', 'storage_field'=>'json[options2][show_link_more]', 'required'=>'required' ) );
		echo JCckDev::renderForm( 'core_dev_text', @$options2['link_more_class'], $config, array( 'defaultvalue'=>'', 'label'=>'CONFIG_SHOW_MORE_LINK_CLASS', 'storage_field'=>'json[options2][link_more_class]' ) );
		echo JCckDev::renderForm( 'core_dev_text', @$options2['link_more_text'], $config, array( 'defaultvalue'=>'', 'label'=>'CONFIG_SHOW_MORE_LINK_TEXT', 'storage_field'=>'json[options2][link_more_text]' ) );
		echo JCckDev::renderForm( 'core_dev_textarea', @$options2['link_more_variables'], $config, array( 'defaultvalue'=>'', 'label'=>'CONFIG_SHOW_MORE_LINK_VARIABLES', 'cols'=>88, 'storage_field'=>'json[options2][link_more_variables]' ), array(), 'w100' );

		echo JCckDev::renderBlank( '<input type="hidden" id="blank_li2" value="" />' );
		echo JCckDev::renderForm( 'core_bool', $this->item->bool6, $config, array( 'defaultvalue'=>0, 'label'=>'RESOURCE_AS_FRAGMENT', 'storage_field'=>'bool6' ) );
		echo JCckDev::renderForm( 'core_menuitem', @$options2['link_resource'], $config, array( 'label'=>'Target', 'selectlabel'=>'Select', 'options'=>'Use Value=optgroup||Field=-2', 'storage_field'=>'json[options2][link_resource]', 'required'=>'required' ) );
		echo JCckDev::renderBlank( '<input type="hidden" id="blank_li3" value="" />' );
		echo JCckDev::renderForm( 'core_dev_text', @$options2['link_resource_fieldname'], $config, array( 'label'=>'Field Name', 'storage_field'=>'json[options2][link_resource_fieldname]' ) );
		echo JCckDev::renderForm( 'core_dev_textarea', @$options2['json_resource'], $config, array( 'label'=>'Parameters', 'cols'=>80, 'rows'=>1, 'storage_field'=>'json[options2][json_resource]' ), array(), 'w100' );

		echo JCckDev::renderSpacer( Text::_( 'COM_CCK_STORAGE' ), Text::_( 'COM_CCK_STORAGE_DESC' ), 3 );
		echo JCckDev::getForm( 'core_storage', $this->item->storage, $config );
        ?>
    </ul>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
	$('#json_options2_class_pagination,#json_options2_callback_pagination').isVisibleWhen('bool3','2,8');
	$('#json_options2_pagination').isVisibleWhen('bool3','2,8',false);
	$('#json_options2_json_resource,#json_options2_link_resource').isVisibleWhen('bool6','1');
	$('#json_options2_show_link_more,#json_options2_link_more_class,#json_options2_link_more_text,#json_options2_link_more_variables').isVisibleWhen('json_options2_show_more','1,2,3');
	$('#blank_li2').isVisibleWhen('json_options2_show_more','0');

	$('div#layer').on('change', '#sorting', function() {
		if ($(this).val() == -1) {
			$('#storage').val('none').prop('disabled',true).trigger('change');
		} else {
			$('#storage').val('standard').prop('disabled',false).trigger('change');
		}
	});

	if ($('#sorting').val() == -1) {
		$('#storage').val('none').prop('disabled',true).trigger('change');
	} else {
		$('#storage').val('standard').prop('disabled',false).trigger('change');
	}
});
</script>