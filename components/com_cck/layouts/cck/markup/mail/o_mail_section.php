<?php
defined( '_JEXEC' ) or die;

$alignment	=	$displayData['cck']->getValue('o_mail_section_alignment');
$container	=	$displayData['cck']->getValue('o_mail_section_container') ? 'padding-left:16px;padding-right:16px;' : '';
?>
<table role="presentation" width="100%" border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;">
  <tr>
    <td align="<?php echo $alignment; ?>" style="padding-top:2px;padding-bottom:10px;<?php echo $container; ?>">
		<?php echo $displayData['html']; ?>
    </td>
  </tr>
</table>