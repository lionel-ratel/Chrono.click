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

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;

// Plugin
class plgCCK_FieldSearch_Query extends JCckPluginField
{
	protected static $type		=	'search_query';
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
	
	// onCCK_FieldConstruct_SearchSearch
	public static function onCCK_FieldConstruct_SearchSearch( &$field, $style, $data = array(), &$config = array() )
	{
		$data['label']			=	null;
		$data['live']			=	null;
		$data['markup']			=	null;
		$data['markup_class']	=	null;
		$data['validation']		=	null;
		$data['variation']		=	null;
		
		if ( !isset( $config['construction']['match_mode'][self::$type] ) ) {
			$data['match_mode']['']	=	HTMLHelper::_( 'select.option', '', Text::_( 'COM_CCK_ENABLED' ) );

			$config['construction']['match_mode'][self::$type]	=	$data['match_mode'];
		} else {
			$data['match_mode']									=	$config['construction']['match_mode'][self::$type];
		}

		parent::onCCK_FieldConstruct_SearchSearch( $field, $style, $data, $config );
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_FieldPrepareContent
	public function onCCK_FieldPrepareContent( &$field, $value = '', &$config = array() )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		parent::g_onCCK_FieldPrepareContent( $field, $config );
		
		// Set
		$field->value	=	$value;
	}
	
	// onCCK_FieldPrepareForm
	public function onCCK_FieldPrepareForm( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		self::$path	=	parent::g_getPath( self::$type.'/' );
		parent::g_onCCK_FieldPrepareForm( $field, $config );
		
		// Init
		if ( count( $inherit ) ) {
			$id		=	( isset( $inherit['id'] ) && $inherit['id'] != '' ) ? $inherit['id'] : $field->name;
			$name	=	( isset( $inherit['name'] ) && $inherit['name'] != '' ) ? $inherit['name'] : $field->name;
		} else {
			$id		=	$field->name;
			$name	=	$field->name;
		}
		$value		=	( $value != '' ) ? $value : $field->defaultvalue;
		$value		=	( $value != ' ' ) ? $value : '';
		$value		=	htmlspecialchars( $value, ENT_QUOTES );
		
		// Validate
		$validate	=	'';
		if ( $config['doValidation'] > 1 ) {
			plgCCK_Field_ValidationRequired::onCCK_Field_ValidationPrepareForm( $field, $id, $config );
			parent::g_onCCK_FieldPrepareForm_Validation( $field, $id, $config, array( 'minSize'=>true ) );
			$validate	=	( count( $field->validate ) ) ? ' validate['.implode( ',', $field->validate ).']' : '';
		}
		
		// Prepare
		$class	=	'inputbox text'.$validate . ( $field->css ? ' '.$field->css : '' );
		$maxlen	=	( $field->maxlength > 0 ) ? ' maxlength="'.$field->maxlength.'"' : '';
		$attr	=	'class="'.$class.'" size="'.$field->size.'"'.$maxlen . ( $field->attributes ? ' '.$field->attributes : '' );
		$form	=	'<input type="text" id="'.$id.'" name="'.$name.'" value="'.$value.'" '.$attr.' />';
		
		// Set
		if ( ! $field->variation ) {
			$field->form	=	$form;
			if ( $field->script ) {
				parent::g_addScriptDeclaration( $field->script );
			}
		} else {
			parent::g_getDisplayVariation( $field, $field->variation, $value, $value, $form, $id, $name, '<input', '', '', $config );
		}
		$field->value	=	$value;
		
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
		parent::g_onCCK_FieldPrepareSearch( $field, $config );
		
		if ( $field->state && $field->match_mode != 'none' && $field->variation != 'clear' ) {
			// Prepare
			$options2			=	json_decode( $field->options2 );
			
			if ( $field->bool2 == 1 && $options2->query != '' ) {

				// Where [MATCH]
				$matches					=	array();
				$query						=	JCckDevHelper::replaceLive( $options2->query );
				$search						=	'#\[MATCH\](.*)\[\/MATCH\]#U';
				$storage					=	'standard';

				if ( $field->match_options != '' ) {
					$field->match_options	=	new Registry( $field->match_options );
				}

				preg_match_all( $search, $query, $matches );
				
				require_once JPATH_PLUGINS.'/cck_storage/'.$storage.'/'.$storage.'.php';

				if ( count( $matches[1] ) ) {
					foreach ( $matches[1] as $k=>$v ) {
						$def				=	explode( '||', $matches[1][$k] );
						
						if ( strpos( $def[0], ',' ) !== false ) {
							$parts			=	explode( ',', $def[0] );
						} else {
							$parts			=	array( 0=>$def[0] );
						}
						$name				=	$field->name;
						$name2				=	'';
						$sql				=	'';
						$sqls				=	array();

						if ( $def[1] != '' ) {
							if ( count( $parts ) ) {
								foreach ( $parts as $p ) {
									$sqls[]		=	JCck::callFunc_Array( 'plgCCK_Storage'.$storage, 'onCCK_StoragePrepareSearch', array( &$field, $field->match_mode, $def[1], $name, $name2, $p, '', array(), &$config ) );
								}
							}
							if ( count( $sqls ) ) {
								$sqls		=	array_diff( $sqls, array( "" ) );
								$sql		=	'('.implode( ' OR ', $sqls ).')';
							}
						}
						if ( $sql == '' ) {
							$sql			=	'()';
						}
						$query				=	str_replace( $matches[0][$k], $sql, $query );
					}
				}

				// Order By [ORDER]
				$matches					=	array();
				$search						=	'#\[ORDER\](.*)\[\/ORDER\]#';

				preg_match_all( $search, $query, $matches );
				
				$count	=	count( $matches[1] );

				if ( $count ) {
					foreach ( $matches[1] as $k=>$v ) {
						$def				=	explode( '||', $matches[1][$k] );
						$sql				=	'';

						if ( $def[0] ) {
							if ( strpos( $def[0], ':' ) !== false ) {
								$parts		=	explode( ':', $def[0] );
								$sql		=	$parts[0].' '.strtoupper( $parts[1] );
							} else {
								$sql		=	$def[0];
							}
						} else {
							$count--;
						}
						$query				=	str_replace( $matches[0][$k], $sql, $query );
					}
					if ( $count == 0 ) {
						$query				=	str_replace( 'ORDER BY ', '', $query );
					}
				}

				// Finalize
				$config['doQuery']			=	false;
				$config['query']			=	str_replace( 'AND ()', '', $query );
				$config['storage_location']	=	'free';
			} else if ( $field->bool2 == 0 ) {
				if ( $options2->query_select != '' ) {
					$config['query_parts']['select'][]		=	JCckDevHelper::replaceLive( $options2->query_select );
				}
				if ( $options2->query_group != '' ) {
					$config['query_parts']['group'][]		=	JCckDevHelper::replaceLive( $options2->query_group );
				}
				if ( $options2->query_having != '' ) {
					$config['query_parts']['having'][]		=	JCckDevHelper::replaceLive( $options2->query_having );
				}
				if ( @$options2->query_where != '' ) {
					$config['query_parts']['where'][]		=	JCckDevHelper::replaceLive( $options2->query_where );
				}
				if ( @$options2->query_order_by != '' ) {
					$config['query_parts']['order_by'][]	=	JCckDevHelper::replaceLive( $options2->query_order_by );
				}
			}
			if ( isset( $options2->query_variables ) ) {
				$config['query_variables'][]				=	$options2->query_variables;
			}
		}

		// Set
		$field->form		=	'';
		$field->value		=	'';
		
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
}
?>