<?php
defined( '_JEXEC' ) or die;

use Joomla\CMS\HTML\HTMLHelper;

if ( !isset( $options ) ) {
	return false;
}

$field->display	=	1;
$form			=	'<p><em>'.JCckDatabase::loadResult( 'SELECT title FROM #__cck_core_types WHERE name="'.$config['type'].'";' ).'</em></p>';