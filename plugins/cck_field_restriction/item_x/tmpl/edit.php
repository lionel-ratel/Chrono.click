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

JCckDev::initScript( 'restriction', $this->item );
?>

<div class="seblod">
	<?php echo JCckDev::renderLegend( Text::_( 'COM_CCK_CONSTRUCTION' ), Text::_( 'PLG_CCK_FIELD_RESTRICTION_'.$this->item->name.'_DESC' ) ); ?>
    <ul class="adminformlist adminformlist-2cols">
        <?php
        echo JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Behavior', 'selectlabel'=>'Any Behavior', 'options'=>'Standard=0||Multiple=1', 'required'=>'', 'storage_field'=>'mode' ) );
		echo JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Task', 'selectlabel'=>'Any Task', 'options'=>'Add=add||Batch=batch||Select Checkbox List=check||Select Modal Box=select||Search=search', 'required'=>'', 'storage_field'=>'task' ) );
		echo JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Validation', 'selectlabel'=>'', 'options'=>'Always=||Validation=optgroup||Optional=0||Required=1', 'required'=>'', 'storage_field'=>'required' ) );
		echo JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Variation', 'selectlabel'=>'Any Variation', 'options'=>'Hidden=hidden||Visible=visible||Visible Form=visible_form||Variation=optgroup||Form=form||Readonly=disabled||Value=value', 'required'=>'', 'storage_field'=>'variation' ) );
		echo JCckDev::renderForm( 'core_bool', '', $config, array( 'label'=>'Invert', 'defaultvalue'=>'0', 'options'=>'Yes=1||No=0', 'storage_field'=>'do' ) );
        ?>
    </ul>
</div>
