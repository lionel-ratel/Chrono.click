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

$config		=	JCckDev::init( array(), true, array( 'item' => $this->item, 'vName'=>$this->vName ) );
$ajax_load	=	'components/com_cck/assets/styles/seblod/images/ajax.gif';
Helper_Include::addDependencies( $this->getName(), $this->getLayout() );
?>

<form action="<?php echo Route::_( 'index.php?option='.$this->option.'&view='.$this->getName().'&layout=edit&id='.(int)$this->item->id ); ?>" method="post" id="adminForm" name="adminForm">

<div class="<?php echo $this->css['wrapper']; ?>">
	<div class="seblod first">       
        <div id="loading" class="loading"></div>
        <ul class="spe spe_title">
            <?php
            echo JCckDev::renderForm( 'more_webservices_webservice_title', $this->item->title, $config );
			echo '<input type="hidden" id="name" name="name" value="'.$this->item->name.'" />';
			?>
        </ul>
        <ul class="spe spe_folder">
        	<li>
            	<?php echo '<label>'.Text::_( 'COM_CCK_TYPE' ).'<span class="star"> *</span></label>'.JCckDev::getFormFromHelper( array( 'component'=>'com_cck_webservices', 'function'=>'getWebservicePlugins', 'name'=>'more_webservices_webservice_type' ), $this->item->type, $config, array( 'storage_field'=>'type' ) ); ?>
        	</li>
        </ul>
        <ul class="spe spe_state spe_third">
            <?php echo JCckDev::renderForm( 'core_state', $this->item->published, $config, array( 'label'=>'clear' ) ); ?>
        </ul>
        <ul class="spe spe_description">
            <?php echo JCckDev::renderForm( 'core_description', $this->item->description, $config, array( 'label'=>'clear', 'selectlabel'=>'Description' ) ); ?>
        </ul>
	</div>
    
	<?php
	$options	=	JCckDev::fromJSON( $this->item->options );
	?>

	<div id="layer" style="text-align: center;">
		<?php
		$type	=	( $this->item->type ) ? $this->item->type : 'http';
		$layer	=	JPATH_PLUGINS.'/cck_webservice/'.$type.'/tmpl/edit.php';
		if ( is_file( $layer ) ) {
			include_once $layer;
		}
		?>
	</div>

    <div class="seblod">
        <div class="legend top left"><?php echo Text::_( 'COM_CCK_INTEGRATION_AUTH' ); ?></div>
        <ul class="adminformlist adminformlist-2cols">
            <?php
            echo JCckDev::renderForm( 'more_webservice_auths', @$options['auth'], $config, array( 'storage_field'=>'json[options][auth]' ) );
            ?>
        </ul>
    </div>
</div>

<div class="clr"></div>
<div>
    <input type="hidden" id="task" name="task" value="" />
    <input type="hidden" id="myid" name="id" value="<?php echo @$this->item->id; ?>" />
    <?php
    echo $this->form->getInput( 'id' );
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
		ajaxLayer: function(view, layout, elem, mydata) {
			var loading = "<img align='center' src='<?php echo $ajax_load; ?>' alt='' />";  
			$.ajax({
				cache: false,
				data: mydata,
				type: "POST",
				url: "index.php?option=com_cck_webservices&view="+view+"&layout="+layout+"&format=raw",
				beforeSend:function(){ $("#loading").html(loading); $(elem).html(""); },
				success: function(response){ $("#loading").html(""); $(elem).css("opacity", 0.4).html(response).fadeTo("fast",1); },
				error:function(){ $(elem).html("<div><strong>Oops!</strong> Try to close the page & re-open it properly.</div>"); }
			});
		},
		submit: function(task) {
			Joomla.submitbutton(task);
		}
	};
	Joomla.submitbutton = function(task) {
		if (task == "<?php echo $this->vName; ?>.cancel" || $("#adminForm").validationEngine("validate",task) === true) {
			JCck.submitForm(task, document.getElementById('adminForm'));
		}
	};
	$(document).ready(function() {
		$("#type").on('change', function() {
			var cur = $("#myid").val();
			var data = "id="+cur+"&ajax_type="+$("#type").val();
			JCck.Dev.ajaxLayer("webservice", "edit2", "#layer", data);
		});
	});
})(jQuery);
</script>