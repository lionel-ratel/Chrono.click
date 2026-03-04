<?php
/**
* @version 			SEBLOD 3.x Core
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\Registry\Registry;

jimport( 'cck.construction.field.generic_app' );

// Class
class plgCCK_Field_RestrictionX_App extends plgCCK_FieldGeneric_App
{
	// -------- -------- -------- -------- -------- -------- -------- -------- // Export

	// onCCK_FieldExportField
	public static function onCCK_Field_RestrictionExportField( &$field, &$data, &$extensions )
	{
	}
	
	// onCCK_FieldExportType_Field
	public static function onCCK_Field_RestrictionExportType_Field( $field, &$field_join, &$data, &$extensions )
	{
		if ( isset( $field_join->restriction_options ) && $field_join->restriction_options != '' ) {
			$restrictions	=	new Registry( $field_join->restriction_options );
			$restrictions	=	$restrictions->toArray();

			if ( count( $restrictions['conditions'] ) ) {
				foreach ( $restrictions['conditions'] as $restriction ) {
					if ( isset( $restriction['trigger'] ) && $restriction['trigger'] ) {
						CCK_Export::exportPlugin( 'cck_field_restriction', $restriction['trigger'], $data, $extensions );
					}
				}
			}
		}		
	}
	
	// onCCK_FieldExportSearch_Field
	public static function onCCK_Field_RestrictionExportSearch_Field( $field, &$field_join, &$data, &$extensions )
	{
		if ( isset( $field_join->restriction_options ) && $field_join->restriction_options != '' ) {
			$restrictions	=	new Registry( $field_join->restriction_options );
			$restrictions	=	$restrictions->toArray();

			if ( count( $restrictions['conditions'] ) ) {
				foreach ( $restrictions['conditions'] as $restriction ) {
					if ( isset( $restriction['trigger'] ) && $restriction['trigger'] ) {
						CCK_Export::exportPlugin( 'cck_field_restriction', $restriction['options']['trigger'], $data, $extensions );
					}
				}
			}
		}
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Import
	
	// onCCK_FieldImportField
	public static function onCCK_Field_RestrictionImportField( &$field, $data )
	{
	}
	
	// onCCK_FieldImportType_Field
	public static function onCCK_Field_RestrictionImportType_Field( $field, &$field_join, $data )
	{
	}
	
	// onCCK_FieldImportSearch_Field
	public static function onCCK_Field_RestrictionImportSearch_Field( $field, &$field_join, $data )
	{
	}
}
?>