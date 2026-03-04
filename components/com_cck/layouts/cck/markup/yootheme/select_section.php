<?php
use YooSeblod\Integration;

defined( '_JEXEC' ) or die;

$class	=	trim( $displayData['field']->markup_class );

if ( $class != '' ) {
	$class	=	' '.$class;
}

$section	=	new \YooSeblod\Integration\YooLayout( $displayData['field']->value );
$typo		=	str_replace( '{layout}', $section->render(), $displayData['field']->typo );
?>
<div class="uk-margin<?php echo $class; ?>">
	<div class="uk-form-controls">
		<div class="uk-inline uk-width-1-1">
			<?php echo $typo; ?>
		</div>
	</div>
</div>