<?php
/**
* @version 			SEBLOD Builder 1.x
* @package			SEBLOD Builder Add-on for SEBLOD 3.x
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
use Joomla\CMS\Router\Route;

$config	=	JCckDev::init( array( '42', 'button_free', 'button_submit', 'select_simple', 'text', 'select_dynamic' ), true );
$params	=	ComponentHelper::getParams( 'com_cck_builder' );
Helper_Include::addDependencies( $this->getName(), $this->getLayout() );
Factory::getDocument()->addStyleDeclaration( '#system-message-container.j-toggle-main.span10{width: 100%;}' );
?>

<form action="<?php echo Route::_( 'index.php?option=' . $this->option ); ?>" method="post" id="adminForm" name="adminForm">
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
        <?php echo JCckDevAccordion::start( 'cckOptions', 'collapse0', Text::_( 'COM_CCK_PANE_SEBLOD_APP' ), array( 'active'=>'collapse0', 'useCookie'=>'1' ) ); ?>
        <div class="seblod">
            <ul class="adminformlist auto-width">
                <?php
                echo JCckDev::renderForm( 'more_builder_type', '', $config );
                echo JCckDev::renderForm( 'more_builder_target', '', $config );
                // echo JCckDev::renderFormFromHelper( array( 'component'=>'com_cck', 'function'=>'getFolderSelect', 'name'=>'core_folder' ), '', $config, array( 'label'=>'Folder', 'selectlabel'=>'Select', 'storage_field'=>'folder', 'required'=>'required' ) );
                echo JCckDev::renderForm( 'more_builder_title', '', $config, array( 'css'=>'validate[custom[plugin_title]] no-placeholder' ) );
                echo '<div class="control-group">'
                 .   '<div class="control-label"><label>'.Text::_( 'COM_CCK_CONTENT_TYPE' ).'<span class="star"> *</span></label></div>'
                 .   '<div class="controls">'
                 .   JCckDev::getForm( 'more_builder_content_type', '', $config )
                 .   JCckDev::getForm( 'more_builder_title_form', '', $config, array( 'label'=>' ', 'css'=>'validate[custom[plugin_title]] no-placeholder' ) )
                 .   '</div>'
                 .   '</div>';
                 ;

				$creation_date	=	( $params->get( 'creation_date', 0 ) == 1 ) ? $params->get( 'creation_date_custom', '2012' ) : Factory::getDate()->format( $params->get( 'creation_date_format', 'F Y' ) );

				echo JCckDev::renderForm( 'more_builder_creation_date', $creation_date, $config, array(), array(), 'hidden' );
				echo JCckDev::renderForm( 'more_builder_description', $params->get( 'description', 'SEBLOD 3.x - www.seblod.com // by Octopoos - www.octopoos.com' ), $config, array(), array(), 'hidden' );
				echo JCckDev::renderForm( 'more_builder_version', '', $config, array(), array(), 'hidden' );
				echo JCckDev::renderForm( 'more_builder_submit', '', $config, array(  'storage'=>'dev', 'css'=>'btn-primary' ), array(), 'flt-right' );
				?>
            </ul>
        </div>
        <?php echo JCckDevAccordion::end(); ?>
	</div>
    <div class="clr"></div>
</div>

<div class="clr"></div>
<div>
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="task" value="" />
	<?php
	$config['validation']['plugin_name']	=	'"plugin_name":{"regex": /^[a-z0-9_]+$/,"alertText":"* '.Text::_( 'COM_CCK_PLUGIN_NAME_VALIDATION' ).'"}';
    $config['validation']['plugin_prefix']  =   '"plugin_prefix":{"regex": /^[a-z]+$/,"alertText":"* '.Text::_( 'COM_CCK_PLUGIN_PREFIX_VALIDATION' ).'"}';
    $config['validation']['plugin_title']   =   '"plugin_title":{"regex": /^[a-zA-Z0-9 \[\]]+$/,"alertText":"* '.Text::_( 'COM_CCK_PLUGIN_TITLE_VALIDATION' ).'"}';
	JCckDev::validate( $config );
    echo HTMLHelper::_( 'form.token' );
	?>
</div>

<?php
Helper_Display::quickCopyright();
?>
</div>
</form>
<style>div.seblod .adminformlist.auto-width select:not(.no-auto){width:215px;}</style>
<script type="text/javascript">
(function ($){
	JCck.Dev = {
        fields: ["name","title","title_form","title_list"],
        trigger: "type",
        createApp: function()
        {
            $("#folder").prop("disabled",false);
            Joomla.submitbutton('createApp');
        },
        setPlaceholders: function(id,init) {
            $.each(JCck.Dev.fields, function(k, v) {
                if ($("#"+v).length) {
                    $("#"+v).attr("placeholder", $("#"+id+" option:selected").attr("data-target-"+v));
                }
            });
        },
        submit: function(task) {
            Joomla.submitbutton(task);
        }
    };
	Joomla.submitbutton = function(task) {
		if ($("#adminForm").validationEngine("validate",task) === true) {
			Joomla.submitform(task, document.getElementById('adminForm'));
		}
	};
    $(document).ready(function() {
        $("#content_type").val("-1").hide();
        $("#folder").closest('.control-group').css('visibility', 'hidden');

        $("#adminForm").on("change", "#type", function() {
            $("#folder").val("");
            $("#content_type option").show();

            var id = $(this).attr("id");
            var v = $(this).val();
            var type = $("#type option:selected").attr("data-type");
            var target = $("#type option:selected").attr("data-target");
            JCck.Dev.setPlaceholders(id);
            
            if (type > 0) {
                if (type == 2) {
                    $("#folder").closest('.control-group').css('visibility', 'visible');
                    $("#target").closest('.control-group').css('visibility', 'hidden');
                } else {
                    $("#folder").closest('.control-group').css('visibility', 'hidden');
                    $("#target").closest('.control-group').css('visibility', 'visible');
                }
                $("#title_form").hide();
                $("#content_type option[value='-1']").hide();
                $("#content_type").val("").show();

                var object = $("#type option:selected").attr("data-object");
                if (object) {
                    $("#content_type option:not([data-object='"+object+"'])").hide();
                    $("#content_type option[value='']").show();
                }
            } else {
                if (type == -2) {
                    $("#folder").closest('.control-group').css('visibility', 'visible');
                    $("#target").closest('.control-group').css('visibility', 'hidden');
                } else {
                    $("#folder").closest('.control-group').css('visibility', 'hidden');
                    $("#target").closest('.control-group').css('visibility', 'visible');
                }
                $('#title_form').show();
                $("#content_type").val("-1").hide();
            }

            if (target != '') {
                $("#target").val(target);
            } else {
                $("#target").val("");
            }

            if (v == "octo_article_x_shared" || v == "octo_category_x_shared") {
                $("#folder").prop("disabled",true);
            } else {
                $("#folder").prop("disabled",false);
            }
        });

        $("#adminForm").on("change", "#content_type", function() {
            if ($("#folder").prop("disabled")) {
                $("#folder").val($("#content_type option:selected").attr("data-folder"));
            }
        });

        $("#adminForm").on("change", ".no-placeholder", function() {
            if ($(this).val() == $(this).attr("placeholder")) {
                $(this).attr("placeholder", "");
            }
        });
        JCck.Dev.setPlaceholders(JCck.Dev.trigger);
    });
})(jQuery);
</script>