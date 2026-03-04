<?php
defined( '_JEXEC' ) or die;

if ( !( isset( $this ) && $this->isSecure() ) ) {
	return;
}

if ( $this->getType() == 'o_mail_template_tester' ) {
	require_once JPATH_SITE.'/project/helper.php';

	if ( $this->getValue( 'o_mail_template_tester_name' ) && $this->getValue( 'o_mail_template_tester_to' ) ) {
		ProjectHelper::sendMailTemplate( $this->getValue( 'o_mail_template_tester_name' ), $this->getValue( 'o_mail_template_tester_to' ), array() );
	}

	return;
}

$content_free	=	new JCckContentFree;

$content_free->setTable( '#__cck_store_form_o_mail_template');
$content_free->extend( JPATH_SITE.'/project/apps/o_mail_templates/extend/mixin.php' );

if ( !$content_free->load( $this->getPk() )->isSuccessful() ) {
	return;
}

$content_free->_updateBody();
?>