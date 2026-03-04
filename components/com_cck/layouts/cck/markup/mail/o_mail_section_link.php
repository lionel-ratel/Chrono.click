<?php
defined( '_JEXEC' ) or die;

$href		=	$displayData['cck']->getValue( 'o_mail_section_link_url' );
$text		=	$displayData['cck']->getValue( 'o_mail_section_link_text' ); /* echo $displayData['html'] */

if ( 1 == 1 ) {
	$html	=	'<a class="link-btn" style="font-size: 16px; font-family: Helvetica, Arial, sans-serif; color: #ffffff; text-decoration: none; border-radius: 50px; background-color: #ef8b1c; border-top: 15px solid #ef8b1c; border-bottom: 15px solid #ef8b1c; border-right: 32px solid #ef8b1c; border-left: 32px solid #ef8b1c; display: inline-block;text-transform: uppercase;" href="'.$href.'">'
			.	$text
			.	'</a>';
} else {
	$html	=	'<table role="presentation" border="0" cellspacing="0" cellpadding="0" class="mobile-button-container"><tr><td align="center" style="border-radius: 50px;" bgcolor="#ef8b1c">'
			.	'<a class="link-btn" style="font-size: 16px; font-family:Arial; color: #ffffff; padding:15px 32px; text-decoration: none; display: inline-block; text-transform: uppercase;" href="'.$href.'">'
			.	$text
			.	'</a>'
			.	'</td></tr></table>';
}

$html	=	'<!--[if !mso]><!-->'
		.	$html
		.	'<!--<![endif]-->'
		.	'<!--[if mso]>'
		.	'<v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="'.$href.'" style="height:48px;v-text-anchor:middle;width:300px;" arcsize="60%" stroke="f" fillcolor="#ef8b1c">'
		.	'<w:anchorlock/>'
		.	'<center style="color:#ffffff;font-family:Arial;font-size:16px;font-weight:bold;text-transform: uppercase;">'
		.	$text
		.	'</center>'
		.	'</v:roundrect>'
		.	'<![endif]-->';

echo $html;
?>