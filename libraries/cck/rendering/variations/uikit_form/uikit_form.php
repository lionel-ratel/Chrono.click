<?php
defined( '_JEXEC' ) or die;

$attr	=	$options->get( 'position_attributes', '' );
$attr	=	$attr ? ' '.$attr : '';
$class	=	$options->get( 'position_class', '' );
$class	=	$class ? ' class="'.$class.'"' : '';

if ( $class || $attr ) {
?>
<div<?php echo $class.$attr; ?>>
<?php } ?>
	<?php echo $content; ?>
<?php if ( $class || $attr ) { ?>
</div>
<?php } ?>