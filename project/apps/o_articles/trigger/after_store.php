<?php
defined( '_JEXEC' ) or die;

if ( !( isset( $this ) && $this->isSecure() ) ) {
	return;
}

$this->setValue( 'o_article_title', $this->getValue( 'o_article_title_fr' ) );
?>