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
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
?>
<?php if ( !$raw_rendering ) { ?>
<div class="cck_module_list<?php echo $class_sfx; ?>">
<?php }
if ( isset( $search->content ) && $search->content > 0 ) {
	echo ( $raw_rendering ) ? $data : '<div>'.$data.'</div>';
	
	if ( ( $show_pagination == 2 || $show_pagination == 8 ) && $total_items > $total ) {
		echo '<div class="'.$class_pagination.'"'.( $show_pagination == 8 ? ' style="display:none;"' : '' ).'><ul class="pagination-list"><li><img id="seblod_form_loading_more" src="media/cck/images/spinner.gif" alt="" style="display:none;" width="28" height="28" /><a id="seblod_form_load_more" href="javascript:void(0);" data-start="'.$offset.'" data-step="'.$config['limitend'].'" data-end="'.( $total_items + $offset ).'">'.Text::_( 'COM_CCK_LOAD_MORE' ).'</a></li></ul></div>';
		?>
		<script type="text/javascript">
		(function ($){
			JCck.Core.loadmore = function(query_params,has_more,replace_html) {
				var data_type    = 'html';
				var elem_target = ".cck-loading-more";
				var replace_html = replace_html || 0;
				$("form#<?php echo $config['formId']; ?> [data-cck-ajax=\'\']").each(function(i) {
					var name = $(this).attr("name");
					query_params += "&"+(name !== undefined ? name : $(this).attr("id"))+"="+$(this).myVal().replace("&", "%26");
				});
				if (has_more < 0) {
					data_type = 'json';
					query_params += "&wrapper=1";
				}
				$.ajax({
					cache: false,
					data: "format=raw&infinite=1<?php echo ( $preconfig['limitend'] ? '&end='.$preconfig['limitend'] : '' );?>&return=<?php echo base64_encode( Uri::getInstance()->toString() ); ?>"+query_params,
					dataType: data_type,
					type: "GET",
					url: '<?php echo JCckDevHelper::getAbsoluteUrl( 'auto', 'view=list&search='.$search->name.( $preconfig['search2'] ? '|'.$preconfig['search2'] : '' ).'&context='.json_encode( $context ) ); ?>',
					beforeSend:function(){ $("#seblod_form_load_more").hide(); $("#seblod_form_loading_more").show(); },
					success: function(response){
						if (has_more < 0) {
							var $el = $("#seblod_form_load_more");
							$($el).attr("data-start",0).attr("data-end",response.total);
							if (response.total > response.count) {
								$("#seblod_form_load_more, [data-cck-loadmore-pagination]").show();
							} else {
								$("[data-cck-loadmore-pagination]").hide();
							}
		 					if ($("[data-cck-total]").length) {
		 						$("[data-cck-total]").text(response.total);
		 					}
							response = response.html;
						} else {
							if (has_more != 1) {
								$("#seblod_form_load_more").show()<?php echo ( $show_pagination == 8 ) ? '.click()' : ''; ?>;
							} else {
								$(".cck_module_list .pagination").hide();
							}
						}
						$("#seblod_form_loading_more").hide();
						if (replace_html==1) { $(elem_target).html(response); } else { $(elem_target).append(response); }
						<?php
						if ( $callback_pagination != '' ) {
							$pos	=	strpos( $callback_pagination, '$(' );

							if ( $pos !== false && $pos == 0 ) {
								echo $callback_pagination;
							} else {
								echo $callback_pagination.'(response);';
							}
						}
						?>
					},
					error:function(){}
				});
			};
			$(document).ready(function() {
				$("#seblod_form_load_more").on("click", function() {
					var start = parseInt($(this).attr("data-start"));
					var step = parseInt($(this).attr("data-step"));
					start = start+step;
					var stop = (start+step>=parseInt($(this).attr("data-end"))) ? 1 : 0;
					$(this).attr("data-start",start);
					JCck.Core.loadmore("&start="+start,stop);
				})<?php echo ( $show_pagination == 8 ) ? '.click()' : ''; ?>;
			});
		})(jQuery);
		</script>
		<?php
	}
	if ( $load_resource && $total_items ) {
		$url	=	Route::_( 'index.php?Itemid='.(int)$link_resource );

		if ( $url == '/' ) {
			$url	=	'';
		}
		$url	=	Uri::getInstance()->toString( array( 'scheme', 'host', 'port' ) ).$url;
		?>
		<script type="text/javascript">
		(function ($){
			JCck.Core.loadfragment = JCck.Core.getModal(<?php echo $json_resource ? $json_resource : '{}'; ?>);
			$(document).ready(function() {
				var fragment = window.location.hash;
				if (fragment != "") {
					fragment = fragment.substring(1);
					setTimeout(function() {
						JCck.Core.loadfragment.loadUrl("<?php echo $url; ?>/"+fragment);
					}, 1);
				}
			});
		})(jQuery);
		</script>
		<?php
	}
} else {
	include __DIR__.'/render_items.php';
}
?>
<?php if ( $show_more_link ) { ?>
	<div class="more"><a<?php echo $show_more_class; ?> href="<?php echo $show_more_link; ?>"><?php echo $show_more_text; ?></a></div>
<?php } if ( !$raw_rendering ) { ?>
</div>
<?php } ?>