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

$options2	=	JCckDev::fromJSON( $this->item->options2 );
?>

<div class="seblod">
	<?php echo JCckDev::renderLegend( Text::_( 'COM_CCK_CONSTRUCTION' ), Text::_( 'PLG_CCK_FIELD_'.$this->item->type.'_DESC' ) ); ?>
    <ul class="adminformlist adminformlist-2cols">
        <?php
        echo JCckDev::renderForm( 'core_dev_bool', $this->item->bool, $config, array( 'label'=>'Behavior', 'options'=>'Call=0||Stack=1||Stack as fallback=2||Store as fallback=3' ) );
        echo JCckDev::renderForm( 'more_webservice', $this->item->extended, $config );
        echo JCckDev::renderBlank( '<input type="hidden" id="blank_li" value="" />' );
        echo JCckDev::renderForm( 'core_dev_text', @$options2['store_identifier'], $config, array( 'label'=>'Identifier', 'storage_field'=>'json[options2][store_identifier]' ) );
        echo JCckDev::renderForm( 'more_webservice_call', @$options2['call'], $config );
		
		echo JCckDev::renderSpacer( Text::_( 'COM_CCK_STORAGE' ), Text::_( 'COM_CCK_STORAGE_DESC' ) );
		echo JCckDev::getForm( 'core_storage', $this->item->storage, $config );
        ?>
    </ul>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
    // $('#blank_li').isVisibleWhen('bool','0,1,2');
    // $('#json_options2_store_identifier').isVisibleWhen('bool','3');
});
</script>