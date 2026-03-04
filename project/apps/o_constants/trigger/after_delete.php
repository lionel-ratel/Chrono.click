<?php
defined( '_JEXEC' ) or die;

if ( !( isset( $this ) && $this->isSecure() ) ) {
	return;
}

require_once JPATH_SITE.'/project/helper.php';

foreach ( array( 'override', 'com_cck_default' ) as $filename ) {
	ProjectHelper::updateConstantFile( 'fr-FR', $filename );
}
?>