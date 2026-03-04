<?php
defined( '_JEXEC' ) or die;

if ( !( isset( $this ) && $this->isSecure() ) ) {
	return;
}

$this->setValue( 'o_category_title', $this->getValue( 'o_category_title_fr' ) );
?>