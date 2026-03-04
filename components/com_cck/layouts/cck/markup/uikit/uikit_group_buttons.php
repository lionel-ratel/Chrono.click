<?php
use YooSeblod\Integration;

defined( '_JEXEC' ) or die;

// $html	=	'';

// dump( $displayData['field'], 'F' );
// dump( $displayData['html'] );


// foreach ( $displayData['field']->form as $key => $button ) {
// 	if ( (int)$button->state ) {
// 		YooSeblod\Integration\YooUikit::markupField( $button );

// 		$html	.=	$button->form;
// 	}
// }

$class	=	trim( $displayData['field']->markup_class );
$class	=	$class ? ' '.$class : '';
?>
<div class="uk-margin uk-grid-small<?php echo $class; ?>" uk-grid><?php echo $displayData['html']; ?></div>