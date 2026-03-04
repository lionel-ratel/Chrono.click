<?php
defined( '_JEXEC' ) or die;

$alt		=	$displayData['cck']->getValue( 'o_mail_section_image_text' ); /* echo $displayData['html'] */
$attr		=	$displayData['cck']->getTypo( 'o_mail_section_image_width' ).$displayData['cck']->getTypo( 'o_mail_section_image_height' )
			.	' border="0" style="max-width: 100%; height: auto; box-sizing: border-box;  display: inline-block; color:#262626; font-family:Arial; font-weight:normal; margin:0; text-align:left; line-height:22px; font-size:16px; font-style:normal;"';
$class		=	'';
$html		=	'';

if ( $displayData['cck']->getTypo( 'o_mail_section_image2' ) != '' ) {
	$html	=	'<!--[if !mso]><!-->'
			.	'<div class="dark-img" style="display:none; overflow:hidden; float:left; width:0px; max-height:0px; max-width:0px; line-height:0px; visibility:hidden;">'
			.	'<img '.$displayData['cck']->getTypo( 'o_mail_section_image2' ).' alt="'.$alt.'"'.$attr.$class.'>'
			.	'</div>'
			.	'<!--<![endif]-->';

	$class	=	' class="light-img"';
}

$html	.=	'<img '.$displayData['cck']->getTypo( 'o_mail_section_image' ).' alt="'.$alt.'"'.$attr.$class.'>';

echo $html;
?>