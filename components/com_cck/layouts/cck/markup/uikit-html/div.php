<?php
use YooSeblod\Integration;

defined( '_JEXEC' ) or die;

YooSeblod\Integration\YooUikit::markupField( $displayData['field'] );

$class	=	trim( $displayData['field']->markup_class );
$class	=	$class ? ' class="'.$class.'"' : '';
?>
<div<?php echo $class; ?>><?php echo $displayData['field']->form; ?></div>