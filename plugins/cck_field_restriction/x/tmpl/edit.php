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

use Joomla\CMS\Language\Text;

use Joomla\CMS\HTML\HTMLHelper;

$type						=	'restriction';
$options					=	array( 'js'=>array() );
$options['js']['load']		=	'var eid = "'.$this->item->id.'";
								var elem = "'.$this->item->id.'_'.$type.'_options";
								var encoded = parent.jQuery("#"+elem).val();
								var data = ( encoded !== undefined && encoded != "" ) ? $.evalJSON(encoded) : "";
								if (data) {
									var j = 0;
									if (typeof data === "object" && data.conditions) {
										$.each(data.conditions, function(k, v) {
											if (typeof v === "object") {
												var p = "'.$type.'";
												var $clone = $("#'.$type.'_id").parent().clone().addClass("new").appendTo(".target");
												$("li.new > *").attr("id",p+j).myVal(v.trigger).parent().removeClass("new");
												var $clone = $("#'.$type.'_options_id").parent().clone().addClass("new").appendTo(".target");
												$("li.new > *").attr("id",p+j+"_options").myVal($.toJSON(v.options)).parent().removeClass("new");
											}
										});
									}
								}';
$options['js']['submit']	=	'if ( $("#adminForm").validationEngine("validate") === true ) {
									var eid = "'.$this->item->id.'";
									var elem = "'.$this->item->id.'_'.$type.'_options";
									var data = {};
									var excluded = [];
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
											data.conditions[i] = d;
										}
									});
									var encoded = $.toJSON(data);
									parent.jQuery("#"+elem).val(encoded);
									this.close();
									return;
								}';
JCckDev::initScript( 'restriction', $this->item, $options );

require_once JPATH_COMPONENT.'/helpers/helper_admin.php';
$restrictions	=	array_merge( array( HTMLHelper::_( 'select.option', '', Text::_( 'COM_CCK_NONE' ) ) ), Helper_Admin::getPluginOptions( 'field_restriction', 'cck_', false, false, true ) );

if ( count( $restrictions ) ) {
	foreach ( $restrictions as $k=>$v ) {
		if ( $v->value == 'x' ) {
			unset( $restrictions[$k] );
		}
	}
}
$html			=	HTMLHelper::_( 'select.genericlist', $restrictions, 'restriction', 'class="restrictions"', 'value', 'text', '', 'restriction_id' );
?>
<div class="seblod">
	<?php echo JCckDev::renderLegend( Text::_( 'COM_CCK_CONSTRUCTION' ), Text::_( 'PLG_CCK_FIELD_RESTRICTION_'.$this->item->name.'_DESC' ) ); ?>
	<ul class="adminformlist adminformlist-2cols target">
    </ul>
    <ul class="adminformlist adminformlist-2cols">
        <?php
		echo '<li><label>'.Text::_( 'COM_CCK_RESTRICTION' ).'</label>'.$html.'<span class="c_res hide">+</span></li>';
		echo '<li class="w100"><label>JSON</label><textarea id="restriction_options_id" cols="88" rows="1" class="restrictions_options"></textarea></li>'
        ?>
    </ul>
</div>