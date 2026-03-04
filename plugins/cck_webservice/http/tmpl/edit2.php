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
		<div class="legend top left"><?php echo '&rArr; ' . Text::_( 'COM_CCK_REQUEST' ); ?></div>
		<ul class="adminformlist adminformlist-2cols">
			<?php
			echo JCckDev::renderForm( 'core_dev_text', $this->item->request, $config, array( 'size'=>'50', 'storage_field'=>'request' ) );
			echo JcckDev::renderForm( 'more_webservices_request_format', $this->item->request_format, $config );
			?>
		</ul>
	</div>
	<div class="seblod">
		<div class="legend top left"><?php echo '&rArr; ' . Text::_( 'COM_CCK_PARAMETERS' ); ?></div>
		<ul class="adminformlist adminformlist-2cols">
			<?php
			$i			=	0;
			$options	=	array();

			if ( $this->item->request_options !== '' ) {
				$options	=	json_decode( $this->item->request_options, true );
			}
			if ( count( $options ) ) {
				foreach ( $options as $k=>$v ) {
					$input	=	'text';
					$type	=	'';
					$value	=	'';
					if ( isset( $options[$k] ) ) {
						$type	=	$options[$k]['type'];
						$value	=	$options[$k]['value'];
						if ( strpos( $type, 'value_' ) !== false ) {
							$input	=	substr( $type, 6 );
						}
					}
					echo '<li class="w100">'.JCckDev::getForm( 'core_dev_text', $k, $config, array( 'css'=>'params', 'storage_field'=>'options_'.$k, 'size'=>16 ) ).'<span class="variation_value" style="margin-right:32px;">=</span>'
					 .	 JCckDev::getForm( 'more_webservice_parameter_type', $type, $config, array( 'storage_field'=>'json[request_options]['.$k.'][type]' ) )
					 .	 JCckDev::getForm( 'core_dev_'.$input, $value, $config, array( 'maxlength'=>0, 'cols'=>88, 'rows'=>6, 'storage_field'=>'json[request_options]['.$k.'][value]' ) )
					 .	 '<span class="fx icon-minus"></span>'
					 .	 '</li>';
					$i++;
				}
				$n	=	$i + 3;
			} else {
				$n	=	3;
			}
			for ( ; $i < $n; $i++ ) {
				$input	=	'text';
				$type	=	'';
				$value	=	'';
				$k		=	'call_request_option'.$i;
				echo '<li class="w100">'.JCckDev::getForm( 'core_dev_text', '', $config, array( 'css'=>'params', 'storage_field'=>'options_'.$k, 'size'=>16 ) ).'<span class="variation_value" style="margin-right:32px;">=</span>'
				 .	 JCckDev::getForm( 'more_webservice_parameter_type', $type, $config, array( 'storage_field'=>'json[request_options]['.$k.'][type]' ) )
				 .	 JCckDev::getForm( 'core_dev_'.$input, $value, $config, array( 'maxlength'=>0, 'cols'=>88, 'rows'=>6, 'storage_field'=>'json[request_options]['.$k.'][value]' ) )
				 .	 '<span class="fx icon-minus"></span>'
				 .	 '</li>';
			}
			?>
		</ul>
	</div>
	<div class="seblod">
		<div class="legend top left"><?php echo '&rArr; ' . Text::_( 'COM_CCK_CALL_RESPONSE' ); ?></div>
		<ul class="adminformlist adminformlist-2cols">
			<?php
			echo JcckDev::renderForm( 'more_webservices_response_format', $this->item->response_format, $config );
			echo JcckDev::renderForm( 'more_webservices_response_string', $this->item->response, $config );
			echo JCckDev::renderBlank();
			echo JcckDev::renderForm( 'more_webservices_response_string', $this->item->response_identifier, $config, array( 'label'=>'Identifier Key', 'attributes'=>'placeholder="'.Text::_( 'COM_CCK_INCREMENT' ).'"', 'storage_field'=>'response_identifier' ) );

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
});
</script>