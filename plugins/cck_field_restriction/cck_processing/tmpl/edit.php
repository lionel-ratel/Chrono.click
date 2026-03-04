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
		echo JCckDev::renderForm( 'core_task_processing', '', $config, array( 'storage_field'=>'processing' ) );
		echo JCckDev::renderForm( 'core_bool', '', $config, array( 'label'=>'Invert', 'defaultvalue'=>'0', 'options'=>'Yes=1||No=0', 'storage_field'=>'do' ) );

        echo JCckDev::renderSpacer( Text::_( 'COM_CCK_CONSTRUCTION' ) . '<span class="mini">('.Text::_( 'COM_CCK_GENERIC' ).')</span>' );
		echo JCckDev::renderForm( 'core_dev_bool', '', $config, array( 'label'=>'Priority', 'defaultvalue'=>'', 'selectlabel'=>'Inherited', 'options'=>'BeforeRender=optgroup||4||5', 'storage_field'=>'priority' ) );
        ?>
    </ul>
</div>