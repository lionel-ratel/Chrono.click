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

JCckDev::initScript( 'field', $this->item, array( 'hasOptions'=>true, 'fieldPicker'=>true ) );
$options	=	JCckDev::fromSTRING( $this->item->options );
$options2	=	JCckDev::fromJSON( $this->item->options2 );
$options_2	=	JCckDev::fromSTRING( @$options2['forms'] );
?>

<div class="seblod">
	<?php echo JCckDev::renderLegend( Text::_( 'COM_CCK_CONSTRUCTION' ), Text::_( 'PLG_CCK_FIELD_'.$this->item->type.'_DESC' ) ); ?>
	<ul class="adminformlist adminformlist-2cols">
		<?php
		echo JCckDev::renderForm( 'core_label', $this->item->label, $config );
		echo JCckDev::renderForm( 'core_defaultvalue', $this->item->defaultvalue, $config );
		echo JCckDev::renderForm( 'core_extended', $this->item->extended, $config, array( 'label' => _C2_TEXT ) );
		
		echo JCckDev::renderForm( 'core_options_display', $this->item->sorting, $config, array( 'defaultvalue'=>'-1' ) );
		echo JCckDev::renderForm( 'core_selectlabel', $this->item->selectlabel, $config );
		echo JCckDev::renderBlank( '<input type="hidden" id="blank_li" value="" />' );
		echo JCckDev::renderForm( 'core_options', $options, $config, array( 'label'=>'Fields', 'rows'=>1 ), array( 'after'=>$this->item->init['fieldPicker'] ) );
		echo JCckDev::renderForm( 'core_options', $options_2, $config, array( 'label'=>'Options', 'rows'=>3, 'storage_field'=>'json[options2][string][forms]', 'name'=>'options_forms' ) );
		
		echo JCckDev::renderSpacer( Text::_( 'COM_CCK_STORAGE' ), Text::_( 'COM_CCK_STORAGE_DESC' ) );
		echo JCckDev::getForm( 'core_storage', $this->item->storage, $config );
        ?>
    </ul>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
	$('#defaultvalue, #selectlabel, #sortable_options_forms').isVisibleWhen('sorting','0,1,2');
	$('#extended,#blank_li').isVisibleWhen('sorting','-1');

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