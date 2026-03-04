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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
?>
<?php if ( !$raw_rendering ) { ?>
<div class="cck_module_list<?php echo $class_sfx; ?>">
<?php }
if ( isset( $search->content ) && $search->content > 0 ) {
	echo ( $raw_rendering ) ? $data : '<div>'.$data.'</div>';
	
	if ( $load_ajax ) {
		$context	=	'&context={\'Itemid\':'.$preconfig['itemId'].',\'view\':\''.Factory::getApplication()->input->get( 'view' ).'\'}';
		echo '<div class="'.$class_pagination.'"'.( $show_pagination == 8 ? ' style="display:none;"' : '' ).'><ul class="pagination-list"><li><img id="seblod_form_loading_more" src="media/cck/images/spinner.gif" alt="" style="display:none;" width="28" height="28" /><a id="seblod_form_load_more" href="javascript:void(0);" data-start="'.$offset.'" data-step="'.$config['limitend'].'" data-end="'.( $total_items + $offset ).'">'.Text::_( 'COM_CCK_LOAD_MORE' ).'</a></li></ul></div>';
		?>
		<script type="text/javascript">
		(function ($){
			JCck.Core.loadmore = function(more,stop,search) {
				var elem = ".cck-loading-more";
				var search = search || 0;
				$.ajax({
					cache: false,
					data: "format=raw&infinite=1<?php echo ( $preconfig['limitend'] ? '&end='.$preconfig['limitend'] : '' );?>&return=<?php echo base64_encode( Uri::getInstance()->toString() ); ?>"+more,
					type: "GET",
					url: "<?php echo JCckDevHelper::getAbsoluteUrl( 'auto', 'view=list&search='.$search->name.$context ); ?>",
					beforeSend:function(){ $("#seblod_form_load_more").hide(); $("#seblod_form_loading_more").show(); },
					success: function(response){
						if (stop != 1) {
							$("#seblod_form_load_more").show()<?php echo ( $show_pagination == 8 ) ? '.click()' : ''; ?>;
						} else {
							$(".cck_module_list .pagination").hide();
						}
						$("#seblod_form_loading_more").hide();
						if (search==1) { $(elem).html(response); } else { $(elem).append(response); }
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
					console.log("hey");
					/*
					var start = parseInt($(this).attr("data-start"));
					var step = parseInt($(this).attr("data-step"));
					start = start+step;
					var stop = (start+step>=parseInt($(this).attr("data-end"))) ? 1 : 0;
					$(this).attr("data-start",start);
					JCck.Core.loadmore("&start="+start,stop);
					*/
				});
			});
		})(jQuery);
		</script>
		<?php
	}
} else {
	include __DIR__.'/render_items.php';
}
?>
<?php if ( !$raw_rendering ) { ?>
</div>
<?php } ?>