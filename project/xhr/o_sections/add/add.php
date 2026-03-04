<?php
defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;

$app		=	Factory::getApplication();
$html		=	'';
$order		=	$app->input->getInt( 'order', 1 );
$pk			=	$app->input->getInt( 'pk', 0 );
$pks		=	0;
$type		=	$app->input->getString( 'type', '' );
$success	=	false;

if ( $type !== '' ) {
	$content	=	new JCckContentFree;
	$content->setTable( '#__cck_store_form_o_section' );
	$content->setOptions( array( 'check_permissions' => 0 ) );

	$data	=	['access'=>1, 'language'=>'*'];

	if ( $content->create( $type, $data )->isSuccessful() ) {
		$success	=	true;
		$pks		=	$content->getPk();	
	}
}

ob_clean();
header( 'Content-Type: application/json' );
echo json_encode( [ 'success' => $success, 'pks' => $pks, 'order' => $order, 'pk' => $pk ] );

Factory::getApplication()->close();
?>