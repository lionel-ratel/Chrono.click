<?php
defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Session\Session;

$url	=	'format=raw&task=ajax&'.Session::getFormToken().'=1'
		.	'&referrer=processing.project_apps_o_sections_process_field_delete_script'
		.	'&file=project/apps/o_sections/process/field/delete/script.php';
$url	=	JCckDevHelper::getAbsoluteUrl( 'auto', $url );	

$js		=	'JCck.Project.url_delete = "'.$url.'"';

Factory::getDocument()->addScriptDeclaration( 'jQuery(document).ready(function(){'.$js.'});' );
?>