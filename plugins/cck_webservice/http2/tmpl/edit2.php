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
use Joomla\CMS\Language\Text;

Factory::getDocument()->addStyleDeclaration( '.fx.icon-minus{color: #e9594d; cursor:pointer; position:relative; top:12px;}' );

if ( !$this->isNew ) { ?>
	<div class="seblod">
		<div class="legend top left"><?php echo Text::_( 'COM_CCK_REQUEST' ); ?></div>
		<ul class="adminformlist adminformlist-1col">
			<?php
			echo '<li>'
				. JCckDev::getForm( 'core_method', $this->item->request_method, $config, array( 'options'=>'DELETE=delete||GET=get||PATCH=patch||POST=post||PUT=put', 'storage_field'=>'request_method' ) )
				. JCckDev::getForm( 'core_dev_text', $this->item->request, $config, array( 'size'=>'50', 'storage_field'=>'request' ) )
				. JcckDev::getForm( 'more_webservices_request_format', $this->item->request_format, $config )
				. '</li>';
			?>
		</ul>
	</div>
	<div class="seblod">
		<div class="legend top left"><?php echo '&rArr; ' . Text::_( 'COM_CCK_INPUT' ); ?></div>
		<ul class="adminformlist adminformlist-2cols">
            <?php
            if ( isset( $this->item->options['input'] ) && count( $this->item->options['input'] ) ) {
                $opt_fields     =   $this->item->options['input'];
                $opt_options    =   array_values( $this->item->options['input'] );
            } else {
                $opt_fields     =   array();
                $opt_options    =   array();
            }


            JCckDev::initScript( 'field', $this->item, array(
                                                            'hasOptions'=>true,
                                                            'customAttr'=>array(
                                                                            array(
                                                                                'id'=>'mode',
                                                                                'label'=>Text::_( 'COM_CCK_MODE' ),
                                                                                'form'=>array( 'options'=>'<option value="field">'.Text::_( 'COM_CCK_PARAMETER_TYPE_DYNAMIC' ).'</option><option value="text">'.Text::_( 'COM_CCK_PARAMETER_TYPE_STATIC' ).'</option>' )
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
                                                                                'size'=>'24'
                                                                            )
                                                                        ),
                                                            'fieldPicker'=>-1,
                                                            'options'=>$opt_options,
                                                            'parent'=>'json[options][input]',
                                                            'toggleAttr'=>false,
                                                            'root'=>'api_input'
                                                        ) );

            echo JCckDev::renderForm( 'core_options', array_keys( $opt_fields ), $config, array( 'label'=>'Properties', 'rows'=>0, 'storage_field'=>'json[options][input]', 'name'=>'api_input' ), array( 'after'=>@$this->item->init['fieldPicker'] ), 'w100 inline-x' );
            ?>
        </ul>
	</div>
	<div class="seblod">
		<div class="legend top left"><?php echo Text::_( 'COM_CCK_CALL_RESPONSE' ); ?></div>
		<ul class="adminformlist adminformlist-2cols">
			<?php
			echo JcckDev::renderForm( 'more_webservices_response_format', $this->item->response_format, $config );
			echo JcckDev::renderForm( 'more_webservices_response_string', $this->item->response, $config );
			echo JCckDev::renderForm( 'core_dev_select', $this->item->response_check, $config, array( 'label'=>'Validity Check', 'selectlabel'=>'', 'defaultvalue'=>'0', 'options'=>'Validity Check Basic=1||Validity Check Standard=0||Validity Check Identifier=2', 'storage_field'=>'response_check' ) );
			echo JcckDev::renderForm( 'more_webservices_response_string', $this->item->response_identifier, $config, array( 'label'=>'Identifier Key', 'storage_field'=>'response_identifier' ) );

			echo JCckDev::renderFormFromHelper( array( 'component'=>'com_cck', 'function'=>'getMediaExtensions', 'name'=>'core_options_media_extensions' ), @$options2['media_extensions'], $config, array( 'label'=>'Media Types', 'storage_field'=>'json[options][media_extensions]' ) );
			echo JCckDev::renderForm( 'core_dev_select', @$this->item->options['media_filename'], $config, array( 'label'=>'Output', 'defaultvalue'=>'0', 'selectlabel'=>'', 'options'=>'Raw=0||Process=optgroup||Base64 Decode=1', 'storage_field'=>'json[options][media_filename]' ) );
			?>
		</ul>
	</div>
<?php } ?>

<script type="text/javascript">
jQuery(document).ready(function($) {
	$("div#layer").on("change", ".params", function() {
		var cur = $(this).attr("id");
		var val = $(this).myVal();
		$("#"+cur).attr("name","options_"+val);
		$("#json_request_"+cur+"_type").attr("name","json[request_options]["+val+"][type]");
		$("#json_request_"+cur+"_value").attr("name","json[request_options]["+val+"][value]");
	});
	$("div#layer").on("click", ".fx.icon-minus", function() {
		$(this).parent().remove();
	});
	$("#json_options_media_extensions,#json_options_media_filename").isVisibleWhen("response_format","file,image");
	$("#response_check").isVisibleWhen("response_format","json,image");
});
</script>