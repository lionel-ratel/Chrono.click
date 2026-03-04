<?php
use YooSeblod\Integration;

defined( '_JEXEC' ) or die;

$class	=	trim( $displayData['field']->markup_class );

if ( $class != '' ) {
	$class	=	' '.$class;
}

// Label
if ( $displayData['options']->get( 'field_label', $displayData['cck']->getStyleParam( 'field_label', 1 ) ) ) {
	$label	=	$displayData['cck']->getLabel( $displayData['field']->name, true, ( $displayData['field']->required ? '*' : '' ) );

	if ( $label != '' ) {
		$label	=	str_replace( '<label', '<label class="uk-form-label"', $label );
	}
}

$builder	=	new \YooSeblod\Integration\YooLayout( $displayData['field']->value );
?>
<div class="uk-margin<?php echo $class; ?>">
	<?php echo $label; ?>
	<div class="uk-form-controls">
		<div class="uk-inline uk-width-1-1">
			<?php echo $builder->render(); ?>
		</div>
	</div>
</div>