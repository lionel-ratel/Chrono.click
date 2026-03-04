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
use Joomla\Registry\Registry;

// Script
class plgCCK_FieldCCK_FormInstallerScript extends JCckInstallerScriptPlugin
{
	// preflight
	public function preflight( $type, $parent )
	{
		if ( $type == 'update' && version_compare( (string)$parent->getParent()->getManifest()->version, '1.7.0', '=' ) ) {
			$db		=	Factory::getDbo();
			$query	=	'SELECT manifest_cache FROM #__extensions WHERE type = "plugin" AND folder = "cck_field" AND element = "cck_form"';

			$db->setQuery( $query );
			$json	=	$db->loadResult();

			if ( $json ) {
				$params	=	new Registry( $json );

				if ( version_compare( $params->get( 'version' ), '1.7.0', '<' ) ) {
					$query	=	'SELECT id, defaultvalue, extended, options, options2 FROM #__cck_core_fields WHERE type = "cck_form"';

					$db->setQuery( $query );
					$fields	=	$db->loadObjectList();

					if ( count( $fields ) ) {
						foreach ( $fields as $field ) {
							$sql	=	'';

							if ( $field->options != '' ) {
								$json	=	( $field->options2 == '' ) ? '{}' : $field->options2;
								$params	=	new Registry( $json );

								$params->set( 'forms', $field->options );

								$sql	=	', options = "", options2 = "'.$db->escape( $params->toString() ).'"';
							}

							$db->setQuery( 'UPDATE #__cck_core_fields SET extended = "'.$field->defaultvalue.'"'.$sql.' WHERE id = '.(int)$field->id );
							$db->execute();
						}
					}
				}
			}
		}

		parent::preflight( $type, $parent );
	}
}
?>