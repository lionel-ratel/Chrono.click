<?php
defined( '_JEXEC' ) or die;

use YooSeblod\Integration;

if ( !isset( $options ) ) {
	return;
}

$layout	=	new YooSeblod\Integration\YooLayout( $value, 'article_'.$config['pk'] );
$typo	=	$layout->display();
?>