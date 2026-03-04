<?php
defined('_JEXEC') or die;

$label	=	$field->value;
$typo   =   '<a href="'.$field->link.'" onclick="if(!confirm(\'Souhaitez-vous vraiment retirer cet élément&nbsp;?\')){return false;}">'.$label.'</a>';
?>