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

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$config	=	JCckDev::init( array( '42', 'password', 'radio', 'select_dynamic', 'select_simple', 'text', 'textarea', 'wysiwyg_editor' ), true, array( 'item'=>$this->item, 'vName'=>$this->vName ) );
Helper_Include::addDependencies( $this->getName(), $this->getLayout() );
?>

<form action="<?php echo Route::_( 'index.php?option='.$this->option.'&view='.$this->getName().'&layout=edit&id='.(int)$this->item->id ); ?>" method="post" id="adminForm" name="adminForm">

<div class="<?php echo $this->css['wrapper']; ?>">
	<div class="seblod first">
		<ul class="spe spe_title">
			<?php echo JCckDev::renderForm( 'more_webservices_resource_title', $this->item->title, $config ); ?>
		</ul>
		<ul class="spe spe_folder">
			<?php echo JCckDev::renderForm( 'core_dev_select', $this->item->type, $config, array( 'label'=>'Type', 'selectlabel'=>'Select', 'options'=>'API Key=api_key||Basic Auth=basic_auth||Bearer Token=token_auth', 'storage_field'=>'type', 'required'=>'required' ) ); ?>
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
			$options    =   json_decode( $this->item->options, true );

			$api_key    =   $this->item->id ? @$options['value'] : hash( 'sha256', time().openssl_random_pseudo_bytes( 64 ) );
			echo JCckDev::renderForm( 'core_dev_text', @$options['key'], $config, array( 'label'=>'Key', 'storage_field'=>'api_key[key]', 'required'=>'required' ) );
			echo JCckDev::renderForm( 'core_dev_text', $api_key, $config, array( 'label'=>'Value', 'storage_field'=>'api_key[value]', 'required'=>'required' ) );
			echo JCckDev::renderForm( 'core_dev_select', @$options['mode'], $config, array( 'label'=>'Mode', 'selectlabel'=>'', 'options'=>'HTTP Header=0||Query Params=1', 'storage_field'=>'api_key[mode]', 'required'=>'required' ) );

			echo JCckDev::renderForm( 'core_dev_text', @$options['username'], $config, array( 'label'=>'Username', 'storage_field'=>'basic_auth[username]', 'required'=>'required' ) );
			echo JCckDev::renderForm( 'core_dev_text', @$options['password'], $config, array( 'label'=>'Password', 'storage_field'=>'basic_auth[password]', 'required'=>'required' ) );

			echo JCckDev::renderForm( 'core_dev_textarea', @$options['token'], $config, array( 'label'=>'Token', 'cols'=>'88', 'rows'=>'8', 'css'=>'input-xxlarge', 'storage_field'=>'token_auth[token]', 'required'=>'required' ), array(), 'w100' );

			$expires_in	=	'';

			if ( $options['expires_at'] ) {
				$exp	=	Factory::getDate( $options['expires_at'] );
				$now	=	Factory::getDate();

				if ( $exp < $now ) {
					$expires_in	=	Text::_( 'COM_CCK_EXPIRED' );
				} else {
					$expires_in =	Text::sprintf( 'COM_CCK_EXPIRES_IN', (int)( ( $exp->toUnix() - $now->toUnix() ) / 60 ) );
				}
				?>
				<style>.token-exp{margin-top:-16px!important;} code{padding: 2px 4px; color: #d14; background-color: #f7f7f9; border: 1px solid #e1e1e8; white-space: nowrap;}</style>
				<li class="token-exp"><label>&nbsp;</label><span class="variation_value"><code><?php echo $expires_in; ?></code></span><input type="hidden" id="token_auth_expires_at" /></li>
				<?php
			}
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
		if (task == "integration_auth.cancel" || $("#adminForm").validationEngine("validate",task) === true) {
			JCck.submitForm(task, document.getElementById('adminForm'));
		}
	};
	$(document).ready(function() {
		$('#api_key_key').isVisibleWhen('type','api_key');
		$('#api_key_value').isVisibleWhen('type','api_key');
		$('#api_key_mode').isVisibleWhen('type','api_key');
		$('#basic_auth_username').isVisibleWhen('type','basic_auth');
		$('#basic_auth_password').isVisibleWhen('type','basic_auth');
		$('#token_auth_token').isVisibleWhen('type','token_auth');
		$('#token_auth_expires_at').isVisibleWhen('type','token_auth');
	});
})(jQuery);
</script>