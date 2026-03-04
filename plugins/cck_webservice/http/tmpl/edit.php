<?php
/**
* @version 			SEBLOD WebServices 1.x
* @package			SEBLOD WebServices Add-on for SEBLOD 3.x
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\CMS\Language\Text;
?>
<div class="seblod">
	<?php echo JCckDev::renderLegend( Text::_( 'COM_CCK_SETTINGS' ), Text::_( 'PLG_CCK_WEBSERVICE_'.$this->item->type.'_DESC' ) ); ?>
    <ul class="adminformlist adminformlist-2cols">
        <?php
		echo JCckDev::renderForm( 'core_dev_text', @$options['url'], $config, array( 'label'=>'Url', 'size'=>112, 'required'=>'required', 'storage_field'=>'json[options][url]' ), array(), 'w100' );
		echo JCckDev::renderForm( 'core_dev_text', @$options['url_dev'], $config, array( 'label'=>'Url Dev', 'size'=>112, 'required'=>'', 'storage_field'=>'json[options][url_dev]' ), array(), 'w100' );
		echo JCckDev::renderForm( 'core_method', @$options['method'], $config, array( 'options'=>'DELETE=delete||GET=get||PATCH=patch||POST=post||PUT=put', 'storage_field'=>'json[options][method]' ) );
        ?>
    </ul>
</div>