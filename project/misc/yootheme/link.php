<?php
defined( '_JEXEC' ) or die;

if ( !isset( $options ) ) {
    return false;
}

$type   =   $fields['o_yootheme_section']->value;
$typo   =   '<a href="javascript:void(0);" onclick="JCck.Project.loadSectionForm(\''.$type.'\');">'
        .   $fields[$name]->text
        .   '</a>';
?>