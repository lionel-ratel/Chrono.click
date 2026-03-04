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

$config     =	JCckDev::init( array(), true, array( 'item' => $this->item, 'vName'=>$this->vName ) );
$ajax_load  =   'components/com_cck/assets/styles/seblod/images/ajax.gif';
Helper_Include::addDependencies( $this->getName(), $this->getLayout() );
Factory::getDocument()->addStyleDeclaration( '.cck-fl{float:left;}' );
?>

<form action="<?php echo Route::_( 'index.php?option='.$this->option.'&view='.$this->getName().'&layout=edit&id='.(int)$this->item->id ); ?>" method="post" id="adminForm" name="adminForm">

<div class="<?php echo $this->css['wrapper']; ?>">
	<div class="seblod first">       
		<div id="loading" class="loading"></div>
		<ul class="spe spe_title">
			<?php
			echo JCckDev::renderForm( 'more_webservices_resource_title', $this->item->title, $config );
			?>
		</ul>
		<ul class="spe spe_folder">
			<?php echo JCckDev::renderFormFromHelper( array( 'component'=>'com_cck', 'function'=>'getFolder', 'name'=>'core_folder' ), $this->item->folder, $config, array( 'label'=>'App Folder', 'storage_field'=>'folder' ) ); ?>
		</ul>
		<ul class="spe spe_state">
			<?php echo JCckDev::renderForm( 'core_state', $this->item->published, $config, array( 'label'=>'clear' ) ); ?>
		</ul>
		<ul class="spe spe_name adminformlist">
			<?php echo JCckDev::renderForm( 'more_webservices_resource_name', $this->item->name, $config ); ?>
		</ul>
		<ul class="spe spe_type">
			<?php echo JCckDev::renderForm( 'more_webservices_resource_type', $this->item->type, $config ); ?>
		</ul>
		<!--<ul class="spe spe_state spe_latest">
			<li>
				<span class="variation_value"><strong><?php echo Text::_( 'COM_CCK_'.JCckWebservice::getConfig_Param( 'resources_format', 'json' ) ); ?></strong></span>
			</li>
		</ul>-->
		
		<ul class="spe spe_description spe_latest">
			<?php echo JCckDev::renderForm( 'core_description', $this->item->description, $config, array( 'label'=>'clear', 'selectlabel'=>'Description' ) ); ?>
		</ul>
		<ul class="spe spe_double">
			<?php echo JCckDev::renderForm( 'more_webservices_resource_methods', $this->item->methods, $config ); ?>
		</ul>
	</div>

	<div class="seblod">
		<div class="legend top left"><?php echo Text::_( 'COM_CCK_SETTINGS' ); ?></div>
		<ul class="adminformlist adminformlist-2cols">
			<?php
			echo JCckDev::renderForm( 'core_task_processing', @$this->item->options['processing'], $config, array( 'storage_field'=>'json[options][processing]' ) );
			echo JCckDev::renderForm( 'core_dev_text', @$this->item->options['field_name'], $config, array( 'storage_field'=>'json[options][field_name]' ) );
			echo JCckDev::renderForm( 'more_webservices_resource_content_type', @$this->item->options['content_type'], $config, array( 'css'=>'max-width-180' ) );
			// echo JCckDev::renderBlank( '<input type="hidden" id="blank_li" value="" />' );
			echo JCckDev::renderForm( 'more_webservices_resource_hateoas', @$this->item->options['hateoas'], $config );
			echo JCckDev::renderForm( 'more_webservices_resource_content_type', @$this->item->options['content_types'], $config, array( 'label'=>'Alt Content Type', 'type'=>'textarea', 'cols'=>84, 'rows'=>1, 'storage_field'=>'json[options][content_types]', 'required'=>'' ), array(), 'w100' );
            echo JCckDev::renderForm( 'core_dev_text', @$this->item->options['content_table'], $config, array( 'label'=>'Table', 'storage_field'=>'json[options][content_table]', 'required'=>'required' ) );
            echo JCckDev::renderForm( 'core_dev_bool', @$this->item->options['debug'], $config, array( 'label'=>'Debug', 'defaultvalue'=>'0', 'storage_field'=>'json[options][debug]' ) );
			?>
		</ul>
	</div>

	<div class="seblod" id="layer">
		<div class="legend top left"><?php echo Text::_( 'COM_CCK_INPUT' ).' '.Text::_( 'COM_CCK_INPUT_API_SUPPORT' ); ?></div>
		<ul class="adminformlist adminformlist-2cols">
			<?php
			if ( isset( $this->item->options['input'] ) && count( $this->item->options['input'] ) ) {
				$opt_fields     =   $this->item->options['input'];
				$opt_options    =   array_values( $this->item->options['input'] );
			} else {
				$opt_fields     =   array();
				$opt_options    =   array();
			}

			echo JCckDev::renderForm( 'core_dev_text', @$this->item->options['property'], $config, array( 'label'=>'Root Key', 'storage_field'=>'json[options][property]' ) );
			echo JCckDev::renderBlank();

			JCckDev::initScript( 'field', $this->item, array(
															'hasOptions'=>true,
															'customAttr'=>array(
																			array(
																				'id'=>'required',
																				'label'=>Text::_( 'COM_CCK_VALIDATION' ),
																				'form'=>array( 'options'=>'<option value="0">'.Text::_( 'COM_CCK_OPTIONAL' ).'</option><option value="1">'.Text::_( 'COM_CCK_REQUIRED' ).'</option><option value="-1">'.Text::_( 'COM_CCK_UNIQUE_ID' ).'</option>' )
																			),
																			array(
																				'id'=>'type',
																				'label'=>Text::_( 'COM_CCK_INPUT_TYPE' ),
																				'form'=>array( 'options'=>'<option value="string">'.Text::_( 'COM_CCK_STRING' ).'</option><optgroup label="'.Text::_( 'COM_CCK_CUSTOM' ).'"><option value="raw">'.Text::_( 'COM_CCK_RAW' ).'</option><option value="unset">'.Text::_( 'COM_CCK_UNSET' ).'</option></optgroup>' ), /* <option value="integer">'.Text::_( 'COM_CCK_INTEGER' ).'</option> */
																				'default'=>'string'
																			),
																			array(
																				'id'=>'property',
																				'label'=>Text::_( 'COM_CCK_PROPERTY_NAME' ),
																				'placeholder'=>Text::_( 'COM_CCK_INHERITED' )
																			),
																			array(
																				'id'=>'value',
																				'label'=>Text::_( 'COM_CCK_VALUE' ),
																				'placeholder'=>Text::_( 'COM_CCK_INPUT' ),
																				'size'=>'16'
																			),
																			array(
																				'id'=>'value_mode',
																				'label'=>Text::_( 'COM_CCK_VALUE_MODE' ),
																				'form'=>array( 'options'=>'<option value="0">'.Text::_( 'COM_CCK_ALWAYS' ).'</option><option value="1">'.Text::_( 'COM_COM_SET_IF_UNSET' ).'</option>' ),
																				'default'=>'0'
																			)
																		),
															'fieldPicker'=>true,
															'options'=>$opt_options,
															'parent'=>'json[options][input]',
															'toggleAttr'=>false,
															'root'=>'api_input'
														) );

			echo JCckDev::renderForm( 'core_options', array_keys( $opt_fields ), $config, array( 'label'=>'Properties', 'rows'=>0, 'storage_field'=>'json[options][input]', 'name'=>'api_input' ), array( 'after'=>@$this->item->init['fieldPicker'] ), 'w100 inline-x' );
			?>
		</ul>
	</div>

	<div class="seblod method-get" id="layer">
		<div class="legend top left"><?php echo Text::_( 'COM_CCK_OUTPUT' ); ?></div>
		<ul class="adminformlist adminformlist-2cols">
			<?php
			
			echo '<li><label>'.Text::_( 'COM_CCK_LIMITS_DEFAULT_MAX' ).'</label>'
			 .   JCckDev::getForm( 'core_limit', @$this->item->options['limit'], $config, array( 'defaultvalue'=>'', 'size'=>'3', 'attributes'=>'style="text-align:center;"', 'storage_field'=>'json[options][limit]' ) )
			 .   JCckDev::getForm( 'core_limit', @$this->item->options['limit_max'], $config, array( 'defaultvalue'=>'', 'size'=>'3', 'attributes'=>'style="text-align:center;"', 'storage_field'=>'json[options][limit_max]' ) )
			 .   '</li>';
			echo JCckDev::renderForm( 'more_webservices_resource_hateoas_pagination', @$this->item->options['hateoas_pagination'], $config );
			echo JCckDev::renderForm( 'more_webservices_resource_ordering', @$this->item->options['ordering'], $config );
			echo JCckDev::renderForm( 'core_dev_bool', @$this->item->options['group_by'], $config, array( 'label'=>'Group By', 'defaultvalue'=>'0', 'storage_field'=>'json[options][group_by]' ) );
			echo JCckDev::renderForm( 'core_dev_text', @$this->item->options['order_by'], $config, array( 'label'=>'Order', 'storage_field'=>'json[options][order_by]' ) );
			echo JCckDev::renderForm( 'core_dev_textarea', @$this->item->options['output_keys'], $config, array( 'label'=>'Properties Ungroup', 'rows'=>1, 'cols'=>84, 'css'=>'input-xxlarge', 'storage_field'=>'json[options][output_keys]' ), array(), 'w100' );
			
			if ( isset( $this->item->options['output'] ) && count( $this->item->options['output'] ) ) {
				$opt_fields     =   $this->item->options['output'];
				$opt_options    =   array_values( $this->item->options['output'] );
			} else {
				$opt_fields     =   array();
				$opt_options    =   array();
			}
			
			JCckDev::initScript( 'field', $this->item, array(
															'hasOptions'=>true,
															'customAttr'=>array(
																			array(
																				'id'=>'view',
																				'label'=>Text::_( 'COM_CCK_DISPLAY' ),
																				'form'=>array( 'options'=>'<option value="0">'.Text::_( 'COM_CCK_ALWAYS' ).'</option><option value="1">'.Text::_( 'COM_CCK_ONLY_ON_RESOURCE_ID' ).'</option><option value="2">'.Text::_( 'COM_CCK_ONLY_ON_RESOURCE_ID_RELATION' ).'</option>' ),
																				'default'=>'0'
																			),
																			array(
																				'id'=>'output',
																				'label'=>Text::_( 'COM_CCK_OUTPUT' ),
																				'form'=>array( 'options'=>'<option value="">'.Text::_( 'COM_CCK_INHERITED_SL' ).'</option><option value="1">'.Text::_( 'COM_CCK_PREPARED' ).'</option><option value="0">'.Text::_( 'COM_CCK_RAW' ).'</option>' ),
																				'default'=>''
																			),
																			array(
																				'id'=>'property',
																				'label'=>Text::_( 'COM_CCK_PROPERTY_NAME' ),
																				'placeholder'=>Text::_( 'COM_CCK_INHERITED' )
																			)
																		),
															'fieldPicker'=>true,
															'options'=>$opt_options,
															'parent'=>'json[options][output]',
															'toggleAttr'=>false/*,
															'root'=>'core_options'*/
														) );

			echo JCckDev::renderForm( 'core_options', array_keys( $opt_fields ), $config, array( 'label'=>'Properties', 'rows'=>0, 'storage_field'=>'json[options][output]' ), array( 'after'=>@$this->item->init['fieldPicker'] ), 'w100 inline-x' );
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
		submit: function(task) {
			Joomla.submitbutton(task);
		},
		togglePane: function(v) {
			if (v.indexOf('GET') != -1) {
				$('.method-get').show();
			} else {
				$('.method-get').hide();
			}
		}
	};
	Joomla.submitbutton = function(task) {
		if (task == "<?php echo $this->vName; ?>.cancel" || $("#adminForm").validationEngine("validate",task) === true) {
			JCck.submitForm(task, document.getElementById('adminForm'));
		}
	};
	$(document).ready(function() {
		$('#json_options_content_type').isVisibleWhen('type','content_type,content_type_standalone,relationship');
		$('#json_options_content_types').isVisibleWhen('type','content_type,relationship');
		$('#json_options_content_table').isVisibleWhen('type','content_type_standalone');
		$('#json_options_limit,#json_options_ordering,#sortable_core_options,#sortable_api_input,#json_options_hateoas,#json_options_hateoas_pagination').isVisibleWhen('type','content_type,content_type_standalone,relationship');
		$('#json_options_group_by').isVisibleWhen('type','relationship');
		// $('#blank_li').isVisibleWhen('type','relationship');
		$('#json_options_processing').isVisibleWhen('type','processing');
		$('#json_options_field_name').isVisibleWhen('type','field');

		$("#adminForm").on("change", "#methods", function() {
			JCck.Dev.togglePane($(this).myVal());
		});

		JCck.Dev.togglePane($("#methods").myVal());
	});
})(jQuery);
</script>

<?php
/*
Get some dev. fields packaged as well..
JCckDev::renderForm( 'more_webservice'
JCckDev::renderForm( 'more_webservice_call'
JCckDev::renderForm( 'more_webservices_resource_format'
JCckDev::renderForm( 'more_webservices_resource_output'
JCckDev::renderForm( 'more_webservices_request_format'
*/
?>