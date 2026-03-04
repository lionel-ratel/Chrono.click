<?php
/**
* @version 			SEBLOD Builder 1.x
* @package			SEBLOD Builder Add-on for SEBLOD 3.x
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

require_once JPATH_ADMINISTRATOR.'/components/'.CCK_COM.'/helpers/common/admin.php';

// Helper
class Helper_Admin extends CommonHelper_Admin
{
	// getFolderOptions
	public static function getFolderOptions( $selectlabel = false, $quickfolder = true, $top = false, $published = true, $element = '', $featured = false, $more = '' )
	{
		$component	=	Factory::getApplication()->input->getCmd( 'option' );
		$options	=	array();
		
		if ( $selectlabel !== false ) {
			if ( is_string( $selectlabel ) ) {
				$options[]	=	HTMLHelper::_( 'select.option', '', '- '.Text::_( 'COM_CCK_'.$selectlabel ).' -', 'value', 'text' );
			} else {
				$options[]	=	HTMLHelper::_( 'select.option', '', Text::_( 'COM_CCK_ALL_FOLDERS_SL' ), 'value', 'text' );
			}
		}
		
		$where		=	( $top ) ? ' WHERE s.lft > 0 AND s.lft BETWEEN parent.lft AND parent.rgt' : ' WHERE s.lft > 1 AND s.lft BETWEEN parent.lft AND parent.rgt';
		$where		=	( $published ) ? $where . ' AND s.published = 1' : $where;

		if ( $component == 'com_cck' && $element && $element != 'session' ) {
			$where	.=	' AND s.elements LIKE "%'.$element.'%"';
		}
		$query		=	'SELECT s.title AS text, s.id AS value, s.parent_id, parent.parent_id AS root_id'
					.	' FROM #__cck_core'.$more.'_folders AS s'
					.	' LEFT JOIN #__cck_core'.$more.'_folders AS parent ON parent.id = s.parent_id'
					.	$where
					.	' GROUP BY s.id ORDER BY s.lft'
					;
		$options2	=	JCckDatabase::loadObjectList( $query );

		if ( count( $options2 ) ) {
			$open	=	false;

			foreach ( $options2 as $k=>$v ) {
				if ( $v->parent_id == 60 ) {
					if ( $open ) {
						$options[]	=	HTMLHelper::_( 'select.option', '</OPTGROUP>', '' );
					}
					$options[]		=	HTMLHelper::_( 'select.option', '<OPTGROUP>', $v->text );
					$open			=	true;
				} elseif ( $v->root_id == 60 ) {
					$options[]		=	HTMLHelper::_( 'select.option', $v->value, $v->text );
				}
			}

			if ( $open ) {
				$options[]	=	HTMLHelper::_( 'select.option', '</OPTGROUP>', '' );
			}
		}

		return $options;
	}
}
?>