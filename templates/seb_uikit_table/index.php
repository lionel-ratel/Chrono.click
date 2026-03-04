<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: index.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

// -- Initialize
require_once __DIR__.'/config.php';
$cck	=	CCK_Rendering::getInstance( $this->template );
if ( $cck->initialize() === false ) { return; }

$attributes		=	$cck->item_attributes;
$table_columns	=	$cck->getStyleParam( 'table_columns', 0 );
$table_header	=	$cck->getStyleParam( 'table_header', 0 );

$divider		=	$cck->getStyleParam( 'divider', 0 );
$hover			=	$cck->getStyleParam( 'hover', 0 );
$stripped		=	$cck->getStyleParam( 'stripped', 0 );
$sortable		=	$cck->getStyleParam( 'sortable', 0 );
$vertical_align	=	$cck->getStyleParam( 'vertical_align', 0 );

$class_table	=	trim( $cck->getStyleParam( 'class_table', 'uk-table' ) );
$class_table	.=	$divider ? ' uk-table-divider' : '';
$class_table	.=	$hover ? ' uk-table-hover' : '';
$class_table	.=	$stripped ? ' uk-table-stripped' : '';
$class_table	.=	$divider ? ' uk-table-divider' : '';
$class_table	.=	$vertical_align ? ' uk-table-middle' : '';
$class_table	=	$class_table ? ' class="'.$class_table.'"' : '';

$class_body		=	'';
$tbody_attr		=	'';
if ( $sortable ) {
	$tbody_attr	=	'cls-custom: uk-box-shadow-small uk-flex uk-flex-middle uk-background;';

	if ( $sortable == 2 ) {
		$tbody_attr	.=	'handle: .uk-sortable-handle';
	}
}
$tbody_attr		=	( $tbody_attr ) ? ' uk-sortable="'.$tbody_attr.'"' : '';

$translate		=	JCck::getConfig_Param( 'language_jtext', 1 );

if ( $translate ) {
	$lang	=	Factory::getLanguage();
}

// Set
$isMore			=	$cck->isLoadingMore();
$legacy			=	(int)JCck::getConfig_Param( 'core_legacy', '2012' );

if ( $cck->isGoingToLoadMore() ) {
	$class_body	=	' class="cck-loading-more"';
}

// -- Render
if ( $cck->id_class && !$isMore ) {
?>
<div id="<?php echo $cck->id; ?>" class="<?php echo $cck->id_class; ?>"<?php echo ( $cck->id_attributes ? ' '.$cck->id_attributes : '' ); ?>>
	<div>
	<?php }
	$attr		=	array(
						'class'=>array(),
						'width'=>array()
					);
	$body		=	array();
	$head		=	array();
	$html		=	'';
	$items		=	$cck->getItems();
	$positions	=	$cck->getPositions();
	ksort( $positions );
	$tbody		=	'';
	$thead		=	false;
	$unset		=	array();

	unset( $positions['hidden'] );

	$count		=	count( $items );
	
	foreach ( $positions as $name=>$position ) {
		$class					=	$position->css;
		$attr['class'][$name]	=	$class ? ' class="'.$class.'"' : '';
		$attr['label'][$name]	=	'';

		$head[$name]			=	array( 'count'=>$count, 'fields'=>0, 'html'=>'', 'items'=>array() );
		$legend					=	'';
		$width					=	$cck->w( $name );

		if ( $position->legend ) {
			$legend						=	trim( $position->legend );
			$legend2					=	$legend;

			if ( $legend != '' && !( $legend[0] == '<' || strpos( $legend, ' / ' ) !== false ) ) {
				if ( $translate ) {
					$key				=	'COM_CCK_' . str_replace( ' ', '_', trim( $legend ) );
					
					if ( $lang->hasKey( $key ) ) {
						$legend			=	Text::_( $key );
					}
				}
				$attr['label'][$name]	=	' data-label="'.strip_tags( $legend ).'"';
			}
		} else {
			if ( isset( $position->legend2 ) && $position->legend2 ) {
				$legend					=	trim( $position->legend2 );
				$legend2				=	$legend;
				$attr['label'][$name]	=	' data-label="'.strip_tags( $legend ).'"';
			}
		}

		if ( $legend || $width ) {
			if ( $legend ) {
				$thead	=	true;
			}
			if ( $position->variation != '' ) {
				$var		=	$cck->renderVariation( $position->variation, $legend2, '', $position->variation_options, $name );
				$matches	=	array();
				preg_match( '#<th class="([a-zA-Z0-9\-\ _]*)"(.*)>#U', $var, $matches );
				if ( isset( $matches[1] ) && $matches[1] != '' ) {
					$class	=	$matches[1];
				} else {
					$class	=	'';
				}
				$attr['class'][$name]	=	$class ? ' class="'.$class.'"' : '';
				$attr['width'][$name]	=	( $width ) ? ' width="'.$width.'"' : ''; // ( $width ) ? ' style="width:'.$width.'"' : '';
				$head[$name]['html']	=	$var;
			} else {
				$attr['width'][$name]	=	( $width ) ? ' width="'.$width.'"' : ''; // ( $width ) ? ' style="width:'.$width.'"' : '';
				$head[$name]['html']	=	'<th'.$attr['class'][$name].$attr['width'][$name].'>'.$legend.'</th>';	
			}
		}
	}
	?>
	<?php
	if ( $count ) {
		$i	=	0;
        foreach ( $items as $item ) {
        	$body[$i]['cols']	=	array();
			$body[$i]['html']	=	'<tr'.$item->replaceLive( $attributes ).'>';

            foreach ( $positions as $name=>$position ) {
				$col		=	'';
				$width		=	'';

				if ( $legacy && $legacy <= 2019 ) {
					$fieldnames	=	$cck->getFields( $name, '', false );

					if ( $i == 0 ) {
						$head[$name]['fields']	=	( count( $fieldnames ) > 1 ) ? true : false;
					}

					$multiple	=	$head[$name]['fields'];

					foreach ( $fieldnames as $fieldname ) {
						$content	=	$item->renderField( $fieldname );
						if ( $content != '' ) {
							if ( $item->getMarkup( $fieldname ) != 'none' && ( $multiple || $item->getMarkup_Class( $fieldname ) ) ) {
								$col	.=	'<div class="cck-clrfix'.$item->getMarkup_Class( $fieldname ).'">'.$content.'</div>';
							} else {
								$col	.=	$content;
							}
						}
					}
				} else {
					$col		=	$item->renderPosition( $name, 'cell' );
				}

				if ( $col == '' ) {
					if ( !$table_columns ) {
						$head[$name]['count']--;
					}
				}
				$body[$i]['cols'][$name]	=	'<td'.$attr['class'][$name].$attr['label'][$name].$width.'>'.$col.'</td>';
			}
			$body[$i]['html2']	=	'</tr>';
			$i++;
		}
		if ( count( $head ) ) {
			foreach ( $head as $k=>$v ) {
				if ( $v['count'] == 0 ) {
					$unset[$k]	=	$k;
					unset( $head[$k] );
				}
			}
		}
		if ( count( $body ) ) {
			foreach ( $body as $k=>$v ) {
				foreach ( $unset as $col ) {
					unset( $v['cols'][$col] );
				}
				$row		=	implode( $v['cols'] );
				$tbody		.=	$v['html'].$row.$v['html2'];
			}
		}
	}

	if ( $isMore < 1 ) {
		$tbody	=	'<tbody'.$class_body.$tbody_attr.'>'.$tbody.'</tbody>';
	}
	if ( !$isMore ) {		
		$html	.=	'<table'.$class_table.'>';
	}
	if ( $isMore < 1 && $thead && count( $head ) ) {
		$thead	=	'';

		foreach ( $head as $k=>$v ) {
			$thead	.=	$v['html'];
		}
	} else {
		$thead	=	'';
	}
	if ( $thead && ( $table_header == 0 || $table_header == 1 ) ) {
		$html	.=	'<thead><tr>'.$thead.'</tr></thead>';
	}
	$html		.=	$tbody;

	if ( $thead && ( $table_header == -1 || $table_header == 1 ) ) {
		$html	.=	'<tfoot><tr>'.$thead.'</tr></tfoot>';
	}
	if ( !$isMore ) {
		$html	.=	'</table>';
	}
	echo $html;

	if ( $cck->id_class && !$isMore ) { ?>
    </div>
</div>
<?php
}
// -- Finalize
$cck->finalize();
?>