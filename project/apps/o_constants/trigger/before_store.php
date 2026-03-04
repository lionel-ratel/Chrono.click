<?php
defined( '_JEXEC' ) or die;

if ( !( isset( $this ) && $this->isSecure() ) ) {
	return;
}

$this->setValue( 'o_constant_constant', strtoupper( $this->getValue( 'o_constant_constant' ) ) );
?>