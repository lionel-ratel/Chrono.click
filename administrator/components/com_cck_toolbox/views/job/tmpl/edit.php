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

if ( JCck::is( '4' ) ) {
	$plg_user   =   array( '42', 'field_x', 'radio', 'select_dynamic', 'select_simple', 'text', 'wysiwyg_editor' );
} else {
	$plg_user   =   array( '42', 'field_x', 'jform_user', 'radio', 'select_dynamic', 'select_simple', 'text', 'wysiwyg_editor' );
}

$config =   JCckDev::init( $plg_user, true, array( 'item'=>$this->item, 'vName'=>$this->vName ) );
$cck	=	JCckDev::preload( array( 'more_toolbox_job_title', 'more_toolbox_job_name', 'core_description', 'core_state', 'more_toolbox_job_type' ) );
Helper_Include::addDependencies( $this->getName(), $this->getLayout() );

if ( JCck::is( '4' ) ) {
	$form_user  =   JCckDev::getForm( 'core_dev_text', $this->item->run_as, $config, array( 'defaultvalue'=>'0', 'size'=>'6', 'storage_field'=>'run_as' ) );
} else {
	$form_user  =   JCckDev::getForm( 'core_author', $this->item->run_as, $config, array( 'defaultvalue'=>'0', 'storage_field'=>'run_as' ) );
}

$options_url    =   JCckDatabase::loadColumn( 'SELECT CONCAT(title,"=",id) FROM #__cck_core_sites WHERE published = 1');

if ( !is_array( $options_url ) ) {
	$options_url    =   array();
}
$options_url    =   'Custom=-1||Site=optgroup||'.implode( '||', $options_url );
?>

<form action="<?php echo Route::_( 'index.php?option='.$this->option.'&view='.$this->getName().'&layout=edit&id='.(int)$this->item->id ); ?>" method="post" id="adminForm" name="adminForm">

<div class="<?php echo $this->css['wrapper']; ?>">
	<div class="<?php echo $this->css['wrapper_first']; ?>">
		<?php
		$dataTmpl	=	array(
							'fields'=>array(
								'description'=>JCckDev::renderForm( $cck['core_description'], $this->item->description, $config, array( 'label'=>'clear', 'selectlabel'=>'Description' ) ),
								'folder'=>JCckDev::renderFormFromHelper( array( 'component'=>'com_cck', 'function'=>'getFolder', 'name'=>'core_folder' ), $this->item->folder, $config, array( 'label'=>'App Folder', 'storage_field'=>'folder' ) ),
								'name'=>JCckDev::renderForm( $cck['more_toolbox_job_name'], $this->item->name, $config ),
								'state'=>JCckDev::renderForm( $cck['core_state'], $this->item->published, $config, array( 'label'=>'Status' ) ),
								'title'=>JCckDev::renderForm( $cck['more_toolbox_job_title'], $this->item->title, $config ),
								'type'=>JCckDev::renderForm( $cck['more_toolbox_job_type'], $this->item->type, $config, array( 'required'=>'required' ) )
							),
							'item'=>$this->item,
							'params'=>array()
						);

		echo JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.admin.job.edit_main', $dataTmpl );
		?>
	</div>

	<div class="main-card">
		<?php
		if ( JCck::on( '4.0' ) ) {
			echo HTMLHelper::_( 'uitab.startTabSet', 'myTab', ['active' => 'details', 'recall' => true, 'breakpoint' => 768] );
			echo HTMLHelper::_( 'uitab.addTab', 'myTab', 'details', Text::_( 'COM_CCK_DETAILS' ) );

			echo JCckDev::renderForm( 'core_options', $this->item->processings, $config, array( 'rows'=>'1', 'label'=>'Toolbox Processings', 'storage_field'=>'processings' ) );
			echo JCckDev::renderLayoutFile(
				'cck'.JCck::v().'.form.field', array(
					'label'=>Text::_( 'COM_CCK_RUN_AS' ),
					'html'=>JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.grid', array(
						'grid'=>'33%|20%|',
						'html'=>array(
							JCckDev::getForm( 'core_dev_select', ( $this->item->run_as ? '1' : '' ), $config, array( 'selectlabel'=>'Inherited', 'options'=>'User=1', 'storage_field'=>'run_as_mode' ) ),
							$form_user
						)
					) )
				)
			);		
			echo JCckDev::renderLayoutFile(
				'cck'.JCck::v().'.form.field', array(
					'label'=>Text::_( 'COM_CCK_URL' ),
					'html'=>JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.grid', array(
						'grid'=>'33%|',
						'html'=>array(
							JCckDev::getForm( 'core_dev_select', $this->item->run_url, $config, array( 'selectlabel'=>'None', 'options'=>$options_url, 'bool8'=>0, 'storage_field'=>'run_url' ) ),
							JCckDev::getForm( 'core_dev_text', $this->item->run_url_custom, $config, array( 'defaultvalue'=>'', 'storage_field'=>'run_url_custom' ) )
						)
					) )
				)
			);
			
			echo HTMLHelper::_( 'uitab.endTab' );
			echo HTMLHelper::_( 'uitab.addTab', 'myTab', 'publishing', Text::_( 'COM_CCK_PUBLISHING' ) );
			echo JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.admin.job.edit_publishing', $dataTmpl );
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
		if (task == "job.cancel" || $("#adminForm").validationEngine("validate",task) === true) {
			JCck.submitForm(task, document.getElementById('adminForm'));
		}
	};
	$(document).ready(function() {
		$('#run_as_id').isVisibleWhen('run_as_mode','1');
		$('#run_as').isVisibleWhen('run_as_mode','1',false);
		$('#run_url_custom').isVisibleWhen('run_url','-1',false);
	});
})(jQuery);
</script>