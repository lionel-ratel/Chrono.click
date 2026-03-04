<?php
/**
* @version 			SEBLOD 3.x More
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

$type						=	'live';
$options					=	array( 'js'=>array() );
$options['js']['load']		=	'var eid = "'.$this->item->id.'";
								var elem = "'.$this->item->id.'_'.$type.'_options";
								var encoded = parent.jQuery("#"+elem).val();
								var data = ( encoded !== undefined && encoded != "" ) ? $.evalJSON(encoded) : "";
								if (data) {
									var j = 0;
									if (typeof data === "object") {
										if (data.conditions) {
											$.each(data.conditions, function(k, v) {
												if (typeof v === "object") {
													var p = "'.$type.'";
													var $clone = $("#'.$type.'_id").parent().clone().addClass("new").appendTo(".target");
													$("li.new > *").attr("id",p+j).myVal(v.trigger).parent().removeClass("new");
													var $clone = $("#'.$type.'_options_id").parent().clone().addClass("new").appendTo(".target");
													$("li.new > *").attr("id",p+j+"_options").myVal($.toJSON(v.options)).parent().removeClass("new");
													var $clone = $("#'.$type.'_restriction_id").parent().clone().addClass("new").appendTo(".target");
													$("li.new > *").attr("id",p+j+"_restriction").myVal(v.restriction).parent().removeClass("new");
													var $clone = $("#'.$type.'_restriction_options_id").parent().clone().addClass("new").appendTo(".target");
													$("li.new > *").attr("id",p+j+"_restriction_options").myVal($.toJSON(v.restriction_options)).parent().removeClass("new");
												}
											});
										}
										if (data.mode) {
											$("#mode").myVal(data.mode);
										}
										if (data.modifier) {
											$("#modifier").myVal(data.modifier);
										}
									}
								}';
$options['js']['submit']	=	'if ( $("#adminForm").validationEngine("validate") === true ) {
									var eid = "'.$this->item->id.'";
									var elem = "'.$this->item->id.'_'.$type.'_options";
									var data = {};
									var excluded = [];
									data.mode = $("#mode").myVal();
									data.modifier = $("#modifier").myVal();
									data.conditions = [];
									$(".'.$type.'s").each(function(i) {
										var v = $(this).myVal();
										if (v != "") {
											var d = {};
											d.trigger = v;
											var enc = $(".'.$type.'s_options:eq("+i+")").myVal();
											if (enc == "") {
												enc = "{}";
											}
											d.options = $.evalJSON(enc);
											d.restriction = $(".'.$type.'s_restriction:eq("+i+")").myVal();
											var enc = $(".'.$type.'s_restriction_options:eq("+i+")").myVal();
											if (enc == "") {
												enc = "{}";
											}
											d.restriction_options = $.evalJSON(enc);
											data.conditions[i] = d;
										}
									});
									var encoded = $.toJSON(data);
									parent.jQuery("#"+elem).val(encoded);
									this.close();
									return;
								}';
JCckDev::initScript( 'live', $this->item, $options );

require_once JPATH_COMPONENT.'/helpers/helper_admin.php';

$lives	=	array_merge(
				array( HTMLHelper::_( 'select.option', '', Text::_( 'COM_CCK_NONE' ) ), HTMLHelper::_( 'select.option', 'default', Text::_( 'COM_CCK_STRING' ) ) ),
				Helper_Admin::getPluginOptions( 'field_live', 'cck_', false, false, true )
			);

if ( count( $lives ) ) {
	foreach ( $lives as $k=>$v ) {
		if ( $v->value == 'x' ) {
			unset( $lives[$k] );
		}
	}
}
$html			=	HTMLHelper::_( 'select.genericlist', $lives, 'live', 'class="lives"', 'value', 'text', '', 'live_id' );

$restrictions	=	array_merge(
						array( HTMLHelper::_( 'select.option', '', Text::_( 'COM_CCK_NONE' ) ) ),
						Helper_Admin::getPluginOptions( 'field_restriction', 'cck_', false, false, true )
					);
$html2			=	HTMLHelper::_( 'select.genericlist', $restrictions, 'restriction', 'class="lives_restriction"', 'value', 'text', '', 'live_restriction_id' );
?>
<div class="seblod">
	<?php echo JCckDev::renderLegend( Text::_( 'COM_CCK_CONSTRUCTION' ), Text::_( 'PLG_CCK_FIELD_LIVE_'.$this->item->name.'_DESC' ) ); ?>
	<ul class="adminformlist adminformlist-2cols">
		<?php
		echo JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Behavior', 'selectlabel'=>'', 'defaultvalue'=>'0', 'options'=>'Apply First Not Empty=0||Concatenate=1', 'storage_field'=>'mode' ) );
		echo JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Modifier', 'selectlabel'=>'None', 'defaultvalue'=>'', 'options'=>'Base64=base64', 'storage_field'=>'modifier' ) );
		?>
	</ul>
	<?php echo JCckDev::renderLegend( Text::_( 'COM_CCK_CONSTRUCTION' ), Text::_( 'PLG_CCK_FIELD_LIVE_'.$this->item->name.'_DESC' ) ); ?>
	<ul class="adminformlist adminformlist-2cols target">
    </ul>
    <ul class="adminformlist adminformlist-2cols">
        <?php
		echo '<li><label>'.Text::_( 'COM_CCK_LIVE' ).'</label>'.$html.'<span class="c_live hide">+</span></li>';
		echo '<li class="w100"><label>JSON</label><textarea id="live_options_id" cols="88" rows="1" class="lives_options"></textarea></li>';
		echo '<li class="w100"><label>'.Text::_( 'COM_CCK_RESTRICTION' ).'</label>'.$html2.'</li>';
		echo '<li class="w100"><label>JSON</label><textarea id="live_restriction_options_id" cols="73" rows="1" class="lives_restriction_options"></textarea></li>';
        ?>
    </ul>
</div>