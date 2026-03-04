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
use Joomla\CMS\Router\Route;

// Plugin
class plgCCK_FieldCck_List extends JCckPluginField
{
	protected static $type		=	'cck_list';
	protected static $path;
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Construct
	
	// onCCK_FieldConstruct
	public function onCCK_FieldConstruct( $type, &$data = array() )
	{
		if ( self::$type != $type ) {
			return;
		}
		parent::g_onCCK_FieldConstruct( $data );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_FieldPrepareContent
	public function onCCK_FieldPrepareContent( &$field, $value = '', &$config = array() )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		$field->priority	=	3;

		parent::g_onCCK_FieldPrepareContent( $field, $config );
		
		// Prepare
		if ( $field->state ) {
			$options2		=	JCckDev::fromJSON( $field->options2 );
			$pagination		=	array(
									'callback'=>(string)( ( isset( $options2['callback_pagination'] ) ) ? $options2['callback_pagination'] : '' ),
									'class'=>(string)( ( isset( $options2['class_pagination'] ) && $options2['class_pagination'] != '' ) ? $options2['class_pagination'] : 'pagination' ),
									'show_more'=>(int)( ( isset( $options2['show_more'] ) ) ? $options2['show_more'] : '0' ),
									'show_link_more'=>(int)( ( isset( $options2['show_link_more'] ) ) ? $options2['show_link_more'] : '0' ),
									'link_more_class'=>(string)( ( isset( $options2['link_more_class'] ) ) ? $options2['link_more_class'] : '' ),
									'link_more_text'=>(string)( ( isset( $options2['link_more_text'] ) ) ? $options2['link_more_text'] : '' ),
									'link_more_variables'=>(string)( ( isset( $options2['link_more_variables'] ) ) ? $options2['link_more_variables'] : '' ),
									'pagination'=>(string)( ( isset( $options2['pagination'] ) ) ? $options2['pagination'] : '' ),
									'show'=>$field->bool3
								);
			$list			=	array(
									'show_form'=>( ( isset( $options2['show_form'] ) ) ? $options2['show_form'] : '' )
								);
			$fragment		=	array(
									'load'=>$field->bool6,
									'link'=>(string)( ( isset( $options2['link_resource'] ) ) ? $options2['link_resource'] : '0' ),
									'link_fieldname'=>(string)( ( isset( $options2['link_resource_fieldname'] ) ) ? $options2['link_resource_fieldname'] : '' ),
									'json'=>(string)( ( isset( $options2['json_resource'] ) ) ? $options2['json_resource'] : '' )
								);

			if ( $field->options ) {
				$options	=	explode( '||', $field->options );
			} else {
				$options	=	array();
			}

			parent::g_addProcess( 'beforeRenderContent', self::$type, $config, array( 'name'=>$field->name, 'fieldnames'=>$options, 'pagination'=>$pagination, 'list'=>$list, 'fragment'=>$fragment ) );
		}

		// Set
		$field->value	=	'';
	}
	
	// onCCK_FieldPrepareForm
	public function onCCK_FieldPrepareForm( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		self::$path	=	parent::g_getPath( self::$type.'/' );
		parent::g_onCCK_FieldPrepareForm( $field, $config );
		
		// Prepare
		if ( $field->state ) {
			$options2		=	JCckDev::fromJSON( $field->options2 );
			$pagination		=	array(
								'callback'=>(string)( ( isset( $options2['callback_pagination'] ) ) ? $options2['callback_pagination'] : '' ),
								'class'=>(string)( ( isset( $options2['class_pagination'] ) && $options2['class_pagination'] != '' ) ? $options2['class_pagination'] : 'pagination' ),
								'show_more'=>(int)( ( isset( $options2['show_more'] ) ) ? $options2['show_more'] : '0' ),
								'show_link_more'=>(int)( ( isset( $options2['show_link_more'] ) ) ? $options2['show_link_more'] : '0' ),
								'link_more_class'=>(string)( ( isset( $options2['link_more_class'] ) ) ? $options2['link_more_class'] : '' ),
								'link_more_text'=>(string)( ( isset( $options2['link_more_text'] ) ) ? $options2['link_more_text'] : '' ),
								'link_more_variables'=>(string)( ( isset( $options2['link_more_variables'] ) ) ? $options2['link_more_variables'] : '' ),
								'pagination'=>(string)( ( isset( $options2['pagination'] ) ) ? $options2['pagination'] : '' ),
								'show'=>$field->bool3
							);
			$list			=	array(
									'show_form'=>( ( isset( $options2['show_form'] ) ) ? $options2['show_form'] : '' )
								);
			$fragment		=	array(
									'load'=>$field->bool6,
									'link'=>(string)( ( isset( $options2['link_resource'] ) ) ? $options2['link_resource'] : '0' ),
									'json'=>(string)( ( isset( $options2['json_resource'] ) ) ? $options2['json_resource'] : '' )
								);
			
			if ( $field->options ) {
				$options	=	explode( '||', $field->options );
			} else {
				$options	=	array();
			}

			parent::g_addProcess( 'beforeRenderForm', self::$type, $config, array( 'name'=>$field->name, 'fieldnames'=>$options, 'pagination'=>$pagination, 'list'=>$list, 'fragment'=>$fragment ) );
		}
				
		// Set
		$field->form	=	'';
		$field->value	=	'';
		
		// Return
		if ( $return === true ) {
			return $field;
		}
	}
	
	// onCCK_FieldPrepareSearch
	public function onCCK_FieldPrepareSearch( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		
		// Prepare
		self::onCCK_FieldPrepareForm( $field, $value, $config, $inherit, $return );
		
		// Return
		if ( $return === true ) {
			return $field;
		}
	}
	
	// onCCK_FieldPrepareStore
	public function onCCK_FieldPrepareStore( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		
		// Init
		if ( count( $inherit ) ) {
			$name	=	( isset( $inherit['name'] ) && $inherit['name'] != '' ) ? $inherit['name'] : $field->name;
		} else {
			$name	=	$field->name;
		}
		
		// Validate
		parent::g_onCCK_FieldPrepareStore_Validation( $field, $name, $value, $config );
		
		// Set or Return
		if ( $return === true ) {
			return $value;
		}
		$field->value	=	$value;
		parent::g_onCCK_FieldPrepareStore( $field, $name, $value, $config );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Render
	
	// onCCK_FieldRenderContent
	public static function onCCK_FieldRenderContent( $field, &$config = array() )
	{
		return parent::g_onCCK_FieldRenderContent( $field );
	}
	
	// onCCK_FieldRenderForm
	public static function onCCK_FieldRenderForm( $field, &$config = array() )
	{
		return parent::g_onCCK_FieldRenderForm( $field );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Special Events
	
	// onCCK_FieldBeforeRenderContent
	public static function onCCK_FieldBeforeRenderContent( $process, &$fields, &$storages, &$config = array() )
	{
		$name	=	$process['name'];

		if ( !$fields[$name]->state ) {
			return;
		}

		// Prepare
		$lives	=	self::_prepare( $process, $fields );

		if ( $process['pagination']['link_more_variables'] != '' ) {
			$process['pagination']['link_more_variables']	=	self::_prepareLink( $process['pagination']['link_more_variables'], $fields );
		}

		if ( (int)$process['fragment']['link'] && (int)$process['fragment']['link'] == -2
		  && $process['fragment']['link_fieldname'] && isset( $fields[$process['fragment']['link_fieldname']] ) && $fields[$process['fragment']['link_fieldname']]->value ) {
			$process['fragment']['link']	=	$fields[$process['fragment']['link_fieldname']]->value;
		} else {
			$process['fragment']['link']	=	0;
		}

		// Set
		$fields[$name]->value	=	self::_render( $fields[$name], 'html', $lives, $config, $process['pagination'], $process['list'], $process['fragment'] );
	}
	
	// onCCK_FieldBeforeRenderForm
	public static function onCCK_FieldBeforeRenderForm( $process, &$fields, &$storages, &$config = array() )
	{
		$name	=	$process['name'];

		if ( !$fields[$name]->state ) {
			return;
		}

		// Prepare
		$lives	=	self::_prepare( $process, $fields );

		if ( $process['pagination']['link_more_variables'] != '' ) {
			$process['pagination']['link_more_variables']	=	self::_prepareLink( $process['pagination']['link_more_variables'], $fields );
		}

		// Set
		$fields[$name]->form	=	self::_render( $fields[$name], 'form', $lives, $config, $process['pagination'], $process['list'], $process['fragment'] );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Stuff & Script
	
	// _prepare
	protected static function _prepare( $process, $fields )
	{
		$lives	=	array();

		if ( count( $process['fieldnames'] ) ) {
			foreach ( $process['fieldnames'] as $field ) {
				if ( strpos( $field, '=' ) !== false ) {
					$f	=	explode( '=', $field );
				} else {
					$f	=	array( 0=>$field, 1=>$field );
				}
				if ( isset( $fields[$f[1]] ) ) {
					$v	=	$fields[$f[1]]->value;
				} elseif ( is_numeric( $f[1] ) ) {
					$v	=	(string)$f[1];
				} else {
					$len	=	strlen( $f[1] );
					
					if ( $f[1] != '' && $f[1][0] == '"' && $f[1][($len-1)] == '"' ) {
						$v	=	substr( $f[1], 1, -1 );
					} else {
						$v	=	'';
					}
				}
				if ( is_array( $v ) ) {
					$v	=	implode( ' ', $v );
				}
				$lives[$f[0]]	=	$v;
			}
		}

		return $lives;
	}

	// _prepareLink
	protected static function _prepareLink( $show_more_vars, $fields )
	{
		$show_more_vars	=	JCckDevHelper::replaceLive( $show_more_vars );
			
		if ( $show_more_vars != '' && strpos( $show_more_vars, '$cck->get' ) !== false ) {
			$matches	=	'';
			$search		=	'#\$cck\->get([a-zA-Z0-9_]*)\( ?\'([a-zA-Z0-9_,\[\]]*)\' ?\)(;)?#';
			preg_match_all( $search, $show_more_vars, $matches );
			if ( count( $matches[1] ) ) {
				if ( count( $matches[1] ) ) {
					foreach ( $matches[1] as $k=>$v ) {
						$fieldname		=	$matches[2][$k];
						$target			=	strtolower( $v );
						$value			=	'';
						$pos			=	strpos( $target, 'safe' );

						if ( $pos !== false && $pos == 0 ) {
							$target		=	substr( $target, 4 );
							$value		=	$fields[$fieldname]->$target;
							$value		=	JCckDev::toSafeID( $value );
						} else {
							$value		=	$fields[$fieldname]->$target;
						}

						$show_more_vars	=	str_replace( $matches[0][$k], $value, $show_more_vars );
					}
				}
			}
		}
		
		return $show_more_vars;
	}

	// _render
	protected static function _render( $field, $target, $lives, $config, $pagination2, $list, $fragment )
	{
		/*
		$main_config				=	$config;
		$main_field					=	$field;
		*/

		$app		=	Factory::getApplication();
		$class_sfx	=	'';
		$uniqId		=	'f'.$field->id;
		$formId		=	'seblod_list_'.$uniqId;
		
		$option		=	$app->input->get( 'option', '' );
		$view		=	'';
		$preconfig	=	array(
							'action'=>'',
							'auto_redirect'=>0,
							'caller'=>$field->name.'.'.$config['pk'],
							'client'=>'search',
							'formId'=>$formId,
							'idx'=>$field->id,
							'itemId'=>$app->input->getInt( 'Itemid', 0 ),
							'limitend'=>$pagination2['pagination'],
							'limit2'=>$field->bool4,
							'ordering'=>'',
							'ordering2'=>'',
							'search'=>$field->extended,
							'search2'=>$field->location,
							'show_form'=>$list['show_form'] != '' ? $list['show_form'] : '0',
							'submit'=>'JCck.Core.submit_'.$uniqId,
							'task'=>'search',
						);

		$limitstart		=	(int)$field->bool5;
		$offset			=	0;

		if ( $limitstart >= 1 ) {
			$limitstart	=	$limitstart - 1;
			$offset		=	$limitstart;
		} else {
			$limitstart	=	-1;
		}
		
		$live			=	'';
		$order_by		=	'';
		$pagination		=	$pagination2['show'];
		$variation		=	'';

		if ( JCck::is( '4' ) ) {
			$raw_rendering	=	$field->bool == -1 ? JCck::getConfig_Param( 'raw_rendering', '0' ) : $field->bool;
		} else {
			$raw_rendering	=	$field->bool;
		}
		
		// Check
		if ( $limitstart == -1 && (int)$field->bool4 > 0 ) {
			$limitstart	=	0;
		}
		if ( $limitstart == -1 && ( $pagination == 2 || $pagination == 8 ) ) {
			$limitstart	=	0;
		}
		
		// Prepare
		jimport( 'cck.base.list.list' );
		include JPATH_SITE.'/libraries/cck/base/list/list_inc.php';

		$context		=	array();
		
		if ( isset( $config['context'] ) ) {
			$context	=	$config['context'];
		}
		$context['Itemid']		=	$preconfig['itemId'];
		$context['view']		=	Factory::getApplication()->input->get( 'view' );
		$context['referrer']	=	$preconfig['caller'];

		$callback_pagination	=	$pagination2['callback'];
		$class_pagination		=	$pagination2['class'];
		$show_pagination		=	$pagination2['show'];

		// Prepare More
		$show_more			=	$pagination2['show_more'];
		$show_link_more		=	$pagination2['show_link_more'];
		$show_more_class	=	$pagination2['link_more_class'];
		$show_more_class	=	( $show_more_class ) ? ' class="'.$show_more_class.'"' : '';
		$show_more_text		=	$pagination2['link_more_text'];
		
		if ( $show_more_text == '' ) {
			$show_more_text	=	Text::_( 'COM_CCK_VIEW_ALL' );
		} elseif ( JCck::getConfig_Param( 'language_jtext', 1 ) ) {
			$show_more_text	=	Text::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $show_more_text ) ) );
		}
		$show_more_link		=	'';

		if ( ( $show_more == 1 || ( $show_more == 2 && $total ) || ( $show_more == 3 && $total_items > $preconfig['limit2'] ) ) && $show_link_more ) {
			$show_more_link	=	'index.php?Itemid='.$show_link_more;
			$show_more_link	=	Route::_( $show_more_link );
			$show_more_vars	=	$pagination2['link_more_variables'];

			if ( $show_more_vars != '' ) {
				$show_more_link	.=	( strpos( $show_more_link, '?' ) !== false ) ? '&'.$show_more_vars : '?'.$show_more_vars;
			}
		}

		static $loaded_resource	=	0;

		$load_resource	=	$fragment['load'];
		$link_resource	=	$fragment['link'];
		$json_resource	=	$fragment['json'];

		if ( $load_resource ) {
			$loaded_resource++;
		}
		if ( $loaded_resource > 1 ) {
			$load_resource	=	0;
		}

		ob_start();
		include __DIR__.'/tmpl/render.php';
		$buffer			=	ob_get_clean();

		if ( $list['show_form'] == '' ) {
			if ( isset( $config['formWrapper'] ) && $config['formWrapper'] ) {
				$buffer		=	'<form action="" method="post" id="'.$config['formId'].'">'.$buffer.'</form>';
			}
		} elseif ( $list['show_form'] == '1' ) {
			if ( isset( $config['formWrapper'] ) && $config['formWrapper'] ) {
				$buffer	=	'<form action="" method="post" id="'.$config['formId'].'">'.$form.$buffer.'</form>';
			} else {
				$buffer	=	'<form action="" method="post" id="'.$config['formId'].'">'.$form.'</form>'.$buffer;
			}
		}

		/*
		$config	=	$main_config;
		$field	=	$main_field;
		*/

		return $buffer;
	}
}
?>