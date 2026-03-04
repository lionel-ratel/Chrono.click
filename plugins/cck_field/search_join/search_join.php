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

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

// Plugin
class plgCCK_FieldSearch_Join extends JCckPluginField
{
	protected static $type		=	'search_join';
	protected static $path;
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Construct
	
	// onCCK_FieldConstruct
	public function onCCK_FieldConstruct( $type, &$data = array() )
	{
		if ( self::$type != $type ) {
			return;
		}
		$data['display']	=	0;
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
			$data['match_mode']	=	array(
										'none'=>HTMLHelper::_( 'select.option', 'none', Text::_( 'COM_CCK_DISABLED' ) ),
										''=>HTMLHelper::_( 'select.option', '', Text::_( 'COM_CCK_ENABLED' ) )
									);

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
	}
	
	// onCCK_FieldPrepareForm
	public function onCCK_FieldPrepareForm( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
	{
		if ( self::$type != $field->type ) {
			return;
		}
	}
	
	// onCCK_FieldPrepareSearch
	public function onCCK_FieldPrepareSearch( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		parent::g_onCCK_FieldPrepareForm( $field, $config );

		// Prepare
		if ( $field->state && $field->match_mode != 'none' ) {
			$options2			=	json_decode( $field->options2 );
			if ( count( $options2->joins ) ) {
				if ( !isset( $config['joins'] ) ) {
					$config['joins']	=	array();
				}
				if ( !isset( $config['joins'][$field->stage] ) ) {
					$config['joins'][$field->stage]		=	array();
				}
				foreach ( $options2->joins as $j ) {
					$aka	=	@$j->aka;
					
					if ( isset( $j->mode ) && $j->mode ) {
						$j->query	=	JCckDevHelper::replaceLive( $j->table );
						$j->query	=	'('.$j->query.')';
					} else {
						if ( $aka ) {
							$j->aka	=	$j->table;
						}
					}
					if ( $aka ) {
						$j->table	=	$aka;
					}

					$j->and	=	JCckDevHelper::replaceLive( $j->and );

					$config['joins'][$field->stage][]	=	$j;
				}
			}
		}
		$field->form	=	'';
		$field->value	=	'';
		
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
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Render
	
	// onCCK_FieldRenderContent
	public static function onCCK_FieldRenderContent( $field, &$config = array() )
	{
	}
	
	// onCCK_FieldRenderForm
	public static function onCCK_FieldRenderForm( $field, &$config = array() )
	{
	}
}
?>