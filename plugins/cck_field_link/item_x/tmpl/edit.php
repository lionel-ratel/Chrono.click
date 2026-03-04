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

JCckDev::initScript( 'link', $this->item );
?>

<div class="seblod">
	<?php echo JCckDev::renderLegend( Text::_( 'COM_CCK_CONSTRUCTION' ), Text::_( 'PLG_CCK_FIELD_LINK_'.$this->item->name.'_DESC' ) ); ?>
    <ul class="adminformlist adminformlist-2cols">
        <?php
        echo JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Task', 'selectlabel'=>'', 'defaultvalue'=>'', 'options'=>'Add=add||Select=select||Current=optgroup||Assign=assign||Fill=fill||Process=process||Remove=remove||Save=save||Selection=optgroup||Assign=assign_multiple', 'storage_field'=>'type', 'required'=>'required' ) );
        echo JCckDev::renderForm( 'core_form', '', $config, array( 'selectlabel'=>'Inherited', 'options2'=>'{"query":"","table":"#__cck_core_types","name":"title","where":"published=1 AND location != \"collection\"","value":"name","orderby":"title","orderby_direction":"ASC"}', 'required'=>'' ) );
        echo JCckDev::renderForm( 'core_task_processing', '', $config, array( 'storage_field'=>'processing' ) );
        echo JCckDev::renderForm( 'core_dev_bool', '', $config, array( 'defaultvalue'=>'0', 'label'=>'Close', 'storage_field'=>'close' ) );
        echo JCckDev::renderForm( 'core_dev_bool', '', $config, array( 'defaultvalue'=>'0', 'label'=>'Confirm', 'storage_field'=>'confirm' ) );
		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Alert', 'defaultvalue'=>'Please Confirm', 'size'=>32, 'storage_field'=>'confirm_alert' ) );
        echo JCckDev::renderForm( 'core_dev_bool', '', $config, array( 'label'=>'Identifier', 'defaultvalue'=>'pk', 'options'=>'ID=id||Primary Key=pk||Use Value=optgroup||Field=field', 'storage_field'=>'identifier' ) );
        echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Field Name', 'storage_field'=>'identifier_fieldname' ) );
        echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Target', 'storage_field'=>'target_fieldname' ) );

		echo JCckDev::renderSpacer( Text::_( 'COM_CCK_CONSTRUCTION' ) . '<span class="mini">('.Text::_( 'COM_CCK_GENERIC' ).')</span>' );
		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Class', 'size'=>24, 'storage_field'=>'class' ) );
        echo '<li><label>'.Text::_( 'COM_CCK_TITLE' ).'</label>'
            . JCckDev::getForm( 'core_dev_select', '', $config, array( 'selectlabel'=>'None', 'options'=>'Custom Text=2||Translated Text=3', 'storage_field'=>'title' ) )
            . JCckDev::getForm( 'core_dev_text', '', $config, array( 'label'=>'Title', 'size'=>16, 'css'=>'input-medium', 'storage_field'=>'title_custom' ) )
            . '</li>';        
        ?>
    </ul>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
	$('#close').isVisibleWhen('type','remove');
	$('#confirm_alert').isVisibleWhen('confirm','1');
    $('#processing').isVisibleWhen('type','process');
    $('#title_custom').isVisibleWhen('title','2,3',false);
    $('#identifier_fieldname').isVisibleWhen('identifier','field');
    $('#form').isVisibleWhen('type','add');
    $('#target_fieldname').isVisibleWhen('type','select');
});
</script>