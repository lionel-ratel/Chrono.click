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
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;

// Plugin
class plgCCK_FieldForm_Action extends JCckPluginField
{
	protected static $type		=	'form_action';
	protected static $path;
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Construct
	
	// onCCK_FieldConstruct
	public function onCCK_FieldConstruct( $type, &$data = array() )
	{
		if ( self::$type != $type ) {
			return;
		}
		parent::g_onCCK_FieldConstruct( $data );
		$data['display']	=	0;
	}
	
	// onCCK_FieldConstruct_SearchSearch
	public static function onCCK_FieldConstruct_SearchSearch( &$field, $style, $data = array(), &$config = array() )
	{
		$data['conditional']	=	null;
		$data['label']			=	null;
		$data['match_mode']		=	null;
		$data['live']			=	null;
		$data['markup']			=	null;
		$data['markup_class']	=	null;
		$data['validation']		=	null;
		$data['variation']		=	null;

		parent::onCCK_FieldConstruct_SearchSearch( $field, $style, $data, $config );
	}

	// onCCK_FieldConstruct_TypeForm
	public static function onCCK_FieldConstruct_TypeForm( &$field, $style, $data = array(), &$config = array() )
	{
		$data['computation']	=	null;
		$data['conditional']	=	null;
		$data['label']			=	null;
		$data['live']			=	null;
		$data['markup']			=	null;
		$data['markup_class']	=	null;
		$data['validation']		=	null;
		$data['variation']		=	null;
		
		parent::onCCK_FieldConstruct_TypeForm( $field, $style, $data, $config );
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_FieldPrepareContent
	public function onCCK_FieldPrepareContent( &$field, $value = '', &$config = array() )
	{
		if ( self::$type != $field->type ) {
			return;
		}

		if ( $field->bool4 ) {
			$formId	=	$config['formId'];
			$js	=	'';

			if ( (int)JCck::getConfig_Param( 'validation', '3' ) > 1 && $config['validation'] != '' ) {
				$config['validation']	=	( count( $config['validation'] ) ) ? implode( ',', $config['validation'] ) : '';
				$opts					=	 JCckDatabase::loadResult( 'SELECT options FROM #__cck_core_searchs WHERE name ="'.JCckDatabase::escape( (string)$config['type'] ).'"' );
				$options				=	new Registry;

				if ( $opts ) {
					$options->loadString( $opts );
				}

				if ( $config['validation'] !== '' ) {
					JCckDev::addValidation( $config['validation'], $options, $formId );
				}
				
				// $js	=	'if (jQuery("#'.$formId.'").validationEngine("validate",task) === true) { if (jQuery("#'.$formId.'").isStillReady() === true) { jQuery("#'.$formId.' input[name=\'config[unique]\']").val("'.$formId.'"); JCck.Core.submitForm("save", document.getElementById("'.$formId.'")); } }';
			} else {
				// ??
			}
			if ( $js ) {
				$html	.=	'<script type="text/javascript">'
						.	$config['submit'].' = function(task) { '.$js.' }'
						.	'</script>';
			}

			$html	.=	'</form>';

			if ( isset( $config['formWrapper'] ) && $config['formWrapper'] == 1 ) {
				$config['formWrapper']	=	2;
			}
		} else {
			$app		=	Factory::getApplication();
			$options2	=	new Registry( $field->options2 );

			$config['doValidation']			=	(int)JCck::getConfig_Param( 'validation', '3' );
			$config['validate']				=	array();
			$config['validation']			=	array();
			$config['validation_options']	=	array();

			// Validation
			if ( $config['doValidation'] > 1 ) {
				Factory::getLanguage()->load( 'plg_cck_field_validation_required', JPATH_ADMINISTRATOR, null, false, true );

				require_once JPATH_PLUGINS.'/cck_field_validation/required/required.php';
			}

			$action			=	$options2->get( 'action', '' );
			$autocomplete	=	$options2->get( 'autocomplete', 0 ) ? 'on' : 'off';
			$class			=	( $field->css ? ' class="'.$field->css.'"' : '' );
			$enctype		=	$options2->get( 'enctype', '' );
			$itemId			=	$options2->get( 'itemid', '' );
			$method			=	$options2->get( 'method', 'get' );
			$target			=	$options2->get( 'target', '_self' );

			if ( $field->bool ) {
				//
			} else {
				if ( $itemId ) {
					if ( (int)$itemId == -1 ) {
						$menu	=	$app->getMenu()->getActive();

						if ( is_object( $menu ) ) {
							$itemId	=	$menu->parent_id;
						}
					}
					$action		=	 Route::_( 'index.php?Itemid='.$itemId );
				} else {
					$action		=	 Route::_( 'index.php?option=com_cck' );
				}
			}

			$enctype		=	( $enctype && $method == 'post' ) ? ' enctype="'.$enctype.'"' : '';
			$target			=	( $target ) ? ' target="'.$target.'"' : '';
			$html			=	'<form'.$enctype.$target.' action="'.$action.'" autocomplete="'.$autocomplete.'" method="'.$method.'" id="'.$config['formId'].'" name="'.$config['formId'].'"'.$class.'>';

			if ( isset( $config['formWrapper'] ) && $config['formWrapper'] == 2 ) {
				$config['formWrapper']	=	1;
			}
		}

		// Set
		$field->display	=	1;
		$field->html	=	$html;
		$field->value	=	'';
		$field->label	=	'';
	}
	
	// onCCK_FieldPrepareForm
	public function onCCK_FieldPrepareForm( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		self::$path	=	parent::g_getPath( self::$type.'/' );
		parent::g_onCCK_FieldPrepareForm( $field, $config );
		
		if ( $field->state ) {
			// Init
			if ( count( $inherit ) ) {
				$id		=	( isset( $inherit['id'] ) && $inherit['id'] != '' ) ? $inherit['id'] : $field->name;
				$name	=	( isset( $inherit['name'] ) && $inherit['name'] != '' ) ? $inherit['name'] : $field->name;
			} else {
				$id		=	$field->name;
				$name	=	$field->name;
			}

			if ( (string)$field->variation != 'hidden' ) {
				$app			=	Factory::getApplication();
				$options2		=	new Registry( $field->options2 );

				$class			=	( $field->css ? ' class="'.$field->css.'"' : '' );
				$autocomplete	=	$options2->get( 'autocomplete', 0 ) ? 'on' : 'off';
				$action			=	$options2->get( 'action', '' );
				$canonical		=	(int)$options2->get( 'canonical', 0 );
				$enctype		=	$options2->get( 'enctype', '' );
				$itemId			=	$options2->get( 'itemid', '' );
				$method			=	$options2->get( 'method', 'get' );
				$target			=	$options2->get( 'target', '_self' );
				
				if ( $field->bool ) {
					//
				} else {
					if ( $itemId ) {
						$action		=	Route::_( 'index.php?Itemid='.$itemId );
					} else {
						if ( $app->input->get( 'option' ) === 'com_cck' ) {
							$action	=	htmlspecialchars( Uri::getInstance()->getPath() );
						} else {
							$action	=	Route::_( 'index.php?option=com_cck' );
						}
					}
				}
				if ( $field->bool2 == '2' ) {
					$action			.=	'#'.$config['formId'];
				} elseif ( $field->bool2 == '1' ) {
					$action			.=	$options2->get( 'anchor', '' );
				}
		 		$enctype			=	( $enctype && $method == 'post' ) ? ' enctype="'.$enctype.'"' : '';
				$target				=	( $target ) ? ' target="'.$target.'"' : '';
				$config['action']	=	'<form'.$enctype.$target.' action="'.$action.'" autocomplete="'.$autocomplete.'" method="'.$method.'" id="'.$config['formId'].'" name="'.$config['formId'].'"'.$class.'>'; // accept-charset="ISO-8859-1"
				$config['core']		=	(int)$field->bool3;
				
				if ( $canonical === 1 || ( $canonical === 2 && $app->input->get( 'view', '' ) == 'form' ) ) {
					$url	=	Uri::current();
					
					Factory::getDocument()->addHeadLink( $url, 'canonical' );

					$app->cck_canonical_url	=	$url;
					$app->cck_canonical		=	true;
				}
				if ( $field->options ) {
					parent::g_addProcess( 'beforeRenderForm', self::$type, $config, array( 'name'=>$name, 'options'=>$field->options ) );
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
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Render
	
	// onCCK_FieldRenderContent
	public static function onCCK_FieldRenderContent( $field, &$config = array() )
	{
		$field->markup	=	'none';
		
		return parent::g_onCCK_FieldRenderContent( $field, 'html' );
	}
	
	// onCCK_FieldRenderForm
	public static function onCCK_FieldRenderForm( $field, &$config = array() )
	{
		return parent::g_onCCK_FieldRenderForm( $field );
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Special Events
	
	// onCCK_FieldBeforeRenderForm
	public static function onCCK_FieldBeforeRenderForm( $process, &$fields, &$storages, &$config = array() )
	{
		$options	=	$process['options'];
		$opts		=	explode( '||', $options );
		
		if ( count( $opts ) ) {
			foreach ( $opts as $opt ) {
				$o	=	explode( '=', $opt );
				
				if ( isset( $fields[$o[0]] ) ) {
					$fields[$o[0]]->form	=	str_replace( 'name="'.$o[0].'"', 'name="'.$o[1].'"', $fields[$o[0]]->form );
					$fields[$o[0]]->form	=	str_replace( 'name="'.$o[0].'[]"', 'name="'.$o[1].'"', $fields[$o[0]]->form );
				}
			}
		}
	}
}
?>