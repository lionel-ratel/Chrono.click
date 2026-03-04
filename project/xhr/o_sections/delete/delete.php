<?php
defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;

$app		=	Factory::getApplication();
$pk			=	$app->input->getInt( 'pk', 0 );
$success	=	false;

if ( $pk ) {
	$content	=	new JCckContentFree;
	$content->setTable( '#__cck_store_form_o_section' );
	$content->setOptions( array( 'check_permissions' => 0 ) );

	if ( $content->load( $pk )->isSuccessful() ) {
		$query		=	'DELETE FROM #__cck_core WHERE id='.(int)$content->getId();
		$success	=	$content->delete();

		if ( $success ) {
			JCckDatabase::execute( $query );
		}
	}
}

ob_clean();
header( 'Content-Type: application/json' );
echo json_encode( [ 'success' => $success ] );

Factory::getApplication()->close();
?>