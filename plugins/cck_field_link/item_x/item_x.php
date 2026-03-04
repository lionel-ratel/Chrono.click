<?php
/**
* @version 			SEBLOD 3.x More
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

// Plugin
class plgCCK_Field_LinkItem_X extends JCckPluginLink
{
	protected static $type	=	'item_x';
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
		
	// onCCK_Field_LinkPrepareContent
	public static function onCCK_Field_LinkPrepareContent( &$field, &$config = array() )
	{		
		if ( self::$type != $field->link ) {
			return;
		}
		
		// Prepare
		$link	=	parent::g_getLink( $field->link_options );
		
		// Set
		$field->link	=	'';
		self::_link( $link, $field, $config );
	}
	
	// _link
	protected static function _link( $link, &$field, &$config )
	{
		$identifier				=	$link->get( 'identifier', 'pk' );
		$identifier_fieldname	=	'';
		$link_class				=	$link->get( 'class', '' );
		$link_onclick			=	'';
		$link_title				=	$link->get( 'title', '' );
		$link_title2			=	$link->get( 'title_custom', '' );
		$parent					=	$link->get( 'fieldname', '' );
		$type					=	$link->get( 'type', 'add' );

		if ( $identifier == 'field' ) {
			$identifier				=	'pk';
			$identifier_fieldname	=	$link->get( 'identifier_fieldname', '' );
		}
		
		// Prepare
		if ( $type == 'save' ) {
			$link_onclick		=	'JCck.More.ItemX.save();';
		} elseif ( $type == 'add' ) {
			$form				=	$link->get( 'form', '' );
			$form				=	$form ? '\''.$form.'\'' : '';
			$link_onclick		=	'JCck.More.ItemX.setFromClick(this).add('.$form.');';
		} elseif ( $type == 'select' ) {
			$target				=	$link->get( 'target_fieldname', '' );
			$target				=	$target ? '\''.$target.'\'' : '';
			$link_onclick		=	'JCck.More.ItemX.setFromClick(this).select('.$target.');';
		} elseif ( $type == 'remove' ) {
			$close				=	(int)$link->get( 'close', '0' );
			$close				=	$close ? ',true' : '';
			$link_class			.=	' item_x-remove';
			$link_onclick		=	'JCck.More.ItemX.setFromClick(this,false).remove(';

			if ( $identifier_fieldname ) {
				parent::g_addProcess( 'beforeRenderContent', self::$type, $config, array( 'name'=>$field->name, 'fieldname'=>$link->get( 'identifier_fieldname', '' ), 'onclick'=>$link_onclick, 'onclick_identifier'=>$config[$identifier] ) );
			}

			$link_onclick		.=	$config[$identifier].$close.');';
		} elseif ( $type == 'process' ) {
			$processing			=	$link->get( 'processing', '' );
			$link_class			.=	' item_x-assign';
			$link_onclick		=	'JCck.More.ItemX.process('.$config['id'].','.$processing.',true);';
		} elseif ( $type == 'assign_multiple' ) {
			$link_onclick		=	'JCck.More.ItemX.assignX();';
		} elseif ( $type == 'fill' ) {
			$link_onclick		=	'JCck.More.ItemX.fill(\''.Factory::getApplication()->input->get( 'target' ).'\',';

			if ( $identifier_fieldname ) {
				parent::g_addProcess( 'beforeRenderContent', self::$type, $config, array( 'name'=>$field->name, 'fieldname'=>$link->get( 'identifier_fieldname', '' ), 'onclick'=>$link_onclick, 'onclick_identifier'=>$config[$identifier] ) );
			}

			$link_onclick		.=	$config[$identifier].',true);';			
		} else {
			$link_class			.=	' item_x-assign';
			$link_onclick		=	'JCck.More.ItemX.assign(';

			if ( $identifier_fieldname ) {
				parent::g_addProcess( 'beforeRenderContent', self::$type, $config, array( 'name'=>$field->name, 'fieldname'=>$link->get( 'identifier_fieldname', '' ), 'onclick'=>$link_onclick, 'onclick_identifier'=>$config[$identifier] ) );
			}

			$link_onclick		.=	$config[$identifier].',true);';
		}
		$link_class				=	trim( $link_class );
		
		// Confirm?
		if ( $link->get( 'confirm', '0' ) ) {
			$alert					=	$link->get( 'confirm_alert' ); // Text::_( 'COM_CCK_CONFIRM_DELETE' )
			if ( $config['doTranslation'] ) {
				if ( trim( $alert ) ) {
					$alert			=	Text::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $alert ) ) );
				}
			}
			$link_onclick		=	'if(!confirm(\''.addslashes( $alert ).'\')){return false;}else{'.$link_onclick.'}';
		}
		
		// Set
		$field->link			=	'javascript:void(0);';
		$field->link_onclick	=	$link_onclick;
		$field->link_class		=	$link_class ? $link_class : ( isset( $field->link_class ) ? $field->link_class : '' );

		if ( $link_title ) {
			if ( $link_title == '2' ) {
				$field->link_title	=	$link_title2;
			} elseif ( $link_title == '3' ) {
				$field->link_title	=	Text::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $link_title2 ) ) );
			}
			if ( !isset( $field->link_title ) ) {
				$field->link_title	=	'';
			}
		} else {
			$field->link_title		=	'';
		}
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Special Events
	
	// onCCK_Field_LinkBeforeRenderContent
	public static function onCCK_Field_LinkBeforeRenderContent( $process, &$fields, &$storages, &$config = array() )
	{
		$fieldname	=	$process['fieldname'];
		$name		=	$process['name'];

		if ( isset( $fields[$fieldname] ) ) {
			$search		=	'onclick="'.$process['onclick'].$process['onclick_identifier'];
			$replace	=	'onclick="'.$process['onclick'].'\''.$fields[$fieldname]->value.'\'';

			$fields[$name]->html	=	str_replace( $search, $replace, $fields[$name]->html );
		}
	}
}
?>