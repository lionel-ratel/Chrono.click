<?php
defined( '_JEXEC' ) or die;

if ( !isset( $options ) ) {
        return;
}

$url_dev        =       'https://ecologic-asl.octopoos-dev.com';
$url_base       =       JUri::getInstance()->toString( array( 'scheme', 'user', 'pass', 'host', 'port' ) );

JCckDatabase::execute( 'UPDATE #__cck_store_form_o_mail_template SET body= REPLACE(body,"'.$url_dev.'","'.$url_base.'") WHERE 1' );

$query  =       'UPDATE #__cck_store_form_o_mail_section'
                .       ' SET text= REPLACE(text,"'.$url_dev.'","'.$url_base.'"),'
                .       ' html= REPLACE(html,"'.$url_dev.'","'.$url_base.'"),'
                .       ' link_url= REPLACE(link_url,"'.$url_dev.'","'.$url_base.'")'
                .       ' WHERE 1';

JCckDatabase::execute( $query );
?>
