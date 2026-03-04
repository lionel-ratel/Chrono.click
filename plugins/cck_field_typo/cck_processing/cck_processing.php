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

use Joomla\Registry\Registry;

// Plugin
class plgCCK_Field_TypoCck_Processing extends JCckPluginTypo
{
	protected static $type	=	'cck_processing';
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
		
	// onCCK_Field_TypoPrepareContent
	public function onCCK_Field_TypoPrepareContent( &$field, $target = 'value', &$config = array() )
	{		
		if ( self::$type != $field->typo ) {
			return;
		}
		
		// Prepare
		$typo	=	parent::g_getTypo( $field->typo_options );
		$value	=	parent::g_hasLink( $field, $typo, $field->$target );
		
		// Set
		if ( $field->typo_label ) {
			$field->label	=	self::_typo( $typo, $field, $field->label, $config );
		}
		$field->typo		=	self::_typo( $typo, $field, $value, $config );
	}
	
	// _typo
	protected static function _typo( $typo, $field, $value, &$config = array() )
	{
		$priority	=	$typo->get( 'priority', '0' );
		$processing	=	$typo->get( 'processing', '' );

		$name		=	$field->name;
		$typo		=	'';

		// Prepare
		if ( $priority ) {
			parent::g_addProcess( 'beforeRenderContent', self::$type, $config, array( 'name'=>$name, 'processing'=>$processing ), $priority );
		} else {
			if ( $processing && JCckToolbox::getConfig()->get( 'processing', 0 ) ) {
				$processing	=	JCckDatabaseCache::loadObject( 'SELECT scriptfile, options FROM #__cck_more_processings WHERE id = '.(int)$processing.' AND published = 1' );

				if ( $processing->scriptfile && is_file( JPATH_SITE.$processing->scriptfile ) ) {
					$options		=	new Registry( $processing->options );

					include JPATH_SITE.$processing->scriptfile;
				}
			}	
		}
		
		return $typo;
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Special Events
	
	// onCCK_Field_TypoBeforeRenderContent
	public static function onCCK_Field_TypoBeforeRenderContent( $process, &$fields, &$storages, &$config = array() )
	{
		$name	=	$process['name'];
		$typo	=	'';

		if ( isset( $fields[$name] ) ) {
			$processing	=	JCckDatabaseCache::loadObject( 'SELECT scriptfile, options FROM #__cck_more_processings WHERE id = '.(int)$process['processing'].' AND published = 1' );

			if ( is_object( $processing ) && $processing->scriptfile && is_file( JPATH_SITE.$processing->scriptfile ) ) {
				$options		=	new Registry( $processing->options );

				include JPATH_SITE.$processing->scriptfile;

				$fields[$name]->typo	=	$typo;
			}
		}
	}
}
?>