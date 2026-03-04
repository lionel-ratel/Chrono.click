<?php
defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;

$buffer			=	$displayData['html'];
$html_tags	=	array(
					'a href="http'=>array(
						0=>'a style="color:#ef8b1c;font-family:Arial;font-weight:normal;margin:0;text-align:left;line-height:22px;font-size:16px;font-style:normal;word-break:break-word;text-decoration:underline;"',
						1=>'href="http'
					),
					'h1'=>array(
						0=>'h1',
						1=>'style="color:#262626;font-family:Arial;font-weight:normal;margin:0;text-align:left;line-height:42px;word-wrap:normal;font-size:36px;font-style:normal;margin-bottom:8px;"',
					),
					'h2'=>array(
						0=>'h2',
						1=>'style="color:#262626;font-family:Arial;font-weight:normal;margin:0;text-align:left;line-height:32px;word-wrap:normal;font-size:26px;font-style:normal;margin-bottom:10px;"',
					),
					'p'=>array(
						0=>'p',
						1=>'style="color:#262626;font-family:Arial;font-weight:normal;margin:0;text-align:left;line-height:22px;font-size:16px;font-style:normal;word-break:break-word;"',
					),
					'small'=>array(
						0=>'small',
						1=>'style="color:#262626;font-family:Arial;font-weight:normal;margin:0;text-align:left;line-height:16px;font-size:11px;font-style:normal;word-break:break-word;"'
					),
          'ul'=>array(
            0=>'ul',
            1=>'style="color:#262626;font-family:Arial;font-weight:normal;padding:0;margin:0;list-style-type:disc;text-align:left;line-height:22px;font-size:16px;font-style:normal;word-break:break-word;"'
          ),
          'ol'=>array(
            0=>'ol',
            1=>'style="color:#262626;font-family:Arial;font-weight:normal;padding:0;margin:0;list-style-type:decimal;text-align:left;line-height:22px;font-size:16px;font-style:normal;word-break:break-word;"'
          ),
          'li'=>array(
            0=>'li',
            1=>'style="color:#262626;font-family:Arial;font-weight:normal;margin: 0 0 0 36px;line-height:22px;font-size:16px;font-style:normal;"'
          )
				);

foreach ( $html_tags as $k=>$v ) {
	$buffer	=	str_replace( '<'.$k, '<'.$v[0].' '.$v[1], $buffer );
}

if ( Factory::getApplication()->input->get( 'preview' ) == '1' ) {
?>
<div class="email-body">
  <style>h1,h2,p,small{padding:0!important;} .email-body{border:1px dashed #ddd;}</style>
  <?php echo $buffer; ?>
</div>
<?php } else { ?>
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="x-apple-disable-message-reformatting">
  <meta name="color-scheme" content="light dark">
  <meta name="supported-color-schemes" content="light dark">
  <!--[if !mso]><!-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <!--<![endif]-->
  <title></title>
  <!--[if mso]>
  <style type="text/css">
    table {border-collapse:collapse;border-spacing:0;margin:0;}
    div, td {padding:0;}
    div {margin:0 !important;}
  </style>
  <noscript>
    <xml>
      <o:OfficeDocumentSettings>
        <o:PixelsPerInch>96</o:PixelsPerInch>
      </o:OfficeDocumentSettings>
    </xml>
  </noscript>
  <![endif]-->
  <style type="text/css">
    @media (prefers-color-scheme: dark ) {
      .dark-img{display:block !important; width: auto !important; overflow: visible !important; float: none !important; max-height:inherit !important; max-width:inherit !important; line-height: auto !important; margin-top:0px !important; visibility:inherit !important;}
      .light-img{display:none; display:none !important;}
      .inner{background-color: #333333 !important;}
      .outer{background-color: #1c1c1e !important;}
      h1, h2, p, small, ul li {color: #ffffff !important;}

      [data-ogsc] .dark-img{display:block !important; width: auto !important; overflow: visible !important; float: none !important; max-height:inherit !important; max-width:inherit !important; line-height: auto !important; margin-top:0px !important; visibility:inherit !important;}
      [data-ogsc] .light-img{display:none; display:none !important;}
      [data-ogsc] .inner{background-color: #333333 !important;}
      [data-ogsc] .outer{background-color: #1c1c1e !important;}
      [data-ogsc] h1, [data-ogsc] h2, [data-ogsc] p, [data-ogsc] span, [data-ogsc] small, [data-ogsc] ul li {color: #ffffff !important;}
    }
  </style>
</head>
<body style="margin:0;padding:0;word-spacing:normal;background-color:#efefef;" class="outer">
  <div role="article" aria-roledescription="email" lang="en" style="-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;background-color:#efefef;" class="outer">
    <table width="100%" align="center" border="0" cellpadding="0" cellspacing="0" role="presentation">
      <tr>
        <td align="center">
          <!--[if mso]>
          <table role="presentation" align="center" style="width:600px;background-color:#ffffff;">
          <tr>
          <td style="padding:20px 0;">
          <![endif]-->
          <div style="display: none; font-size: 1px; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: sans-serif;"><?php echo $displayData['cck']->getValue( 'o_mail_template_snippet' ); ?></div>
          <div class="inner" style="width:100%;max-width:600px;margin:20px auto;background-color:#ffffff;">
          <?php echo $buffer; ?>
          </div>
          <!--[if mso]>
          </td>
          </tr>
          </table>
          <![endif]-->
        </td>
      </tr>
    </table>
  </div>
</body>
<?php } ?>