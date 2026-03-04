<?php
/**
* @version 			SEBLOD Toolbox 1.x
* @package			SEBLOD Toolbox Add-on for SEBLOD 3.x
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// View
class CCK_ToolboxViewCCK_Toolbox extends JCckBaseLegacyView
{
	protected $vName	=	'cck_toolbox';

	public function completePlugins( &$items, $type, $target )
	{
		$html	=	'';

 		static $names	=	array(
								'cck_ecommerce_payment'=>'Payment',
								'cck_ecommerce_shipping'=>'Shipping',
								'cck_field'=>'Field',
								'cck_field_link'=>'Link',
								'cck_field_live'=>'Live',
								'cck_storage_location'=>'Object',
								'cck_field_restriction'=>'Restriction',
								'cck_storage'=>'Storage',
								'cck_field_typo'=>'Typograhy',
								'cck_field_validation'=>'Validation',
								'cck_webservice'=>'WebService'
							);

		if ( isset( $names[$type] ) ) {
			$items[$type]->text    =   $names[$type];
		}
		
		if ( $target == 'unpublished' ) {
			$items2		=	JCckDatabase::loadObjectList( 'SELECT a.element FROM #__extensions AS a'
						.	' WHERE a.type = "plugin" AND a.folder = "'.$type.'" AND a.enabled = 0' );
			$property	=	'num3';
		} else {
			$property	=	'num2';

			switch ( $type ) {
				case 'cck_field':
					$items2	=	JCckDatabase::loadObjectList( 'SELECT a.element FROM #__extensions AS a'
							.	' LEFT JOIN #__cck_core_fields AS b ON b.type = a.element'
							.	' WHERE a.type = "plugin" AND a.folder = "'.$type.'" AND a.enabled = 1 AND b.id IS NULL' );
					break;
				case 'cck_field_typo':
					$items2	=	JCckDatabase::loadObjectList( 'SELECT a.element FROM #__extensions AS a'
							.	' LEFT JOIN #__cck_core_type_field AS b ON b.typo = a.element'
							.	' LEFT JOIN #__cck_core_search_field AS c ON c.typo = a.element'
							.	' WHERE a.type = "plugin" AND a.folder = "'.$type.'" AND a.enabled = 1 AND b.fieldid IS NULL AND c.fieldid IS NULL' );	
					break;
				case 'cck_storage':
					$items2	=	JCckDatabase::loadObjectList( 'SELECT a.element FROM #__extensions AS a'
							.	' LEFT JOIN #__cck_core_fields AS b ON b.storage = a.element'
							.	' WHERE a.type = "plugin" AND a.folder = "'.$type.'" AND a.enabled = 1 AND b.id IS NULL' );
					break;
				case 'cck_storage_location':
					$items2	=	JCckDatabase::loadObjectList( 'SELECT a.element FROM #__extensions AS a'
							.	' LEFT JOIN #__cck_core_fields AS b ON b.storage_location = a.element'
							.	' WHERE a.type = "plugin" AND a.folder = "'.$type.'" AND a.enabled = 1 AND b.id IS NULL AND a.element != "config"' );
					break;
				case 'cck_webservice':
					$items2	=	JCckDatabase::loadObjectList( 'SELECT a.element FROM #__extensions AS a'
							.	' LEFT JOIN #__cck_more_webservices AS b ON b.type = a.element'
							.	' WHERE a.type = "plugin" AND a.folder = "'.$type.'" AND a.enabled = 1 AND b.id IS NULL' );
					break;
				default:
					$items2	=	array();
					break;
			}
		}

		if ( $count = count( $items2 ) ) {
			$items[$type]->$property	=	$count;
			$items[$type]->num			=	(int)$items[$type]->num - $count;
			
			foreach ( $items2 as $item ) {
				$html	.=  '<li class="'.$target.'">'.$item->element.'</li>';
			}
		} else {
			$items[$type]->$property	=	0;
		}

		return $html;
	}
}
?>