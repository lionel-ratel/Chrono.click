<?php
defined( '_JEXEC' ) or die;

if ( !( isset( $this ) && $this->isSecure() ) ) {
	return;
}

$content_free	=	new JCckContentFree;

$content_free->setTable( '#__cck_store_form_o_mail_template');
$content_free->extend( JPATH_SITE.'/project/apps/o_mail_templates/extend/mixin.php' );

$pks			=	JCckDatabase::loadColumn( 'SELECT id FROM #__cck_store_join_o_mail_template_sections WHERE id2='.(int)$this->getPk() );

foreach ( $pks as $pk ) {
	if ( !$content_free->load( $pk )->isSuccessful() ) {
		continue;
	}

	$content_free->_updateBody();
}
?>