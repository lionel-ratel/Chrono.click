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
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;

jimport( 'cck.base.install.export' );

// Model
class CCK_BuilderModelCCK_Builder extends BaseDatabaseModel
{
	// createApp
	public function createApp( $params )
	{
		set_time_limit( 0 );
		
		$app			=	Factory::getApplication();
		$prefix			=	$app->input->post->getString( 'prefix', JCck::getConfig_Param( 'development_prefix', '' ) );
		$title			=	$app->input->post->getString( 'title', '' );
		$name			=	strtolower( str_replace( ' ', '_', $title ) );
		$folder_id		=	$app->input->post->getInt( 'folder', 0 );
		$content_type	=	$app->input->post->getString( 'content_type', '-1' );
		$lang_tags		=	$app->input->post->getString( 'languages', array() );

		if ( empty( $lang_tags ) ) {
			if ( $params->get( 'languages', '' ) ) {
				$lang_tags	=	array( 0=>$params->get( 'languages' ) );
			} else {
				$lang_tags	=	array( 0=>Factory::getLanguage()->getDefault() );
			}
		}

		$title_list		=	$title;
		$type			=	$app->input->post->getString( 'type', '' );
		$target			=	$app->input->post->getString( 'target', '' );
		$class			=	str_replace( ' ', '_', $title );

		if ( strpos( $title, ' ' ) !== false ) {
			$abbr	=	explode( ' ', $title );
			$abbr	=	strtoupper( $abbr[0][0].$abbr[1][0] );
		} else {
			$abbr	=	strtoupper( $title[0] ).strtolower( $title[1] );
		}
		
		if ( $content_type == '-1' ) {
			$content_type	=	'';
		}
		if ( $content_type ) {
			$title_form		=	JCckDatabase::loadResult( 'SELECT title FROM #__cck_core_types WHERE name = "' . $content_type . '"' );
			$name_form 		=	substr( $content_type, strlen( $prefix ) + 1 );
		} else {
			$title_form		=	$app->input->post->getString( 'title_form', '' );
			$name_form		=	strtolower( str_replace( array( ' ', '[', ']' ), array( '_', '', '' ), $title_form ) );
		}

		$parent 		=	'';
		$child_name 	=	'';
		$child_title 	=	'';
		if ( strpos( $type, '_child' ) !== false ) {
			$parent 	=	explode( ' ', $title_form );
			$parent 	=	$prefix.'_'.strtolower( $parent[0] );

			preg_match_all( '/\[(.*?)\]/', $title_form, $matches );
			$child_title 		=	$matches[1][0];
			$child_name 		=	strtolower( $child_title );
		}

		$name_list		=	strtolower( str_replace( ' ', '_', $title_list ) );
		$title_ref 		=	explode( ' ', $title_list );
		$upper_title	=	strtoupper( $title );
		
		if ( $title_ref[0] == 'X' ) {
			$title_ref 	=	ucfirst( $title_ref[0].' '.$title_ref[1] );
			$name_ref	=	strtolower( str_replace( ' ', '_', $title_ref ) );
		} else {
			$title_ref 	=	ucfirst( $title_ref[0] );
			$name_ref 	=	strtolower( $title_ref );
		}

		$form_object 		=	'';
		$form_object_alias 	=	'';
		$form_state 		=	'';	
		$form_table_base 	=	'';
		$form_table_more 	=	'';
		$form_table_more2 	=	'';

		$ref_object 		=	'';
		$ref_object_alias 	=	'';
		$ref_state 			=	'';
		$ref_table_base 	=	'';
		$ref_table_more 	=	'';
		$ref_table_more2 	=	'';

		if ( strpos( $type, 'x' ) !== false ) {
			// Form
			$form_object 		=	JCckDatabase::loadResult( 'SELECT storage_location FROM #__cck_core_types WHERE name="'.$prefix.'_'.$name_form.'"' );

			require_once JPATH_SITE.'/plugins/cck_storage_location/'.$form_object.'/'.$form_object.'.php';
			$properties		=	array( 'status', 'table', 'type_alias' );
			$properties		=	JCck::callFunc( 'plgCCK_Storage_Location'.$form_object, 'getStaticProperties', $properties );

			if ( $form_object != 'free' ) {
				$form_object_alias 	=	strtolower( $properties['type_alias'] );
				$form_state 		=	$properties['status'] ;	
				$form_table_base 	=	$properties['table'];
				$form_table_more 	=	'#__cck_store_form_'.$prefix.'_'.$name_form;
				$form_table_more2 	=	'#__cck_store_item_'.str_replace( '#__', '', $properties['table'] );
			} else {
				$form_object_alias 	=	$name_form;
				$form_state 		=	'published';	
				$form_table_base 	=	'#__cck_store_form_'.$prefix.'_'.$name_form;
				$form_table_more 	=	$form_table_base;
				$form_table_more2 	=	'';
			}

			if ( strpos( $type, 'shared' ) === false ) {
				// Ref
				$ref_object 		=	JCckDatabase::loadResult( 'SELECT storage_location FROM #__cck_core_types WHERE name="'.$prefix.'_'.$name_ref.'"' );

				require_once JPATH_SITE.'/plugins/cck_storage_location/'.$ref_object.'/'.$ref_object.'.php';
				$properties		=	array( 'status', 'table', 'type_alias' );			
				$properties		=	JCck::callFunc( 'plgCCK_Storage_Location'.$ref_object, 'getStaticProperties', $properties );

				if ( $ref_object != 'free' ) {
					$ref_object_alias 	=	strtolower( $properties['type_alias'] );
					$ref_state 			=	$properties['status'];	
					$ref_table_base 	=	$properties['table'];
					$ref_table_more 	=	'#__cck_store_form_'.$prefix.'_'.$name_ref;
					$ref_table_more2 	=	'#__cck_store_item_'.str_replace( '#__', '', $properties['table'] );
				} else {
					$ref_object_alias 	=	$name_ref;
					$ref_state 			=	'published';	
					$ref_table_base 	=	'#__cck_store_form_'.$prefix.'_'.$name_ref;
					$ref_table_more 	=	$ref_table_base;
					$ref_table_more2 	=	'';
				}
			}
		}

		// --------
		$paramsXML	=	array( 'author'=>$params->get( 'author', 'Octopoos' ),
							   'author_email'=>$params->get( 'author_email', 'contact@seblod.com' ),
							   'author_url'=>$params->get( 'author_url', 'https://www.seblod.com' ),
							   'copyright'=>$params->get( 'copyright', 'Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.' ),
							   'license'=>$params->get( 'license', 'GNU General Public License version 2 or later.' ),
							   'creation_date'=>$app->input->post->getString( 'creation_date', '' ),
							   'description'=>$app->input->post->getString( 'description', 'SEBLOD 4.x - www.seblod.com // by Octopoos - www.octopoos.com' ),
							   'version'=>$app->input->post->getString( 'version', '1.0.0' ) );
		// --------
		$config			=	Factory::getConfig();
		$tmp_path		=	$config->get( 'tmp_path' );
		$tmp_dir 		=	uniqid( 'cck_' );
		$path 			= 	$tmp_path.'/'.$tmp_dir;
		$root			=	$path.'/app_cck_'.$type;
		$suffix			=	'';
		//
		$output			=	$params->get( 'output', 0 );
		$output_path	=	$params->get( 'output_path', '' );
		$output_path	=	( $output == 2 && $output_path != '' && is_dir( $output_path ) ) ? $output_path : ( ( $output == 1 && $output_path != ''  ) ? JPATH_SITE.'/'.$output_path : $tmp_path );

		$languages		=	array( 'de', 'en', 'es', 'fr', 'it' );
		$languages_sef	=	array();

		foreach ( $lang_tags as $lang_tag ) {
			$lang_sef			=	substr( $lang_tag, 0, 2 );
		
			$languages_sef[]	=	$lang_sef;
		}

		$languages_sef	=	array_diff( $languages, $languages_sef );

		if ( $name && $type ) {
			Folder::copy( JPATH_COMPONENT.'/install/development/apps/'.$type, $root );
			
			// Exclude by Content Type
			if ( $content_type ) {
				File::delete( $root.'/elements/tables/table_cck_store_form_%prefix%_%form%.xml' );
				File::delete( $root.'/elements/template_styles/template_style_seb_minima-%prefix%_%form%-intro.xml' );
				File::delete( $root.'/elements/template_styles/template_style_seb_one-%prefix%_%form%-admin.xml' );
				File::delete( $root.'/elements/template_styles/template_style_seb_one-%prefix%_%form%-content.xml' );
				File::delete( $root.'/elements/template_styles/template_style_seb_one-%prefix%_%form%-site.xml' );
				File::delete( $root.'/elements/types/type_%prefix%_%form%.xml' );

				$form_field_files	=	Folder::files( $root.'/elements/fields', '.', false, true );

				foreach ( $form_field_files as $form_field_file ) {
					if ( ( strpos( $form_field_file, 'field_%prefix%_%form%' ) !== false ) ) {
						File::delete( $form_field_file );
					}
				}
			}

			// Exclude by Language
			foreach ( $languages_sef as $lang_sef ) {
				// Content Types
				$exluded_files	=	Folder::files( $root.'/elements/types', '_'.$lang_sef.'\.xml$', false, true );

				foreach ( $exluded_files as $excluded_file ) {
					File::delete( $excluded_file );
				}

				// Fields
				$exluded_files	=	Folder::files( $root.'/elements/fields', '_'.$lang_sef.'\.xml$', false, true );

				foreach ( $exluded_files as $excluded_file ) {
					File::delete( $excluded_file );
				}
			}

			$files						=	Folder::files( $root, '.', true, true );
			$paramsXML['description']	=	str_replace( '%Title%', $title, $paramsXML['description'] );
			$variables					=	$params->get( 'variables', (object)array() );

			$abbr_folder	=	'';
			$name_folder	=	'';
			$title_folder	=	'';
			
			if ( $folder_id ) {
				$query	=	'SELECT a.id, a.name, a.title, a.introchar, b.name as parent_name'
						.	' FROM #__cck_core_folders AS a'
						.	' LEFT JOIN #__cck_core_folders AS b ON b.id = a.parent_id'
						.	' WHERE a.id = '.(int)$folder_id;
				$folder	=	JCckDatabase::loadObject( $query );

				if ( is_object( $folder ) ) {
					$abbr_folder	=	$folder->introchar;
					$name_folder	=	$folder->name;
					$title_folder	=	$folder->title;
				}

				// if ( strpos( $type, 'x' ) !== false && strpos( $type, 'shared' ) === false ) {
				$target	=	$folder->parent_name;
				// }
			}
			
			if ( count( $files ) ) {
				foreach ( $files as $file ) {
					$buffer	=	file_get_contents( $file );
					$buffer	=	str_replace( array( '%class%', '%form%', '%Form%', '%list%', '%List%', '%name%', '%prefix%', '%Title%', '%TITLE%', '%abbr%', '%ref%', '%Ref%', '%folder_abbr%', '%folder%', '%Folder%' ), array( $class, $name_form, $title_form, $name_list, $title_list, $name, $prefix, $title, $upper_title, $abbr, $name_ref, $title_ref, $abbr_folder, $name_folder, $title_folder ), ( $content_type ? str_replace( '%prefix%_%form%', $name_form, $buffer ) : $buffer ) );

					//
					$buffer =	str_replace( array( '%TARGET%', '%target%', '%parent%', '%Child%', '%child%' ), array( strtoupper( $target ), $target, $parent, $child_title, $child_name ), $buffer );
					$buffer =	str_replace( array( '%form_object%', '%form_object_alias%', '%form_table_base%', '%form_table_more%', '%form_table_more2%', '%form_state%' ), array( $form_object, $form_object_alias,$form_table_base, $form_table_more, $form_table_more2, $form_state ), $buffer );
					$buffer =	str_replace( array( '%ref_object%', '%ref_object_alias%', '%ref_table_base%', '%ref_table_more%', '%ref_table_more2%', '%ref_state%' ), array( $ref_object, $ref_object_alias, $ref_table_base, $ref_table_more, $ref_table_more2, $ref_state ), $buffer );

					// 
					$buffer =	str_replace( array( '%core_menu_item%' ), array( '' ), $buffer );

					// Variables
					if ( !is_null( $variables ) ) {
						$i	=	1;
						foreach ( (array)$variables as $k=>$variable ) {
							$buffer	=	str_replace( '%variable'.$i++.'%', $variable->value, $buffer );
						}
					}

					if ( File::getExt( $file ) == 'xml' ) {
						$buffer			=	str_replace( array( '%author%', '%author_email%', '%author_url%', '%copyright%', '%license%', '%creation_date%', '%description%', '%version%' ), $paramsXML, $buffer );
					}
					File::write( $file, $buffer );
					if ( ( strpos( $file, '%' ) !== false ) ) {
						File::move( $file, str_replace( array( '%target%', '%name%', '%form%', '%list%', '%prefix%', '%ref%', '%Ref%', '%folder_abbr%', '%folder%', '%Folder%', '%Child%', '%child%' ), array( $target, $name, $name_form, $name_list, $prefix, $name_ref, $title_ref, $abbr_folder, $name_folder, $title_folder, $child_title, $child_name ), ( $content_type ? str_replace( '%prefix%_%form%', $name_form, $file ) : $file ) ) );
					}
				}
			}
			
			require_once JPATH_ADMINISTRATOR.'/components/com_cck/helpers/pclzip/pclzip.lib.php';
			$zip		=	$path.'/'.$name.'.zip';
			$archive	=	new PclZip( $zip );
			if ( $archive->create( $root, PCLZIP_OPT_REMOVE_PATH, $root ) == 0 ) {
				return false;
			}
			
			if ( is_file( $zip ) ) {
				$file	=	$output_path.'/app_cck_'.( $prefix ? $prefix.'_' : '' ).$name.$suffix.'.zip';
				File::move( $zip, $file );
				
				if ( is_dir( $path ) ) {
					Folder::delete( $path );
				}
				
				return ( $output > 0 ) ? true : $file;
			}
		}
		
		return false;
	}
}
?>