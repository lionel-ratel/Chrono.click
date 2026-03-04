<?php
/**
* @version 			SEBLOD Toolbox 1.x
* @package			SEBLOD Toolbox Add-on for SEBLOD 3.x
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$config     =	JCckDev::init( array( '42', 'checkbox', 'jform_menuitem', 'jform_usergroups', 'password', 'radio', 'select_dynamic', 'select_numeric', 'select_simple', 'text', 'textarea', 'wysiwyg_editor' ), true, array( 'item'=>$this->item, 'vName'=>$this->vName ) );
$cck        =	JCckDev::preload( array( 'more_toolbox_processing_title', 'more_toolbox_processing_name', 'core_description', 'core_state', 'more_toolbox_processing_type', 'more_toolbox_processing_scriptfile' ) );
Helper_Include::addDependencies( $this->getName(), $this->getLayout() );

$options    =   JCckDev::fromJSON( $this->item->options );
?>

<form action="<?php echo Route::_( 'index.php?option='.$this->option.'&view='.$this->getName().'&layout=edit&id='.(int)$this->item->id ); ?>" method="post" id="adminForm" name="adminForm" autocomplete="off">

<div class="<?php echo $this->css['wrapper']; ?>">
	<div class="<?php echo $this->css['wrapper_first']; ?>">
		<?php
		if ( $this->item->scriptfile == '' ) {
			$path_html	=	JCckDev::renderForm( $cck['more_toolbox_processing_scriptfile'], 'media/cck/processings/', $config, array( 'label'=>'Folder' ), array(), 'w100' );
		} else {
			$filename   =   basename( $this->item->scriptfile );
			$filename   =   substr( $filename, 0, strrpos( $filename, '.' ) );

			if ( $filename && $this->item->name && strpos( $this->item->scriptfile, $filename.'/'.$filename.'.php' ) !== false ) {
				$folder 	=   str_replace( $filename.'/'.$filename.'.php', '', $this->item->scriptfile );
				$path_html	=	JCckDev::renderForm( $cck['more_toolbox_processing_scriptfile'], $folder, $config, array( 'label'=>'Folder' ), array(), 'w100' );
			} else {
				$folder 	=   'media/cck/processings/';
				$path_html	=	JCckDev::renderForm( $cck['more_toolbox_processing_scriptfile'], $this->item->scriptfile, $config, array(), array(), 'w100' );
			}
		}

		$dataTmpl   =   array(
							'fields'=>array(
								'description'=>JCckDev::renderForm( $cck['core_description'], $this->item->description, $config, array( 'label'=>'clear', 'selectlabel'=>'Description' ) ),
								'folder'=>JCckDev::renderFormFromHelper( array( 'component'=>'com_cck', 'function'=>'getFolder', 'name'=>'core_folder' ), $this->item->folder, $config, array( 'label'=>'App Folder', 'storage_field'=>'folder' ) ),
								'name'=>JCckDev::renderForm( $cck['more_toolbox_processing_name'], $this->item->name, $config ),
								'path'=>$path_html,
								'state'=>JCckDev::renderForm( $cck['core_state'], $this->item->published, $config, array( 'label'=>'Status' ) ),
								'title'=>JCckDev::renderForm( $cck['more_toolbox_processing_title'], $this->item->title, $config ),
								'type'=>JCckDev::renderForm( $cck['more_toolbox_processing_type'], $this->item->type, $config )
							),
							'item'=>$this->item,
							'params'=>array()
						);

		echo JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.admin.processing.edit_main', $dataTmpl );
		?>
	</div>

	<div class="main-card">
		<?php
		if ( JCck::on( '4.0' ) ) {
			echo HTMLHelper::_( 'uitab.startTabSet', 'myTab', ['active' => 'details', 'recall' => true, 'breakpoint' => 768] );
			echo HTMLHelper::_( 'uitab.addTab', 'myTab', 'details', Text::_( 'COM_CCK_DETAILS' ) );

			if ( $this->item->id ) { ?>
			<div id="layer" class="cck-padding-top-0 cck-padding-bottom-0">
				<?php
				$layer  =   JPATH_SITE.'/'.$folder.$this->item->name.'/tmpl/edit.php';
				
				if ( file_exists( $layer ) ) {
					include_once $layer;
				}
				?>
			</div>
			<?php }

			echo HTMLHelper::_( 'uitab.endTab' );
			echo HTMLHelper::_( 'uitab.addTab', 'myTab', 'publishing', Text::_( 'COM_CCK_INPUT' ) );
			echo JCckDev::renderForm( 'core_dev_bool', @$options['input'], $config, array( 'label'=>'Mode', 'defaultvalue'=>'0', 'options'=>'Core=0||Standalone=1', 'storage_field'=>'json[options][input]' ) );
			echo JCckDev::renderForm( 'core_dev_select', @$options['input_cid'], $config, array( 'label'=>'Ids', 'defaultvalue'=>'int', 'selectlabel'=>'', 'options'=>'Int=int||String=string', 'storage_field'=>'json[options][input_cid]' ) );
			echo JCckDev::renderForm( 'core_dev_text', @$options['ajax_count'], $config, array( 'label'=>'Config Mode Ajax Count', 'defaultvalue'=>'', 'selectlabel'=>'', 'storage_field'=>'json[options][ajax_count]' ) );

			echo HTMLHelper::_( 'uitab.endTab' );
			echo HTMLHelper::_( 'uitab.addTab', 'myTab', 'publishing', Text::_( 'COM_CCK_OUTPUT' ) );
			echo JCckDev::renderForm( 'more_toolbox_processing_output', @$options['output'], $config, array( 'defaultvalue'=>'', 'storage_field'=>'json[options][output]' ) );
			echo JCckDev::renderForm( 'more_toolbox_processing_output_path', @$options['output_path'], $config, array( 'defaultvalue'=>'tmp/', 'storage_field'=>'json[options][output_path]' ) );
			echo JCckDev::renderForm( 'more_toolbox_processing_output_extension', @$options['output_extension'], $config, array( 'storage_field'=>'json[options][output_extension]' ) );
			echo JCckDev::renderForm( 'more_toolbox_processing_output_filename_date', @$options['output_filename_date'], $config, array( 'defaultvalue'=>'', 'storage_field'=>'json[options][output_filename_date]' ) );
            echo JCckDev::renderForm( 'core_dev_text', @$options['message_error'], $config, array( 'label'=>'Message Error', 'defaultvalue'=>'', 'storage_field'=>'json[options][message_error]' ) );

			echo HTMLHelper::_( 'uitab.endTab' );
			echo HTMLHelper::_( 'uitab.addTab', 'myTab', 'publishing', Text::_( 'COM_CCK_PUBLISHING' ) );
			echo JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.admin.processing.edit_publishing', $dataTmpl );
			echo HTMLHelper::_( 'uitab.endTab' );
			echo HTMLHelper::_( 'uitab.endTabSet' );
		}
		?>
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
		if (task == "processing.cancel" || $("#adminForm").validationEngine("validate",task) === true) {
			JCck.submitForm(task, document.getElementById('adminForm'));
		}
	};
	$(document).ready(function() {
		$("#json_options_output_path,#json_options_output_extension,#json_options_output_filename_date").isVisibleWhen("json_options_output","0,1,2");
	});
})(jQuery);
</script>