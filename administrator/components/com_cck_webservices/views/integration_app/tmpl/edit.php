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

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$config	=	JCckDev::init( array( '42', 'checkbox', 'password', 'radio', 'select_dynamic', 'select_simple', 'text', 'textarea', 'wysiwyg_editor' ), true, array( 'item'=>$this->item, 'vName'=>$this->vName ) );
Helper_Include::addDependencies( $this->getName(), $this->getLayout() );
?>

<form action="<?php echo Route::_( 'index.php?option='.$this->option.'&view='.$this->getName().'&layout=edit&id='.(int)$this->item->id ); ?>" method="post" id="adminForm" name="adminForm">

<div class="<?php echo $this->css['wrapper']; ?>">
	<div class="seblod first">
        <ul class="spe spe_title">
            <?php echo JCckDev::renderForm( 'more_webservices_resource_title', $this->item->title, $config ); ?>
        </ul>
        <ul class="spe spe_folder">
            <?php echo JCckDev::renderForm( 'core_dev_select', $this->item->type, $config, array( 'label'=>'Type', 'selectlabel'=>'Select', 'options'=>'API=resources||Project=platform', 'storage_field'=>'type', 'required'=>'required' ) ); ?>
        </ul>
        <ul class="spe spe_folder">
            <?php echo JCckDev::renderForm( 'more_webservices_resource_name', $this->item->name, $config, array( 'label'=>'Name' ) ); ?>
        </ul>
        <ul class="spe spe_state spe_third">
            <?php echo JCckDev::renderForm( 'core_state', $this->item->published, $config, array( 'label'=>'clear', 'defaultvalue'=>1 ) ); ?>
        </ul>
        <ul class="spe spe_description">
            <?php echo JCckDev::renderForm( 'core_description', $this->item->description, $config, array( 'label'=>'clear', 'selectlabel'=>'Description' ) ); ?>
        </ul>
	</div>
    
    <div class="seblod">
        <div class="legend top left"><?php echo Text::_( 'COM_CCK_SETTINGS' ); ?></div>
        <ul class="adminformlist adminformlist-2cols">
            <?php
            echo JCckDev::renderForm( 'more_webservices_app_auth', @$this->item->auth_id, $config );
            echo '<li><label>'.Text::_( 'COM_CCK_RUN_AS' ).'</label>'
               . JCckDev::getForm( 'core_dev_select', ( $this->item->run_as ? '1' : '' ), $config, array( 'selectlabel'=>'Inherited', 'options'=>'User=1', 'storage_field'=>'run_as_mode' ) )
               . JCckDev::getForm( 'core_dev_text', $this->item->run_as, $config, array( 'defaultvalue'=>'0', 'size'=>'6', 'storage_field'=>'run_as' ) )
               . '</li>';

            echo JCckDev::renderForm( 'more_webservices_resource_methods', @$this->item->methods, $config, array( 'defaultvalue'=>'' ), array(), 'w100' );
            ?>
        </ul>
    </div>
    <div class="seblod">
        <div class="legend top left"><?php echo Text::_( 'COM_CCK_ENCRYPTION' ); ?></div>
        <ul class="adminformlist adminformlist-2cols">
            <?php
            $options    =   json_decode( $this->item->options, true );

            // echo JCckDev::renderForm( 'core_dev_text', $this->item->nonce, $config, array( 'label'=>'Nonce', 'defaultvalue'=>'', 'storage_field'=>'nonce', 'attributes'=>'placeholder="'.Text::_( 'COM_CCK_AUTOMATICALLY_GENERATED' ).'"' ) );
            echo JCckDev::renderForm( 'core_dev_text', @$options['key_private'], $config, array( 'label'=>'Private Key Env', 'defaultvalue'=>'', 'storage_field'=>'json[options][key_private]' ) );
            echo JCckDev::renderForm( 'core_dev_text', @$options['key_public'], $config, array( 'label'=>'Public Key Env', 'defaultvalue'=>'', 'storage_field'=>'json[options][key_public]' ) );            
            ?>
        </ul>
    </div>
</div>

<div class="clr"></div>
<div>
    <input type="hidden" id="task" name="task" value="" />
    <input type="hidden" id="myid" name="id" value="<?php echo @$this->item->id; ?>" />
    <?php
	JCckDev::validate( $config );
    echo HTMLHelper::_( 'form.token' );
	?>
</div>
</form>

<?php
Helper_Display::quickCopyright();
?>

<script type="text/javascript">
(function ($){
    JCck.Dev = {
        submit: function(task) {
            Joomla.submitbutton(task);
        }
    };
    Joomla.submitbutton = function(task) {
        if (task == "integration_app.cancel" || $("#adminForm").validationEngine("validate",task) === true) {
            JCck.submitForm(task, document.getElementById('adminForm'));
        }
    };
    $(document).ready(function() {
        $('#run_as_mode,#auth_id,#methods').isVisibleWhen('type','resources');
        $('#run_as_id').isVisibleWhen('run_as_mode','1');
        $('#run_as').isVisibleWhen('run_as_mode','1',false);
        $('#json_options_key_private,#json_options_key_public').isVisibleWhen('type','platform');
    });
})(jQuery);
</script>