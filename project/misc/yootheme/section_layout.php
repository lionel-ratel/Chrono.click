<?php
defined( '_JEXEC' ) or die;

if ( !isset( $options ) ) {
    return false;
}

$query	=	'SELECT a.params'
		.	' FROM #__template_styles AS A'
		.	' LEFT JOIN #__cck_core_types AS b ON b.template_intro = a.id'
		.	' WHERE b.id='.(int)$config['type_id']
		;

$style	=	json_decode( 
				JCckDatabase::loadResult( $query )
			);

if ( !$style->layout_id ) {
	return;
}

$layout	=	json_decode(
				JCckDatabase::loadResult( 'SELECT json FROM #__cck_store_form_o_yootheme_template WHERE id='.(int)$style->layout_id )
			);

$path		=	'/templates/yootheme/vendor/yootheme/builder/elements';
$icon		=	'images/icon.svg';
$icon_small	=	'images/iconSmall.svg';

$section	=	$layout->children[0];
$row		=	$section->children[0];

$html		=	'';

foreach ( $row->children as $key => $column ) {
	$children	=	$column->children[0];
	$class		=	$column->props->width_medium;
	$type		=	$children->type;

	$image		=	$path.'/'.$type.'/'.$icon_small;

	$html	.=	'<div class="uk-width-'.$class.'">
					<div class="uk-flex uk-flex-column">
						<div class="yo-builder-element uk-flex-1 uk-width-1-1 uk-flex uk-flex-center uk-flex-middle">
							<div class="uk-text-center">
								<img alt="Image" src="'.$image.'" uk-svg="" class="" width="20" height="20">
								<span class="uk-margin-small-left uk-text-middle">'.ucfirst( $type ).'</span>
							</div>
						</div>
					</div>
				</div>';
}

$value	=	'<div class="yo-builder-grid">'
		.		'<div>'
		.			'<div class="uk-grid uk-grid-match uk-child-width-expand">'
		.				$html
		.			'</div>'
		.		'</div>'
		.	'</div>';


$field->display	=	1;
?>