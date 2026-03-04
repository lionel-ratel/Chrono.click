<?php
/**
* @version 			SEBLOD 3.x More
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\CMS\Language\Text;
?>

<div class="seblod">
	<?php echo JCckDev::renderLegend( Text::_( 'COM_CCK_CONSTRUCTION' ), Text::_( 'PLG_CCK_FIELD_'.$this->item->type.'_DESC' ) ); ?>
    <ul class="adminformlist adminformlist-2cols">
        <?php
        echo JCckDev::renderForm( 'core_label', $this->item->label, $config );
        echo JCckDev::renderForm( 'core_defaultvalue', $this->item->defaultvalue, $config );
		echo JCckDev::renderForm( 'core_bool', $this->item->bool, $config, array( 'label'=>'Behavior', 'defaultvalue'=>'0', 'options'=>'Standard=0||Multiple=1' ) );

        echo '<li><label>'.Text::_( 'COM_CCK_FOLDER' ).'</label>'
         .   JCckDev::getForm( 'core_dev_bool', @$options2['path_type'], $config, array( 'defaultvalue'=>'0', 'options'=>'Root=0||Target=optgroup||Resources=1', 'storage_field'=>'json[options2][path_type]' ) )
         .   JCckDev::getForm( 'core_options_path', @$options2['path'], $config, array( 'required'=> 'required', 'size'=>22 ) )
         .   JCckDev::getForm( 'core_options_path_content', @$options2['path_content'], $config )
         .   '</li>';

        echo '<li><label>'.Text::_( 'COM_CCK_LEGAL_EXTENSIONS' ).'</label>'
         .   JCckDev::getFormFromHelper( array( 'component'=>'com_cck', 'function'=>'getMediaExtensions', 'name'=>'core_options_media_extensions' ), $media_ext, $config, array( 'storage_field'=>'json[options2][media_extensions]' ) )
         .   JCckDev::getForm( 'core_options_legal_extensions', @$options2['legal_extensions'], $config, array( 'size'=>13, 'required'=>'required' ) )
         .   '</li>';

        echo '<li><label>'.Text::_( 'COM_CCK_MAXIMUM_SIZE' ).'</label>'
         .   JCckDev::getForm( 'core_options_max_size', @$options2['max_size'], $config )
         .   JCckDev::getForm( 'core_options_size_unit', @$options2['size_unit'], $config )
         .   '</li>';

        echo JCckDev::renderForm( 'core_dev_select', @$options2['forbidden_extensions'], $config, array( 'label'=>'Forbidden Extensions', 'selectlabel'=>'Inherited', 'options'=>'None=0||Whitelist=1', 'storage_field'=>'json[options2][forbidden_extensions]' ) );

		echo JCckDev::renderSpacer( Text::_( 'COM_CCK_STORAGE' ), Text::_( 'COM_CCK_STORAGE_DESC' ) );
		echo JCckDev::getForm( 'core_storage', $this->item->storage, $config );
        ?>
    </ul>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
    $('#json_options2_legal_extensions').isVisibleWhen('json_options2_media_extensions','custom',false);    
});
</script>