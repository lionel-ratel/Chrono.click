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

JCckDev::forceStorage( 'none', true );
?>

<div class="seblod">
	<?php echo JCckDev::renderLegend( Text::_( 'COM_CCK_CONSTRUCTION' ), Text::_( 'PLG_CCK_FIELD_'.$this->item->type.'_DESC' ) ); ?>
    <ul class="adminformlist adminformlist-2cols">
        <?php
        echo JCckDev::renderForm( 'core_task_processing', $this->item->defaultvalue, $config, array( 'storage_field'=>'defaultvalue' ) );
		echo JCckDev::renderForm( 'core_bool2', $this->item->bool5, $config, array( 'label'=>'Priority', 'defaultvalue'=>'0', 'options'=>'0||BeforeRender=optgroup||1||2||3||4||5', 'storage_field'=>'bool5' ) );
        
		echo JCckDev::renderSpacer( Text::_( 'COM_CCK_STORAGE' ), Text::_( 'COM_CCK_STORAGE_DESC' ) );
		echo JCckDev::getForm( 'core_storage', $this->item->storage, $config );
        ?>
    </ul>
</div>