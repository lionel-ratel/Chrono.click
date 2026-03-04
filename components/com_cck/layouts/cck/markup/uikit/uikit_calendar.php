<?php
defined( '_JEXEC' ) or die;

dump( $displayData, 'D');

$search		=	array( 'inputbox text', 'hasTooltip', 'class="icon-calendar"', 'input-append' );
$replace	=	array( 'uk-input', 'uk-button uk-button-default', 'uk-icon="icon: calendar"', 'uk-flex' );

$displayData['html']	=	str_replace( $search, $replace, $displayData['html'] );

// Label
if ( $displayData['options']->get( 'field_label', $displayData['cck']->getStyleParam( 'field_label', 1 ) ) ) {
	$label	=	$displayData['cck']->getLabel( $displayData['field']->name, true, ( $displayData['field']->required ? '*' : '' ) );
	$label	=	( $label != '' ) ? '<div class="o-label">'.$label.'</div>' : '';
}
?>
<div class="uk-margin"<?php echo $attr; ?>>
    <div class="uk-form-label"><?php echo $label; ?></div>
    <div class="uk-form-controls">
        <?php echo $displayData['html'].$desc; ?>
    </div>
</div>
