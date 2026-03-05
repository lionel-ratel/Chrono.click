<?php
namespace YooSeblod\Integration;

defined( '_JEXEC' ) or die;

class YooUikit
{
	protected static $_types  =   array(
        'button_free'=>array( 'add'=>'uk-button|uk-button-default', 'remove'=>'button|btn' ),
        'button_submit'=>array( 'add'=>'uk-button|uk-button-default', 'remove'=>'button|btn' ),
        'checkbox'=>array( 'add'=>'', 'remove'=>'', 'function'=>1 ),					// Ok
        'colorpicker'=>array( 'add'=>'uk-input', 'remove'=>'inputbox|text' ),			// Ok
        'email'=>array( 'add'=>'uk-input', 'remove'=>'inputbox|text' ),					// Ok
        'email2'=>array( 'add'=>'uk-input', 'remove'=>'inputbox|text' ),				// Ok
        'jform_calendar'=>array( 'add'=>'', 'remove'=>'', 'function'=>1 ),				// Ok
        'jform_accesslevel'=>array( 'add'=>'uk-select', 'remove'=>'inputbox|select|form-select' ),	
        'jform_menuitem'=>array( 'add'=>'uk-select', 'remove'=>'inputbox|select' ),		// Ok
        'jform_templatestyle'=>array( 'add'=>'uk-select', 'remove'=>'inputbox|select', 'function'=>1 ),	// Ok
        'jform_timezone'=>array( 'add'=>'uk-select', 'remove'=>'inputbox|select' ),		// OK
        'jform_usergroups'=>array( 'add'=>'', 'remove'=>'', 'function'=>1 ),			// Ok
        'password'=>array( 'add'=>'uk-input', 'remove'=>'inputbox|password' ),			// Ok
        'radio'=>array( 'add'=>'', 'remove'=>'', 'function'=>1 ),						// Ok
        'select_dynamic'=>array( 'add'=>'uk-select', 'remove'=>'inputbox|select' ),		// Ok
        'select_multiple'=>array( 'add'=>'uk-select', 'remove'=>'inputbox|select' ),	// Ok
        'select_numeric'=>array( 'add'=>'uk-select', 'remove'=>'inputbox|select' ),		// Ok
        'select_simple'=>array( 'add'=>'uk-select', 'remove'=>'inputbox|select' ),		// Ok
        'search_generic'=>array( 'add'=>'uk-input', 'remove'=>'inputbox|text' ),
        'search_ordering'=>array( 'add'=>'uk-select', 'remove'=>'inputbox|select' ),
        'text'=>array( 'add'=>'uk-input', 'remove'=>'inputbox|text' ), 					// Ok
        'textarea'=>array( 'add'=>'uk-textarea', 'remove'=>'inputbox|textarea' ),		// Ok
        'upload_file2'=>array( 'add'=>'', 'remove'=>'', 'function'=>1 ),				// Ok - To do better
        'upload_image2'=>array( 'add'=>'', 'remove'=>'', 'function'=>1 ),				// Ok - To do better
        'wysiwyg_editor'=>array( 'add'=>'', 'remove'=>'', 'function'=>1 )				// Ok
    );

	// initField
	public static function initField( $field )
	{
		if ( !isset( self::$_types[$field->type] ) ) {
		    return;
		}

		$css        =   explode( ' ', $field->css );

		foreach ( $css as $key => $value ) {
			if ( substr( $value, 0, 2 ) == 'o-' ) {
				unset( $css[$key] );
			}
		}

		$css_type   =   self::$_types[$field->type]['add'];

		if ( strpos( $css_type, '|' ) !== false ) {
		    $css_type   =   explode( '|', $css_type );
		} else {
		    $css_type   =   array( 0=>$css_type );
		}

		foreach ( $css_type as $code ) {
		    if ( !in_array( $code, $css ) ) {
		        $css[]  =   $code;
		    }
		}

		if ( count( $css ) ) {
			$css	=	array_unique( $css );
		}

		$css	=	trim( implode( ' ', $css ) );

	    \JCckDatabase::execute( 'UPDATE #__cck_core_fields SET css="'.\JCckDatabase::escape( $css ).'" WHERE id='.(int)$field->id );
	}

	// getModalHtml
	public static function getModalHtml( $selector, $content, $options = array() )
	{
		$attr			=	'';
		$button_class	=	'';
		$class			=	array( 'uk-flex-top' );
		$close			=	true;
		$title			=	'';

		if ( is_array( $options ) && !empty( $options ) ) {
			if ( isset( $options['modal_title'] ) && $options['modal_title'] !== '' ) {
				$title	=	$options['modal_title'];
			}
			if ( isset( $options['modal_class'] ) && $options['modal_class'] !== '' ) {
				$class	=	explode( ' ', $options['modal_class'] );
			}
			if ( isset( $options['modal_close'] ) && !(int)$options['modal_close'] ) {
				$close	=	false;
			}
			if ( isset( $options['modal_center'] ) && (int)$options['modal_center'] ) {
				$class[]	=	'uk-margin-auto-vertical';
			}
			if ( isset( $options['modal_overflow'] ) && (int)$options['modal_overflow'] ) {
				$attr	=	' uk-overflow-auto';
			}
			if ( isset( $options['modal_size'] ) && $options['modal_size'] !== '' ) {
				if ( $options['modal_size'] === 'expand' ) {
					$class[]	=	'uk-modal-container';
				} elseif ( $options['modal_size'] === 'full' ) {
					$class[]		=	'uk-modal-full';
					$button_class	=	' uk-modal-close-full';
				}
			}
		}

		$class	=	implode( ' ', $class );

		$html	=	'<div id="'.$selector.'" class="'.$class.'" uk-modal'.$attr.'>'
				.		'<div class="uk-modal-dialog uk-modal-body">';

		if ( $title !== '' ) {
			$html	.=	'<h2 class="uk-modal-title">'.$title.'</h2>';
		}

		if ( $close ) {
			$html	.=	'<button class="uk-modal-close-default'.$button_class.'" type="button" uk-close></button>';
		}

		$html	.=	'<form class="lionel">'.$content.'</form>'
				.	'</div></div>';

		return $html;
	}

	// markupField
	public static function markupField( &$field )
	{
		$type	=	$field->type;

		if ( !isset( self::$_types[$type] ) ) {
			return;
		}

		preg_match( '/class="(.*?)"/', $field->form, $matches );

		if ( isset( $matches[1] ) ) {
			$current	=	trim( $matches[1] );
			$replace	=	$matches[0];

			if ( $current != '' ) {
				$current	=	str_replace( '  ', ' ', $current );
				$current	=	array_flip( explode( ' ', $current ) );
			} else {
				$current	=	array();
			}
		} else {
			$current	=	array();

			preg_match( '/<(.*?) /', $field->form, $matches );

			if ( isset( $matches[0] ) ) {
				$replace	=	$matches[0];
			} else {
				$replace	=	'';
			}
		}

		// Remove
		if ( !empty( $current ) && self::$_types[$type]['remove'] != '' ) {
			$remove	=	explode( '|', self::$_types[$type]['remove'] );

			foreach ( $remove as $class ) {
				if ( isset( $current[$class] ) ) {
					unset( $current[$class] );
				}
			}
		}

		$current	=	array_flip( $current );

		// Add
		if ( self::$_types[$type]['add'] != '' ) {
			$add		=	explode( '|', self::$_types[$type]['add'] );
			$current	=	array_merge( $current, $add );
		}

		$current	=	array_unique( $current );

		if ( $replace != '' ) {
			if ( strpos( $replace, 'class' ) !== false ) {
				$current	=	'class="'.implode( ' ', $current ).'"';
			} else {
				$current	=	$replace.' class="'.implode( ' ', $current ).'" ';
			}

			$field->form	=	str_replace( $replace, $current, $field->form );
		}

		// Function
		if ( self::$_types[$type]['function'] ) {
			$function	=	'set'.ucfirst( $field->type );

			self::{$function}( $field );
		}

		$field->form	=	preg_replace( '/ size="(.*?)"/s', '', $field->form );
	}

	// setCheckbox
	protected static function setCheckbox( &$field )
	{
		self::setFieldOptions( $field, 'checkbox' );
	}

	// setFieldOptions
	protected static function setFieldOptions( &$field, $type = 'radio' )
	{
		preg_match_all( '/<input (.*?)<label (.*?)<\/label>/s', $field->form, $matches );

		$html	=	array();

		if ( isset( $matches[1] ) && count( $matches[1] ) ) {
			foreach ( $matches[1] as $key => $value ) {
				$value	=	str_replace( 'class="'.$type, 'class="uk-'.$type, $value );
				$tmp	=	explode( '>', $matches[2][$key] );
				$html[]	=	'<label><input '.$value.' '.$tmp[1].'</label>';
			}
		}

		$is_filter	=	( strpos( $field->form, 'is-filter' ) !== false ) ? ' is-filter' : '';

		if ( (int)$field->bool ) {
			$nb		=	(int)$field->bool2;

			if ( $nb > 1 ) {
				$columns	=	array();
				$i			=	-1;
				$per		=	ceil( count( $html ) / $nb );

				foreach ( $html as $key => $value ) {
					if ( $key % $per == 0 ) {
						$i	==	$i++;
					}

					$columns[$i][]	=	$value;	
				}

				$html	=	'';

				foreach ( $columns as $key => $value ) {
					$html	.=	'<div>'.implode( '<br>', $value ).'</div>';
				}
				
				$field->form	=	'<fieldset id="'.$field->name.'" class="uk-field-desc'.$is_filter.'" uk-grid>'.$html.'</fieldset>';
			} else {
				$html			=	implode( '<br>', $html );
				$field->form	=	'<fieldset id="'.$field->name.'" class="uk-field-desc'.$is_filter.'">'.$html.'</fieldset>';
			}
		} else {
			$html			=	implode( '', $html );
			$field->form	=	'<fieldset id="'.$field->name.'" class="uk-grid-small uk-child-width-auto uk-grid uk-field-desc'.$is_filter.'">'.$html.'</fieldset>';
		}
	}	

	// setJform_calendar
	protected static function setJform_calendar( &$field )
	{
		$field->form	=	str_replace( 
								array( 'input-append', 'inputbox text', 'hasTooltip' ),
								array( 'uk-flex', 'uk-input', 'uk-button' ),
								$field->form
							);
	}

	// setJform_templatestyle
	protected static function setJform_templatestyle( &$field )
	{
		$field->form	=	str_replace( 
								array( 'style="width: 150px"' ),
								array( '' ),
								$field->form
							);
	}

	// setJform_usergroups
	protected static function setJform_usergroups( &$field )
	{
		$html	=	str_replace( 
							array( 'class="checkbox"', 'input class="' ), 
							array( '', 'input class="uk-checkbox ' ), 
							$field->form 
						);

		$html	=	substr( $html, 0, strlen( $html ) - 4 );

		$field->form	=	'<fieldset class="uk-field-desc"'.substr( $html, 5 ).'fieldset>';
	}

	// setRadio
	protected static function setRadio( &$field )
	{
		self::setFieldOptions( $field, 'radio' );
	}

	// setUpload_file2
	protected static function setUpload_file2( &$field )
	{
		
	}

	// setUpload_image2
	protected static function setUpload_image2( &$field )
	{
		
	}

	// setWysiwyg_editor
	protected static function setWysiwyg_editor( &$field )
	{
		$pattern		=	'/<div class="toggle(.*?)<\/div>\n<\/div>/s';
		$field->form	=	preg_replace( $pattern, '', $field->form );

		$field->description_class	=	'wysiwyg-desc';
	}
}