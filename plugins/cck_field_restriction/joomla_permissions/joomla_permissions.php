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

// Plugin
class plgCCK_Field_RestrictionJoomla_Permissions extends JCckPluginRestriction
{
	protected static $type	=	'joomla_permissions';
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare

	// onCCK_Field_RestrictionPrepareContent
	public static function onCCK_Field_RestrictionPrepareContent( &$field, &$config )
	{
		if ( self::$type != $field->restriction ) {
			return;
		}
		
		$restriction	=	parent::g_getRestriction( $field->restriction_options );
		
		return self::_authorise( $restriction, $field, $config );
	}

	// onCCK_Field_RestrictionPrepareForm
	public static function onCCK_Field_RestrictionPrepareForm( &$field, &$config )
	{
		if ( self::$type != $field->restriction ) {
			return;
		}
		
		$restriction	=	parent::g_getRestriction( $field->restriction_options );
		
		return self::_authorise( $restriction, $field, $config );
	}

	// onCCK_Field_RestrictionPrepareStore
	public static function onCCK_Field_RestrictionPrepareStore( &$field, &$config )
	{
		if ( self::$type != $field->restriction ) {
			return;
		}
		
		$restriction	=	parent::g_getRestriction( $field->restriction_options );
		
		return self::_authorise( $restriction, $field, $config );
	}
	
	// _authorise
	protected static function _authorise( $restriction, &$field, &$config )
	{	
		$do				=	$restriction->get( 'do', 0 );
		$user 			=	JCck::getUser();

		// Construction
		$permissions	=	$restriction->get( 'permissions', '' );
		$permissions	=	explode( ',', $permissions );
		$permissions	=	array_flip( $permissions );
		$check			=	0;
		$count			=	count( $permissions );
		
		// Check
		if ( isset( $permissions['edit'] ) ) {
			$canEdit		=	$user->authorise( 'core.edit', 'com_cck.form.'.$config['type_id'] );
			$check			+=	1;
			if ( !$canEdit ) {
				if ( $count == 1 ) {
					return ( $do ) ? true : false;
				}
			}
		}
		
		if ( isset( $permissions['edit.own'] ) ) {
			$canEditOwn		=	$user->authorise( 'core.edit.own', 'com_cck.form.'.$config['type_id'] );
			$check			+=	2;
			if ( ( !$canEditOwn ) ||
				 ( $canEditOwn && $config['author'] != $user->id ) ) {
				if ( $count == 1 ) {
					return ( $do ) ? true : false;
				}
			}
		}
		
		if ( isset( $permissions['edit.own.content'] ) ) {
			jimport( 'cck.joomla.access.access' );
			$canEditOwnContent	=	CCKAccess::check( $user->id, 'core.edit.own.content', 'com_cck.form.'.$config['type_id'] );
			$check				+=	4;
			if ( $canEditOwnContent ) {
				$field2					=	JCckDatabaseCache::loadObject( 'SELECT storage, storage_table, storage_field FROM #__cck_core_fields WHERE name = "'.$canEditOwnContent.'"' );
				$canEditOwnContent		=	false;
				if ( is_object( $field2 ) && $field2->storage == 'standard' ) {
					$pks				=	( isset( $config['pks'] ) ) ? $config['pks'] : $config['pk'];
					$query				=	'SELECT '.$field2->storage_field.' as map, id FROM '.$field2->storage_table.' WHERE id IN ('.$pks.')';
					$index				=	md5( $query );
					if ( !isset( $cache[$index] ) ) {
						$cache[$index.'_pks']	=	JCckDatabase::loadObjectList( $query, 'id' );
						$values					=	array();
						if ( count( $cache[$index.'_pks'] ) ) {
							foreach ( $cache[$index.'_pks'] as $p ) {
								$values[]	=	$p->map;
							}
						}
						$values			=	( count( $values ) ) ? implode( ',', $values ) : '0';
						$cache[$index]	=	JCckDatabase::loadObjectList( 'SELECT author_id, pk FROM #__cck_core WHERE storage_location = "joomla_article" AND pk IN ( '.$values.' )', 'pk' );
					}
					if ( isset( $cache[$index.'_pks'][$config['pk']] )
						&& isset( $cache[$index][$cache[$index.'_pks'][$config['pk']]->map] )   
						&& $cache[$index][$cache[$index.'_pks'][$config['pk']]->map]->author_id == $user->id ) {
						$canEditOwnContent	=	true;
					}
				}
			} else {
				$canEditOwnContent	=	false;
			}
			if ( !$canEditOwnContent ) {
				if ( $count == 1 ) {
					return ( $do ) ? true : false;
				}
			}
		}

		// if ( isset( $permissions['admin.form'] ) ) {
		// 	$canAdminForm	=	(int)$user->authorise( 'core.admin.form', 'com_cck.form.'.$config['type_id'] );
		// 	$check			+=	10;	/* TODO */
		// 	if ( !$canAdminForm ) {
		// 		if ( $count == 1 ) {
		// 			return ( $do ) ? true : false;
		// 		}
		// 	}
		// }

		// Check (')
		if ( isset( $permissions['export'] ) ) {
			$type_id	=	$config['type_id'];

			if ( ( $form = $restriction->get( 'form', '' ) ) != '' ) {
				if ( $config['type'] != $form ) {
					$type_id	=	JCckDatabaseCache::loadResult( 'SELECT id FROM #__cck_core_types WHERE name = "'.$form.'"' );
				}
			}
			
			if ( !$user->authorise( 'core.export', 'com_cck.form.'.$type_id ) ) {
				return ( $do ) ? true : false;
			}
		}
		if ( isset( $permissions['process'] ) ) {
			$type_id	=	$config['type_id'];

			if ( ( $form = $restriction->get( 'form', '' ) ) != '' ) {
				if ( $config['type'] != $form ) {
					$type_id	=	JCckDatabaseCache::loadResult( 'SELECT id FROM #__cck_core_types WHERE name = "'.$form.'"' );
				}
			}
			
			if ( !$user->authorise( 'core.process', 'com_cck.form.'.$type_id ) ) {
				return ( $do ) ? true : false;
			}
		}
		
		// Check (2)
		if ( $check == 3 ) {
			if ( !( $canEdit && $canEditOwn
				|| ( $canEdit && !$canEditOwn && ( $config['author'] != $user->id ) )
				|| ( $canEditOwn && ( $config['author'] == $user->id ) ) ) ) {
				return ( $do ) ? true : false;
			}
		} elseif ( $check == 5 ) {
			if ( !( $canEdit || ( $canEditOwnContent ) ) ) {
				return ( $do ) ? true : false;
			}
		} elseif ( $check == 6 ) {
			if ( !( ( $canEditOwn && ( $config['author'] == $user->id ) )
				|| ( $canEditOwnContent ) ) ) {
				return ( $do ) ? true : false;
			}
		} elseif ( $check == 7 ) {
			if ( !( $canEdit && $canEditOwn
				|| ( $canEdit && !$canEditOwn && ( $config['author'] != $user->id ) )
				|| ( $canEditOwn && ( $config['author'] == $user->id ) )
				|| ( $canEditOwnContent ) ) ) {
				return ( $do ) ? true : false;
			}
		}

		// Content
		$permissions	=	$restriction->get( 'permissions_content', '' );
		$permissions	=	explode( ',', $permissions );
		$permissions	=	array_flip( $permissions );
		$check			=	0;
		$count			=	count( $permissions );

		if ( $config['pk'] ) {
			if ( isset( $config['location'] ) && $config['location'] ) {
				$location	=	$config['location'];
			} elseif ( isset( $config['base'] ) && is_object( $config['base'] ) && $config['base']->location ) {
				$location	=	$config['base']->location;
			} else {
				$location	=	'joomla_article';
			}

			if ( isset( $permissions['edit'] ) ) {
				$canEdit	=	false;
				$check		+=	1;
				if ( is_file( JPATH_SITE.'/plugins/cck_storage_location/'.$location.'/'.$location.'.php' ) ) {
					require_once JPATH_SITE.'/plugins/cck_storage_location/'.$location.'/'.$location.'.php';
					$canEdit	=	JCck::callFunc_Array( 'plgCCK_Storage_Location'.$location, 'authorise', array( 'core.edit', $config['pk'] ) );
				}
				if ( !$canEdit ) {
					if ( $count == 1 ) {
						return ( $do ) ? true : false;
					}
				}
			}

			if ( isset( $permissions['edit.own'] ) ) {
				$canEditOwn	=	false;
				$check		+=	2;

				if ( is_file( JPATH_SITE.'/plugins/cck_storage_location/'.$location.'/'.$location.'.php' ) ) {
					require_once JPATH_SITE.'/plugins/cck_storage_location/'.$location.'/'.$location.'.php';
					$canEditOwn	=	JCck::callFunc_Array( 'plgCCK_Storage_Location'.$location, 'authorise', array( 'core.edit.own', $config['pk'] ) );
				}
				if ( !$canEditOwn ) {
					if ( $count == 1 ) {
						return ( $do ) ? true : false;
					}
				}
			}

			// Check (2)
			if ( $check == 3 ) {
				if ( !( $canEdit && $canEditOwn
					|| ( $canEdit && !$canEditOwn && ( $config['author'] != $user->id ) )
					|| ( $canEditOwn && ( $config['author'] == $user->id ) ) ) ) {
					return ( $do ) ? true : false;
				}
			}
		}

		return ( $do ) ? false : true;
	}
}
?>