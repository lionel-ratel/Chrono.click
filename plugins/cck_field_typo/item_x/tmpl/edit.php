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

JCckDev::initScript( 'typo', $this->item );
?>

<div class="seblod">
	<?php echo JCckDev::renderLegend( Text::_( 'COM_CCK_CONSTRUCTION' ), Text::_( 'PLG_CCK_FIELD_TYPO_'.$this->item->name.'_DESC' ) ); ?>
    <ul class="adminformlist adminformlist-2cols">
        <?php
        echo JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Behavior', 'selectlabel'=>'', 'defaultvalue'=>'identifier', 'options'=>'Item=optgroup||Identifier=identifier||Join=optgroup||Property Identifier=property_identifier||Property Identifier Group=property_identifier_group', 'storage_field'=>'type', 'required'=>'required' ) );
        echo JCckDev::renderBlank();
        echo JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Identifier', 'defaultvalue'=>'', 'selectlabel'=>'Inherited', 'options'=>'ID=id||Primary Key=pk||Unique Key=pkey', 'storage_field'=>'identifier' ) );
        echo '<li><label>'.Text::_( 'COM_CCK_NAME' ).'</label>'
            . JCckDev::getForm( 'core_dev_select', '', $config, array( 'selectlabel'=>'Inherited', 'options'=>'Custom=-1', 'storage_field'=>'identifier_property' ) )
            . JCckDev::getForm( 'core_dev_text', '', $config, array( 'label'=>'Name', 'defaultvalue'=>'', 'size'=>20, 'storage_field'=>'identifier_name' ) )
            . '</li>';

        echo JCckDev::renderSpacer( Text::_( 'COM_CCK_CONSTRUCTION' ) . '<span class="mini">('.Text::_( 'COM_CCK_HTML' ).')</span>' );
        echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Class', 'defaultvalue'=>'', 'storage_field'=>'class' ) );
        echo JCckDev::renderForm( 'core_required', '', $config, array( 'defaultvalue'=>'0', 'selectlabel'=>'', 'options'=>'No=||Yes=required||Yes GroupRequired=grouprequired' ) );
        echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Validation', 'defaultvalue'=>'', 'storage_field'=>'validation' ) );
        echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Group', 'required'=>'required', 'storage_field'=>'required2' ) );
        ?>
    </ul>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
    $('#required2').isVisibleWhen('required','grouprequired');
    $('#identifier_name').isVisibleWhen('identifier_property','-1',false);  
});
</script>