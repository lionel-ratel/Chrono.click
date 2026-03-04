<?php
defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Session\Session;

$url_delete	=	'format=raw&task=ajax&'.Session::getFormToken().'=1'
			.	'&referrer=processing.project_xhr_o_sections_delete_delete'
			.	'&file=project/xhr/o_sections/delete/delete.php';
$url_delete	=	JCckDevHelper::getAbsoluteUrl( 'auto', $url_delete );	

$url_insert	=	'format=raw&task=ajax&'.Session::getFormToken().'=1'
			.	'&referrer=processing.project_xhr_o_sections_add_add'
			.	'&file=project/xhr/o_sections/add/add.php';
$url_insert	=	JCckDevHelper::getAbsoluteUrl( 'auto', $url_insert );	

// 
$js		=	'(function($) {
				$(document).ready(function(){
					JCck.Uikit.modal.url_delete = "'.$url_delete.'";
					JCck.Uikit.modal.url_insert = "'.$url_insert.'";
					JCck.More.ItemX.modal = JCck.Uikit.modal;
					JCck.Uikit.modal.initInstances();
				});
			})(jQuery);';

Factory::getDocument()->addScriptDeclaration( $js );
?>