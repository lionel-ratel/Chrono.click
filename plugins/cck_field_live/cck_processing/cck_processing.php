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
class plgCCK_Field_LiveCck_Processing extends JCckPluginLive
{
	protected static $type	=	'cck_processing';
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
		
	// onCCK_Field_LivePrepareForm
	public function onCCK_Field_LivePrepareForm( &$field, &$value = '', &$config = array(), $inherit = array() )
	{
		if ( self::$type != $field->live ) {
			return;
		}
		
		// Init
		$live		=	'';
		$name		=	$field->name;
		$options	=	parent::g_getLive( $field->live_options );
		$processing	=	$options->get( 'processing' );

		// Prepare
		if ( $processing && JCckToolbox::getConfig()->get( 'processing', 0 ) ) {
			$processing	=	JCckDatabase::loadObject( 'SELECT scriptfile, options FROM #__cck_more_processings WHERE id = '.(int)$processing.' AND published = 1' );
			
			if ( $processing->scriptfile && is_file( JPATH_SITE.$processing->scriptfile ) ) {
				$options	=	new Registry( $processing->options );

				include JPATH_SITE.$processing->scriptfile;
			}
		}
		
		// Set
		$value	=	is_array( $live ) ? $live : (string)$live;
	}
}
?>