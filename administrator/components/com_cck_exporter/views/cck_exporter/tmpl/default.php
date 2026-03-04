<?php
/**
* @version 			SEBLOD Exporter 1.x
* @package			SEBLOD Exporter Add-on for SEBLOD 3.x
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;

PluginHelper::importPlugin( 'cck_storage_location' );
Text::script( 'COM_CCK_CONFIRM_PURGE_OUTPUT_FOLDER' );
$app        =   Factory::getApplication();
$config	    =	JCckDev::init( array(), true );
$ajax_load  =   'components/com_cck/assets/styles/seblod/images/ajax.gif';
$params	    =	ComponentHelper::getParams( 'com_cck_exporter' );
Helper_Include::addDependencies( $this->getName(), $this->getLayout() );
Factory::getDocument()->addStyleDeclaration( 'div.seblod .adminformlist button {margin:0;} div.seblod .adminformlist-2cols li {margin:0;} #system-message-container.j-toggle-main.span10{width: 100%;}' );
?>

<form enctype="multipart/form-data" action="<?php echo Route::_( 'index.php?option=' . $this->option ); ?>" method="post" id="adminForm" name="adminForm">
<div>

<div class="<?php echo $this->css['wrapper']; ?> hidden-phone">
    <div class="<?php echo $this->css['w100']; ?>">
        <div class="seblod first cpanel_news full">
            <div class="legend top center plus"><?php echo CCK_LABEL .' &rarr; '. Text::_( 'COM_CCK_ADDON_'.CCK_NAME ); ?></div>
            <ul class="adminformlist">
                <li style="text-align:center;">
                    <?php echo Text::_( 'COM_CCK_ADDON_'.CCK_NAME.'_DESC' ); ?>
                </li>
            </ul>
            <div class="clr"></div>
        </div>
    </div>
    <div class="seblod-less cpanel_news full">
        <?php echo JCckDevAccordion::start( 'cckOptions', 'collapse0', Text::_( 'COM_CCK_PANE_TO_CSV' ), array( 'active'=>'collapse0', 'useCookie'=>'1' ) ); ?>
        <div class="seblod cck-padding-bottom-0">
            <div class="form-grid">
                <div class="control-group">
                    <div class="control-label">
                        <label><?php echo Text::_( 'COM_CCK_CONTENT_OBJECT' ); ?><span class="star"> *</span></label>
                    </div>
                    <div class="controls">
				        <?php echo JCckDev::getFormFromHelper( array( 'component'=>'com_cck_exporter', 'function'=>'getObjectPlugins', 'name'=>'more_exporter_storage_location' ), '', $config, array( 'storage_field'=>'options[storage_location]', 'css'=>'form-select' ) )
				                .  JCckDev::getForm( 'more_exporter_storage', $params->get( 'storage', 'standard' ), $config ); ?>
				    </div>
                </div>
				<?php echo JCckDev::renderForm( 'more_exporter_content_type', '', $config ); ?>
                <?php echo JCckDev::renderForm( 'more_exporter_limit', '', $config ); ?>
                <?php echo JCckDev::renderForm( 'more_exporter_prepare_output', $this->params->get( 'prepare_output', '0' ), $config ); ?>
                <?php echo JCckDev::renderForm( 'more_exporter_separator', $params->get( 'separator', ';' ), $config, array(), array(), 'w100' ); ?>
            </div>
        </div>
        <div id="layer" class="cck-padding-top-0 cck-padding-bottom-0">
            <?php /* Loaded by AJAX */ ?>
        </div>
        <div class="seblod cck-padding-top-0 cck-overflow-visible">
            <div class="form-grid">
				<?php
				$attr	=	'onclick="javascript:Joomla.submitbutton(\'exportToCsv\');"';
                ?>
                <div class="control-group">
                    <div class="controls text-center">
			            <div class="btn-group dropup">
			                <?php echo JCckDev::getForm( 'more_exporter_submit', '', $config, array( 'label'=>'Export to CSV', 'storage'=>'dev', 'attributes'=>$attr, 'css'=>'btn-primary' ) ); ?>
			                <a href="javascript:void(0);" id="featured_session" class="btn btn-primary hasTooltip hasTip" title="Remember this session"><span class="icon-unarchive"></span></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php echo JCckDevAccordion::end(); ?>
	</div>
</div>

<div class="clr"></div>
<div>
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="task" value="" />
	<?php
	JCckDev::validate( $config );
    echo HTMLHelper::_( 'form.token' );
	?>
</div>

<?php
Helper_Display::quickCopyright();
Helper_Display::quickSession( array( 'extension'=>'com_cck_exporter' ) );
?>
</div>
</form>

<script type="text/javascript">
(function ($){
    JCck.Dev = {
        token:Joomla.getOptions("csrf.token")+"=1",
		ajaxLayer: function(view, layout, elem, mydata, myopts) {
			var loading = "<img align='center' src='<?php echo $ajax_load; ?>' alt='' />";  
			$.ajax({
				cache: false,
				data: mydata,
				type: "POST",
				url: "index.php?option=com_cck_exporter&view="+view+"&layout="+layout+"&format=raw",
				beforeSend:function(){ $("#loading").html(loading); $(elem).html(""); },
				success: function(response){ $("#loading").html(""); $(elem).css("opacity", 0.4).html(response).fadeTo("fast",1); if (myopts) { JCck.Dev.setSession(myopts); } },
				error:function(){ $(elem).html("<div><strong>Oops!</strong> Try to close the page & re-open it properly.</div>"); }
			});
		},
        ajaxSession: function(opts, key, val) {
            if ( key != "" ) {
                var cur = $("#"+key).myVal();
            }
			if ( key != "" && cur != val ) {
				var data = "&ajax_type="+val;
				JCck.Dev.ajaxLayer("cck_exporter", "default2", "#layer", data, opts);
			} else {
				JCck.Dev.setSession(opts);
			}
        },
        ajaxSessionDelete: function(sid) {
            var loading = "<img align='center' src='<?php echo $ajax_load; ?>' alt='' />"; 
            $.ajax({
                cache: false,
                data: "sid="+sid+"&"+JCck.Dev.token,
                type: "POST",
                url: "index.php?option=com_cck&task=deleteSessionAjax&format=raw",
                beforeSend:function(){ $("#loading").html(loading); },
                success: function(){ $("#loading").html(""); document.location.reload(); }
            });
        },
        ajaxSessionSave: function() {
            var loading = "<img align='center' src='<?php echo $ajax_load; ?>' alt='' />"; 
            var data = {};
            var id = '';
            $("#adminForm input.text, #adminForm select.select, #adminForm fieldset.checkbox, #adminForm fieldset.radios").each(function(i) {
                id = $(this).attr("id");
                data[id] = String($(this).myVal());
            });
            var encoded = $.toJSON(data);
            var type = $("#options_storage_location").val();
            $.ajax({
                cache: false,
                data: "data="+encoded+"&extension=com_cck_exporter&folder=1&type="+type+"&"+JCck.Dev.token,
                type: "POST",
                url: "index.php?option=com_cck&task=saveSessionAjax&format=raw",
                beforeSend:function(){ $("#loading").html(loading); },
                success: function(){ $("#loading").html(""); document.location.reload(); }
            });
        },
        setSession: function(opts) {
            var data = $.evalJSON(opts);
            $.each(data, function(k, v) {
                $("#"+k).myVal(v);
                
				switch( k ) {
					case "options_prepare_output":
	                	if ( v == "1" ) {
	                       	$("label[for='options_prepare_output0']").addClass("active btn-success");
	                       	$("label[for='options_prepare_output1']").removeClass("active btn-danger");
	                       } else {
	                       	$("label[for='options_prepare_output0']").removeClass("active btn-success");
	                       	$("label[for='options_prepare_output1']").addClass("active btn-danger");
	                       }
	                	break;
                }
            });
            if (typeof JCck.Dev.applyConditionalStates === 'function') {
                JCck.Dev.applyConditionalStates();
            }
        },
        submit: function(task) {
            Joomla.submitbutton(task);
        },
        toggleOptions: function(cur,clear) {
            $('#options_content_type option').show();
            if (cur) {
                var v = "";
                $('#options_content_type option').each(function() {
                    v = $(this).attr("data-object");
                    if (v != "" && v !== undefined && v != cur) {
                        $(this).hide();
                    }
                });
            }
            if (clear && $('#options_content_type').val() != "-1") {
                $('#options_content_type').val("");
            }
        }
    };
    Joomla.submitbutton = function(task) {
        if (task == "purge") {
            if (confirm(Joomla.JText._('COM_CCK_CONFIRM_PURGE_OUTPUT_FOLDER'))) {
                Joomla.submitform(task);
            } else {
                return false;
            }
        } else if ($("#adminForm").validationEngine("validate",task) === true) {
            Joomla.submitform(task, document.getElementById('adminForm'));
        }
    };
    $(document).ready(function() {
        JCck.Dev.toggleOptions($("#options_storage_location").val(),false);

		$("#options_storage_location").on('change', function() {
            JCck.Dev.toggleOptions($(this).val(),true);
			JCck.Dev.ajaxLayer("cck_exporter", "default2", "#layer", "&ajax_type="+$(this).val());
		});
        $("#featured_session").on("click", function() {
            JCck.Dev.ajaxSessionSave();
        });
        $(".featured_sessions").on("click", function() {
            JCck.Dev.ajaxSession($(this).attr("mydata2"), "options_storage_location", $(this).attr("mydata"));
        });
        $(".featured_sessions_del").on("click", function(e) {
        	e.preventDefault();
            JCck.Dev.ajaxSessionDelete($(this).attr("mydata"));
        });
        JCck.Dev.ajaxLayer("cck_exporter", "default2", "#layer", "&ajax_type="+$("#options_storage_location").val());
    });
})(jQuery);
</script>