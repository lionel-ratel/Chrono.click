<?php
defined( '_JEXEC' ) or die;

if ( !isset( $options ) ) {
	return false;
}

$query		=	'UPDATE #__content AS a'
			.	' LEFT JOIN #__cck_store_form_o_builder AS b ON b.id = a.id'
			.	' LEFT JOIN #__cck_store_form_o_layout AS c ON c.type_id = b.type_id'
			.	' SET a.fulltext = c.json'
			.	' WHERE b.id IS NOT NULL';

JCckDatabase::execute( $query );


?>