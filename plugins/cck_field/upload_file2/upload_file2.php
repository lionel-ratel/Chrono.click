<?php
/**
* @version 			SEBLOD 3.x More
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;

// Plugin
class plgCCK_FieldUpload_File2 extends JCckPluginField
{
	protected static $type		=	'upload_file2';
	protected static $path;
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Construct

	// onCCK_FieldConstruct
	public function onCCK_FieldConstruct( $type, &$data = array() )
	{
		if ( self::$type != $type ) {
			return;
		}

		if ( $data['json']['options2']['path'][strlen($data['json']['options2']['path'])-1] != '/' ) {
			$data['json']['options2']['path']	.=	'/';
		}

		$data['json']['options2']['path']		=	trim( $data['json']['options2']['path'] );

		if ( strpos( $data['json']['options2']['path'], '{' ) === false ) {
			$root_folder	=	JCckDevHelper::getRootFolder( 'resources', ( (int)$data['json']['options2']['path_type'] == 1 ) );

			JCckDevHelper::createFolder( $root_folder.'/'.$data['json']['options2']['path'] );
		}

		if ( (int)$data['storage_crypt'] ) {
			$data['storage_crypt']	=	(int)$data['storage_crypt'] * -1;
		}

		parent::g_onCCK_FieldConstruct( $data );
	}
	
	// onCCK_FieldConstruct_SearchSearch
	public static function onCCK_FieldConstruct_SearchSearch( &$field, $style, $data = array(), &$config = array() )
	{
		if ( !isset( $config['construction']['match_mode'][self::$type] ) ) {
			$data['match_mode']	=	array(
										'none'=>HTMLHelper::_( 'select.option', 'none', Text::_( 'COM_CCK_NONE' ) ),
										''=>HTMLHelper::_( 'select.option', '', Text::_( 'COM_CCK_AUTO' ) )
									);

			$config['construction']['match_mode'][self::$type]	=	$data['match_mode'];
		} else {
			$data['match_mode']									=	$config['construction']['match_mode'][self::$type];
		}
		
		if ( !isset( $config['construction']['variation'][self::$type] ) ) {
			$data['variation']['201']			=	HTMLHelper::_( 'select.option', '<OPTGROUP>', Text::_( 'COM_CCK_SEARCH_FORM' ) );
			$data['variation']['form_upload']	=	HTMLHelper::_( 'select.option', 'form_upload', Text::_( 'COM_CCK_UPLOAD' ) );

			$config['construction']['variation'][self::$type]	=	$data['variation'];
		} else {
			$data['variation']									=	$config['construction']['variation'][self::$type];
		}
		
		parent::onCCK_FieldConstruct_SearchSearch( $field, $style, $data, $config );
	}
	
	// onCCK_FieldConstruct_TypeForm
	public static function onCCK_FieldConstruct_TypeForm( &$field, $style, $data = array(), &$config = array() )
	{
		if ( !isset( $config['construction']['variation'][self::$type] ) ) {
			$data['variation']['201']			=	HTMLHelper::_( 'select.option', '<OPTGROUP>', Text::_( 'COM_CCK_VALUE_CUSTOM' ) );
			$data['variation']['file_download']	=	HTMLHelper::_( 'select.option', 'file_download', Text::_( 'COM_CCK_FILE_DWONLOAD' ) );
			$data['variation']['202']			=	HTMLHelper::_( 'select.option', '</OPTGROUP>', '' );

			$config['construction']['variation'][self::$type]	=	$data['variation'];
		} else {
			$data['variation']									=	$config['construction']['variation'][self::$type];
		}

		parent::onCCK_FieldConstruct_TypeForm( $field, $style, $data, $config );
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Delete

	// onCCK_FieldDelete
	public function onCCK_FieldDelete( &$field, $value = '', &$config = array() )
	{
		if ( self::$type != $field->type ) {
			return;
		}

		$values	=	is_array( $value ) ? $value : ( strpos( $value, ',' ) !== false ? explode( ',', $value ) : (array)$value );

		if ( empty( $values ) ) {
			return;
		}

		$options2		=	JCckDev::fromJSON( $field->options2 );
		$root_folder		=	JCckDevHelper::getRootFolder( 'resources', ( isset( $options2['path_type'] ) && (int)$options2['path_type'] == 1 ) );
		$path 			=	$root_folder.'/'.self::_getPath( $options2, $config, $field );

		foreach ( $values as $v ) {
			if ( is_file( $path.'/'.$v ) ) {
				File::delete( $path.'/'.$v );
			}
		}

		return false;
	}	

	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_FieldPrepareContent
	public function onCCK_FieldPrepareContent( &$field, $value = '', &$config = array() )
	{
		if ( self::$type != $field->type ) {
			return;
		}

		parent::g_onCCK_FieldPrepareContent( $field, $config );
		
		$html 		=	array();

		if ( $value ) {
			$options2			=	JCckDev::fromJSON( $field->options2 );
			$path				=	self::_getPath( $options2, $config, $field );

			if ( !$field->bool ) {
				$file 				=	$path.'/'.$value;
				$field->file_folder =	$path;
				$field->text		=	$value;
				$field->hits		=	self::_getHits( $config['id'], $field->name );
				$field->file_size	=	( file_exists( $file ) ) ? self::_formatBytes( filesize( $file ) ) : self::_formatBytes( 0 );
				$field->extension	=	( strrpos( $file, '.' ) ) ? strtolower( substr( $file, strrpos( $file, '.' ) + 1 ) ) : '';
				$field->link		=	Route::_( 'index.php?option=com_cck&task=download&file='.$field->name.'&id='.$config['id'] );
				$field->linked		=	true;
				$field->html		=	'<a href="'.$field->link.'" title="'.$value.'">'.$value.'</a>';
				$field->typo_target	=	'text';
			} else {
				$field->file_folder =	$path;
				$field->text		=	$value;

				$html				=	'';
				$tmp				=	explode( ',', $value );

				foreach ( $tmp as $key => $name ) {
					$file 	=	$path.'/'.$value;
					$link	=	Route::_( 'index.php?option=com_cck&task=download&file='.$field->name.'&id='.$config['id'].'&xi='.$key );
					$html	.=	'<li><a href="'.$link.'" title="'.$name.'">'.$name.'</a></li>';
				}

				$field->html 		=	'<ul>'.$html.'</ul>';
			}
		} else {
			$field->hits	=	0;
		}

		// Set
		$field->value	=	$value;		
	}
	
	// onCCK_FieldPrepareDownload
	public function onCCK_FieldPrepareDownload( &$field, $value = '', &$config = array() )
	{
		if ( self::$type != $field->type ) {
			return;
		}

		$options2		=	JCckDev::fromJSON( $field->options2 );
		$root_folder	=	JCckDevHelper::getRootFolder( 'resources', ( isset( $options2['path_type'] ) && (int)$options2['path_type'] == 1 ) );

		if ( isset( $options2['path_type'] ) && (int)$options2['path_type'] == 1 ) {
			$field->filepath	=	JCckDevHelper::getRootFolder( 'resources' );
		} else {
			$field->filepath	=	$root_folder;			
		}

		$field->filepath	.=	'/'.self::_getPath( $options2, $config, $field );

		if ( Factory::getSession()->get( 'cck_task' ) == 'form' ) {
			$permissions	=	0755;
			$preview_ext	=	JCck::getConfig_Param( 'media_preview_extensions', '' );		

			if ( $preview_ext ) {
				$preview_ext	=	explode( ',', $preview_ext );

				if ( $file && is_file( $field->filepath.'/'.$value ) && $permissions === 493 ) {
					$ext	=	File::getExt( $field->filepath.'/'.$value );

					if ( in_array( $ext, $preview_ext ) ) {
						$field->task	=	'read';
					}
				}
			}
		}

		if ( !$field->bool ) {
			$filename 	=	$value;
		} else {
			$values 	= 	is_array( $value ) ? $value : ( ( strpos( $value, ',' ) !== false ) ? explode( ',', $value ) : (array)$value );
			$filename 	=	$values[$config['xi']];
		}

		$field->filename 	=	$filename;
	}

	// onCCK_FieldPrepareForm
	public function onCCK_FieldPrepareForm( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
	{
		if ( self::$type != $field->type ) {
			return;
		}

		parent::g_onCCK_FieldPrepareForm( $field, $config );

		self::$path	=	parent::g_getPath( self::$type.'/' );
		
		if ( !(int)$field->state ) {
			return;
		}

		// Init
		if ( count( $inherit ) ) {
			$id		=	( isset( $inherit['id'] ) && $inherit['id'] != '' ) ? $inherit['id'] : $field->name;
			$name	=	( isset( $inherit['name'] ) && $inherit['name'] != '' ) ? $inherit['name'] : $field->name;
		} else {
			$id		=	$field->name;
			$name	=	$field->name;
		}

		// Clear Value for assets
		if ( isset( $config['copyfrom_id'] ) && $config['copyfrom_id'] ) {
			$value	=	'';
		}

		$session 	=	Factory::getSession();
		$session->set( $field->id, array() );

		// Validate
		$validate	=	'';
		$maxfiles	=	'null';

		if ( $config['doValidation'] > 1 ) {
			plgCCK_Field_ValidationRequired::onCCK_Field_ValidationPrepareForm( $field, $id, $config );
			parent::g_onCCK_FieldPrepareForm_Validation( $field, $id, $config );

			if ( isset( $field->validate['max'] ) ) {
				$maxfiles 	=	(int)$field->validate['max'];

				unset( $field->validate['max'] );
			}

			$validate	=	( count( $field->validate ) ) ? ' validate['.implode( ',', $field->validate ).']' : '';
		}

		$folder		=	'';
		$options2	=	JCckDev::fromJSON( $field->options2 );		

		if ( isset( $config['isNew'] ) && !$config['isNew'] ) {
			$folder		=	self::_getFolder( $options2['path'], $config );
			$folder		=	'<input type="hidden" name="'.$name.'_folder_hidden" value="'.$folder.'" />';
		}

		// Prepare
		$class	=	'inputbox text' . $validate . ( $field->css ? ' '.$field->css : '' );
		$attr	=	'class="'.$class.'" ' . ( $field->attributes ? ' '.$field->attributes : '' );
		$line	=	( (int)$field->bool ) ? ' upload-one-line' : '';
		$form 	=	'<div class="upload_file2'.$line.'">'
					.	'<input type="text" id="'.$id.'" name="'.$name.'" value="'.$value.'" '.$attr.' />'
					.	'<input type="hidden" name="'.$name.'_hidden" value="'.$value.'" />'
					.	$folder
					.	'<div class="dropzone" id="'.$id.'Dropzone" name="'.$name.'Dropzone">'
						.	'<div class="fallback">'
							.	'<input id="'.$id.'" name="'.$name.'" '.$attr.' type="file" />'
						.	'</div>'
					.	'</div>'
				.	'</div>';

		// Scripts
		self::_addScripts( $config );
		self::_init( $field, $value, $maxfiles, JCckDev::fromJSON( $field->options2 ), $config );

		// Set
		if ( !$field->variation || $field->variation == 'form_upload' ) {
			$field->form	=	$form;

			if ( $field->script ) {
				parent::g_addScriptDeclaration( $field->script );
			}
		} elseif ( $field->variation == 'file_download' ) {
			if ( strpos( $value, ',' ) !== false ) {
				$values	=	explode( ',', $value );
			} else {
				$values	=	array( $value );
			}

			$field->form	=	'<input type="hidden" id="'.$id.'" name="'.$field->name.'" value="'.$value.'" class="inputbox is-value">'
							.	'<ul>';

			$app		=	Factory::getApplication();
			$join_id	=	'';

			if ( $joinfrom_id = $app->input->getInt( 'joinfrom_id', '' ) ) {
				$join_id	=	'&join_id='.$joinfrom_id;
			} elseif ( $joinfrom = $app->input->get( 'joinfrom', '' ) ) {
				$join_id	=	$app->input->getInt( $joinfrom );

				if ( $join_id ) {
					$join_id	=	'&join_id='.$join_id;
				}
			}

			foreach ( $values as $key => $v ) {
				$link			=	Route::_( 'index.php?option=com_cck&task=download&file='.$field->name.'&id='.$config['id'].$join_id.( $field->bool ? '&xi='.$key : '' ) );

				$field->form	.=	'<li><a href="'.$link.'" title="'.$v.'">'.$v.'</a></li>';
			}

			$field->form	.=	'</ul>';
		} else {
			parent::g_getDisplayVariation( $field, $field->variation, $value, $value, $form, $id, $name, '<input', '', '', $config );
		}

		$field->value	=	$value;
		
		// Return
		if ( $return === true ) {
			return $field;
		}
	}
	
	// onCCK_FieldPrepareResource
	public function onCCK_FieldPrepareResource( &$field, $value = '', &$config = array() )
	{
		if ( self::$type != $field->type ) {
			return;
		}

		$field->data	=	'';

		// Set
		if ( (int)$config['id'] && $value != '' ) {
			$field->data	=	array(
									'href'=>$config['uri_root'].'/download/'.base64_encode( 'file='.$field->name.'&id='.(int)$config['id'] ),
									'name'=>$value
								);
		}
	}

	// onCCK_FieldPrepareSearch
	public function onCCK_FieldPrepareSearch( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		
		// Init
		$divider	=	$field->match_value ? $field->match_value : ' ';

		if ( is_array( $value ) ) {
			$value	=	implode( $divider, $value );
		}

		// Prepare
		if ( $field->variation == 'form_upload' ) {
			self::onCCK_FieldPrepareForm( $field, $value, $config, $inherit, $return );

			$field->match_mode	=	'none';
		} else {
			parent::g_onCCK_FieldPrepareSearch( $field, $config );

			$form			=	JCckDevField::getForm( 'core_not_empty_file', $value, $config, array( 'id'=>$field->id, 'name'=>$field->name, 'variation'=>$field->variation ) );
			
			// Set
			$field->type	=	'checkbox';
			$field->form	=	$form;

			// Match
			if ( $field->match_mode != 'none' ) {
				if ( $value != '' ) {
					$field->match_mode	=	'not_empty';
				} else {
					$field->match_mode	=	'';
				}
			}
		}
		
		$field->value		=	$value;
		
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

		// Init
		if ( count( $inherit ) ) {
			$name		=	( isset( $inherit['name'] ) && $inherit['name'] != '' ) ? $inherit['name'] : $field->name;
			$old_values	=	( isset( $inherit['post'] ) ) ? $inherit['post'][$name.'_hidden'] : @$config['post'][$name.'_hidden'];
			$old_folder	=	( isset( $inherit['post'] ) ) ? $inherit['post'][$name.'_folder_hidden'] : @$config['post'][$name.'_folder_hidden'];
		} else {
			$name		=	$field->name;
			$old_values	=	@$config['post'][$name.'_hidden'];
			$old_folder	=	@$config['post'][$name.'_folder_hidden'];
		}

		// Validate
		parent::g_onCCK_FieldPrepareStore_Validation( $field, $name, $value, $config );
		
		$options2		=	JCckDev::fromJSON( $field->options2 );
		$path_content	=	( isset( $options2['path_content'] ) ) ? (int)$options2['path_content'] : 1;	

		if ( strpos( $old_values, ',' ) != false ) {
			$old_values 	=	explode( ',', $old_values );	
		} elseif ( $old_values != '' ) {
			$old_values		=	array( $old_values );
		} else {
			$old_values 	=	array();
		}

		if ( strpos( $value, ',' ) != false ) {
			$value 	=	explode( ',', $value );	
		} elseif ( $value != '' ) {
			$value	=	array( $value );
		} else {
			$value 	=	array();
		}

		$remove 	=	array_diff( $old_values, $value );

		if ( count( $remove ) ) {
			parent::g_addProcess( 'beforeStore', self::$type, $config, array( 'name'=>$field->name, 'path'=>$options2['path'], 'path_content'=>$path_content, 'remove'=>$remove , 'old_folder'=>$old_folder, 'path_type'=>( isset( $options2['path_type'] ) ? (int)$options2['path_type'] : 0 ) ) );
		}

		$session 	=	Factory::getSession();
		$files 		=	$session->get( $field->id, array() );

		$session->set( $field->id, array() );

		// Same Names ?
		if ( !empty( $files ) ) {
			$names	=	[];
			$value	=	[];

			foreach ( $files as $uuid => $name ) {
				if ( isset( $names[$name] ) ) {
					$ext	=	File::getExt( $name );
					$name	=	str_replace( '.'.$ext, '_'.( ++$names[$name] ).'.'.$ext, $name );
				} else {
					$names[$name]	=	1;
				}

				$files[$uuid]	=	$name;
				$value[]		=	$name;
			}

			unset( $names );
		}

		//
		parent::g_addProcess( 'afterStore', self::$type, $config, array( 'name'=>$field->name, 'path'=>$options2['path'], 'path_content'=>$path_content, 'files'=>$files, 'values'=>$value, 'old_folder'=>$old_folder, 'path_type'=>( isset( $options2['path_type'] ) ? (int)$options2['path_type'] : 0 ) ) );

		$value 			=	implode( ',', $value );

		// Set or Return
		if ( $return === true ) {
			return $value;
		}

		$field->value	=	$value;

		parent::g_onCCK_FieldPrepareStore( $field, $name, $value, $config );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Render
	
	// onCCK_FieldRenderContent
	public static function onCCK_FieldRenderContent( $field, &$config = array() )
	{
		return parent::g_onCCK_FieldRenderContent( $field, 'html' );
	}
	
	// onCCK_FieldRenderForm
	public static function onCCK_FieldRenderForm( $field, &$config = array() )
	{
		return parent::g_onCCK_FieldRenderForm( $field );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Special Events

	// onCCK_FieldBeforeStore
	public static function onCCK_FieldBeforeStore( $process, &$fields, &$storages, &$config = array() )
	{
		$root_folder	=	JCckDevHelper::getRootFolder( 'resources', (int)$process['path_type'] == 1 );
		$path			=	$root_folder.'/'.self::_getPath( $process, $config, $fields[$process['name']] );
 		
		foreach ( $process['remove'] as $value ) {
			if ( is_file( $path.'/'.$value ) ) {
				File::delete( $path.'/'.$value );
			}
		}
	}

	// onCCK_FieldAfterStore
	public static function onCCK_FieldAfterStore( $process, &$fields, &$storages, &$config = array() )
	{	
		$root_folder	=	JCckDevHelper::getRootFolder( 'resources', (int)$process['path_type'] == 1 );
		$folder			=	self::_getFolder( $process['path'], $config );
		$path			=	$root_folder.'/'.self::_getPath( $process, $config, $fields[$process['name']] );

		foreach ( $process['files'] as $key => $name ) {
			$tmp_folder =	JPATH_SITE.'/tmp/'.$key;

			if ( is_file( $tmp_folder.'/'.$key.'_0' ) ) {
				if ( !is_dir( $path ) ) {
					Folder::create( $path );
				}

				if ( abs( $fields[$process['name']]->storage_crypt ) ) {
					$buffer	=	file_get_contents( $tmp_folder.'/'.$key.'_0' );
					$buffer	=	$config['app']->encrypt( $buffer );

					File::write( $path.'/'.$name, $buffer );
				} else {
					File::move( $tmp_folder.'/'.$key.'_0', $path.'/'.$name );
				}

				Folder::delete( $tmp_folder );

				// Set Filename
				$fields[$process['name']]->filename	=	$path.'/'.$name;
			}
		}

		if ( !$config['isNew'] && $process['old_folder'] !== $folder ) {
			$new_path	=	preg_replace( '/\{(.*?)\}/', $folder, $process['path'] );
			$old_path	=	preg_replace( '/\{(.*?)\}/', $process['old_folder'], $process['path'] );
			$from_path	=	str_replace( $new_path, $old_path, $path );
			$values		=	count( $process['files'] ) ? $process['files'] : $process['values'];
			
			if ( !$process['files'] ) {
				if ( !is_dir( $path ) ) {
					Folder::create( $path );
				}

				foreach ( $values as $value ) {
					$old_file	=	$from_path.'/'.$value;
					$new_file	=	$path.'/'.$value;

					if ( is_file( $old_file ) ) {
						File::move( $old_file, $new_file );
					}
				}
			}
		}
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Stuff & Script

	// _addScripts
	protected function _addScripts( $config )
	{
		$app 	= 	Factory::getApplication();
		$doc	=	Factory::getDocument();
		
		JCck::loadjQuery();

		if ( !isset( $app->cck_dropzone ) ) {
			$app->cck_dropzone 	= 	1;

			if ( isset( $config['context']['tmpl'] ) && $config['context']['tmpl'] == 'raw' || isset( $config['formId'] ) && strpos( $config['formId'], '_raw' ) !== false ) {

				echo '<link rel="stylesheet" href="'.self::$path.'assets/css/dropzone.min.css" type="text/css">';
				echo '<script src="'.self::$path.'assets/js/dropzone.min.js" type="text/javascript"></script>';
			} else {
				
				$doc->addStyleSheet( self::$path.'assets/css/dropzone.min.css' );
				$doc->addScript( self::$path.'assets/js/dropzone.min.js' );
			}
		}
		if ( !JCck::is( '4.0' ) ) {
			if ( isset( $config['context']['tmpl'] ) && $config['context']['tmpl'] == 'raw' || isset( $config['formId'] ) && strpos( $config['formId'], '_raw' ) !== false ) {
				echo '<style>div.cck_forms .'.self::$type.' > input.inputbox {width: 0;height: 0;float: left !important;padding: 0 !important;margin: 0 !important;border: none;}</style>';
			} else {
				$doc->addStyleDeclaration( 'div.cck_forms .'.self::$type.' > input.inputbox {width: 0;height: 0;float: left !important;padding: 0 !important;margin: 0 !important;border: none;}' );
			}
		}
	}

	// _formatBytes
	protected static function _formatBytes( $bytes, $precision = 2 )
	{ 
		$units	=	array( 'B', 'KB', 'MB', 'GB', 'TB' ); 

		$bytes	=	max( $bytes, 0 );
		$pow	=	floor( ( $bytes ? log( $bytes ) : 0 ) / log( 1024 ) );
		$pow	=	min( $pow, count( $units ) - 1 );
		$bytes	/=	pow( 1024, $pow );

		if ( $bytes >= 1000 && $bytes < 1024 ) {
			$bytes	=	1;
			$pow++;
		}

		return round( $bytes, $precision ) .' '. $units[$pow];
	}

	// _getFolder
	protected static function _getFolder( $path, $config )
	{
		if ( strpos( $path, '{' ) === false ) {
			return '';
		}

		preg_match( '/\{(.*?)\}/', $path, $matches );

		if ( !( isset( $matches[1] ) && $matches[1] !== '' ) ) {
			return '';
		}

		$field_name	=	$matches[1];	
		$id			=	(int)$config['id'];

		if ( !$id ) {
			$id	=	JCckDatabase::loadResult( 'SELECT id FROM #__cck_core WHERE cck="'.$config['type'].'" AND pk='.(int)$config['pk'] );
		}

		$content	=	JCckContent::getInstance( $id );

		if ( !$content->isSuccessful() ) {
			return '';
		}

		$property	=	JCckDatabase::loadResult( 'SELECT storage_field FROM #__cck_core_fields WHERE name="'.$field_name.'"' );

		if ( is_null( $property ) ) {
			return '';
		}

		$mode	=	$content->getProperty( $property );

		return $mode;
	}

	// _getHits
	protected static function _getHits( $id, $fieldname, $collection = '', $x = 0 )
	{
		$query	=	'SELECT a.hits FROM #__cck_core_downloads AS a WHERE a.id = '.(int)$id.' AND a.field = "'.(string)$fieldname.'" AND a.collection = "'.(string)$collection.'" AND a.x = '.(int)$x;
		$hits	=	JCckDatabase::loadResult( $query ); //@
		return ( $hits ) ? $hits : 0;
	}

	// _getPath
	protected static function _getPath( $params, $config, $field )
	{
		$path		=	$params['path'];
		$replace	=	self::_getFolder( $params['path'], $config );

		if ( $replace !== '' ) {
			$path	=	preg_replace( '/\{(.*?)\}/', $replace, $path );
		}

		if ( $params['path_content'] ) {
			$path 	.=	$config['pk'].'/';	
		}

		if ( isset( $field->language ) && $field->language !== '*' && $field->language !== '' ) {
			$path	.=	$field->language.'/';
		}

		if ( substr( $path, -1 ) == '/' ) {
			$path	=	substr( $path, 0, strlen( $path ) - 1 );
		}

		return $path;
	}

	// _init
	protected function _init( $field, $value, $maxfiles, $options2, $config )
	{
		$doc		=	Factory::getDocument();
		$id 		=	$field->name;
		$path		=	'';
		$pk 		=	$config['pk'];

		$url 		=	'format=raw&task=ajax&'.Session::getFormToken().'=1'
					. 	'&referrer=plugin.cck_field.'.self::$type
					.	'&file=plugins/cck_field/'.self::$type.'/assets/ajax/script.php';				
		$url 		=	JCckDevHelper::getAbsoluteUrl( 'auto', $url );	

		//
		$legal_ext		=	isset( $options2['media_extensions'] ) ? $options2['media_extensions'] : 'custom';

		if ( $legal_ext == 'custom' ) {
			$legal_ext	=	$options2['legal_extensions'];
		} else {
			$default	=	array(
								'archive'=>'7z,bz2,gz,rar,zip,7Z,BZ2,GZ,RAR,ZIP',
								'audio'=>'flac,mp3,ogg,wma,wav,FLAC,MP3,OGG,WMA,WAV',
								'document'=>'csv,doc,docx,pdf,pps,ppsx,ppt,pptx,txt,xls,xlsx,CSV,DOC,DOCX,PDF,PPS,PPSX,PPT,PPTX,TXT,XLS,XLSX',
								'image'=>'bmp,gif,jpg,jpeg,png,tif,tiff,BMP,GIF,JPEG,JPG,PNG,TIF,TIFF',
								'video'=>'flv,mov,mp4,mpg,mpeg,swf,wmv,FLV,MOV,MP4,MPG,MPEG,SWF,WMV',
								'common'=>'bmp,csv,doc,docx,gif,jpg,pdf,png,pps,ppsx,ppt,pptx,txt,xls,xlsx,zip,BMP,CSV,DOC,DOCX,GIF,JPG,PDF,PNG,PPS,PPSX,PPT,PPTX,TXT,XLS,XLSX,ZIP',
								'preset1'=>'',
								'preset2'=>'',
								'preset3'=>''
							);
			$legal_ext	=	JCck::getConfig_Param( 'media_'.$legal_ext.'_extensions', $default[$legal_ext] );

			if ( !$legal_ext ) {
				$legal_ext	=	$options2['legal_extensions'];
			}
		}

		$legal_ext	=	array_unique( explode( ',', strtolower( $legal_ext ) ) );

		//
		switch ( @$options2['size_unit'] ) {
			case '0' : $unit_prod = 1; break;
			case '1' : $unit_prod = 1000; break;
			case '2' : $unit_prod = 1000000; break;
			default  : $unit_prod = 1; break;
		}

		$maxsize	=	floatval( $options2['max_size'] ) * $unit_prod;

		//
		if ( $field->bool ) {
			//	Multiple
			$addedfile 	=	'';
			$success 	=	'var values = (el_'.$id.'.val() != "") ? el_'.$id.'.val().split(",") : [];
							values.push(response);
							el_'.$id.'.attr("value",values.join(","));';
		} else {
			//	Standard
			$maxfiles 	=	1;
			$addedfile 	=	'if (dz_'.$id.'.files.length > 1) {
	  							dz_'.$id.'.removeFile(dz_'.$id.'.files[0]);
	  						}';
			$success 	=	'el_'.$id.'.attr("value",response);';
		}

		//
		$files 		=	array();

		if ( $value ) {
			$values 		=	explode( ',', $value );
			$options2['name']	=	$field->name;
			$root_folder		=	JCckDevHelper::getRootFolder( 'resources', ( isset( $options2['path_type'] ) && (int)$options2['path_type'] == 1 ) );
			$path			=	$root_folder.'/'.self::_getPath( $options2, $config, $field );

			$app			=	Factory::getApplication();
			$bypass			=	(bool)JCck::getConfig_Param( 'media_download_bypass', '1' ) ? '&allow_permissions=1' : '';
			$join_id		=	'';

			if ( $joinfrom_id = $app->input->getInt( 'joinfrom_id', '' ) ) {
				$join_id	=	'&join_id='.$joinfrom_id;
			} elseif ( $joinfrom = $app->input->get( 'joinfrom', '' ) ) {
				$join_id	=	$app->input->getInt( $joinfrom );

				if ( $join_id ) {
					$join_id	=	'&join_id='.$join_id;
				}
			}

			unset( $options2['name'] );

			foreach ( $values as $key => $value ) {
				$download_link	=	'task=download&file='.$field->name.'&id='.$config['id'].$join_id.( $field->bool ? '&xi='.$key : '' ).$bypass;
				$size 			=	@filesize( $path.'/'.$value );
				$size 			=	( $size ) ? $size : 0;
				$files[] 		=	'{name: "'.$value.'",size:'.$size.',accepted:true,dl:"'.JCckDevHelper::getAbsoluteUrl( 'auto', $download_link ).'"}';
			}
		}
		
		$existings 	=	'var '.$id.'mockFiles = ['.implode( ',', $files ).'];';
		
		$lang   	=	Factory::getLanguage();
		$lang->load( 'plg_cck_field_upload_file2', JPATH_ADMINISTRATOR, null, false, true );

		if ( $field->bool ) {
			$default_message	=	Text::_( 'COM_CCK_DROPZONE_UPLOAD_FILES' );
		} else {
			$default_message	=	Text::_( 'COM_CCK_DROPZONE_UPLOAD_FILE' );
		}

		$translations 	=	'dictDefaultMessage:"'.$default_message.'",'
						.	'dictFileTooBig:"'.Text::_( 'COM_CCK_DROPZONE_EXCEEDED_FILE_SIZE' ).'",'
						.	'dictInvalidFileType:"'.Text::_( 'COM_CCK_DROPZONE_INVALID_FILE_TYPE' ).'",'
						.	'dictRemoveFile:"'.Text::_( 'COM_CCK_DROPZONE_REMOVE_FILE' ).'",'
						.	'dictCancelUpload:"'.Text::_( 'COM_CCK_DROPZONE_CANCEL_UPLOAD' ).'",';

		$discover	=	'Dropzone.autoDiscover = false;';
		$js			=	'';

		if ( !JCck::on( '4' ) ) {
			$js	=	$discover;
		}

		$js 	.=	'var dz_'.$id.'=null,el_'.$id.'=$("#'.$id.'");
					$("#'.$id.'Dropzone").dropzone({
						acceptedFiles: ".'.implode( ',.', $legal_ext ).'",
						addRemoveLinks: true,
						chunking: true,
						chunkSize:2000000,
						createImageThumbnails: false,
						maxFiles: '.$maxfiles.',
						maxFilesize: '.$maxsize.',
						parallelChunkUploads: false,
						parallelUploads:1,
						uploadMultiple: false,
						url: "'.$url.'",
						withCredentials: true,
						'.$translations.'
						init: function() {
							dz_'.$id.' = this;
		  					this.on("sending", function(file, xhr, formData) {
		  						formData.append("fid",'.$field->id.');
		  						formData.append("pk",'.$pk.');
		  						formData.append("uuid",file.upload.uuid);
		  						$(file.previewTemplate).find(".dz-remove").addClass("o-btn-outlined o-btn-default o-btn-small").fadeTo( "fast", 1 );
		  						if(typeof '.$id.'dzSending === "function"){'.$id.'dzSending(file);}
		  					});
							this.on("addedfile", function(file) {
								if (typeof jQuery.fn.validationPlugin === "function") {
									el_'.$id.'.validationEngine("hide");	
								}
								'.$addedfile.'
								$(file.previewTemplate).find(".dz-remove").fadeTo( 0, 0 );
								if(typeof '.$id.'dzAddedfile === "function"){'.$id.'dzAddedfile(file);}
		  					});
		  					this.on("removedfile", function(file) {
								if (file.accepted) {
									var uuid = (undefined != file.upload) ? file.upload.uuid : "";
									var values = (el_'.$id.'.val() != "") ? el_'.$id.'.val().split(",") : [];
									values = $.grep(values, function (el_'.$id.', i) {
									    return (el_'.$id.' === file.name) ? false : true;
									});
									el_'.$id.'.attr("value",values.join(","));
									if (uuid != "") {
										$.ajax({
											url: dz_'.$id.'.options.url,
											type: "POST",
											data: ({ mode:"delete", fid:'.$field->id.', pk:'.$pk.', uuid:uuid, name:file.name })
										});								
									}
									if(typeof '.$id.'dzRemovedfile === "function"){'.$id.'dzRemovedfile(file);}
								}
							});
		  					this.on("success", function(file, response) {
								if (!file.upload.chunked) {
									'.$success.'
									if (typeof jQuery.fn.validationPlugin === "function") {
										el_'.$id.'.validationEngine("hide");
									}
								}
								$(file.previewTemplate).find(".dz-remove").fadeTo( "fast", 1 );
								if(typeof '.$id.'dzSuccess === "function"){'.$id.'dzSuccess(file);}
		  					});
		  					this.on("complete", function(file) {
		  						if (file.dl != undefined) {
		  							var downloadButton = "<div class=\"dz-download\"><a href=\""+file.dl+"\" class=\"o-btn-solid o-btn-default o-btn-small o-btn-auto o-btn-icon\"><span class=\"icon-download\"></span><a></div>";
		  							var elDownload = Dropzone.createElement(downloadButton);
		  							$(file.previewTemplate).find(".dz-details").append(elDownload);
		  						}
								$(file.previewTemplate).find(".dz-remove").addClass("o-btn-outlined o-btn-default o-btn-small").fadeTo( "fast", 1 );
								if(typeof '.$id.'dzComplete === "function"){'.$id.'dzComplete(file);}
		  					})
		  					this.on("maxfilesexceeded", function(file) {
		  						this.removeFile(file);
		  					});
		  					this.on("error", function(file, errorMessage, xhr) {
            					$(file.previewElement).find(".dz-error-message").remove();
            					this.removeFile(file);
            					if (typeof jQuery.fn.validationPlugin === "function") {
            						el_'.$id.'.validationEngine("showPrompt", errorMessage, "load", true);	
            					}
            					if(typeof '.$id.'dzError === "function"){'.$id.'dzError(file);}
		  					});
							'.$existings.'
							if ('.$id.'mockFiles.length > 0) {
								$.each( '.$id.'mockFiles, function(i,v) {
									dz_'.$id.'.files.push(v);
									dz_'.$id.'.emit("addedfile", v);
									dz_'.$id.'.emit("complete", v);
								});
							};
							if(typeof '.$id.'dzInitialized === "function"){'.$id.'dzInitialized(this);}
						},
						accept: function(file, done) {
							if (dz_'.$id.'.options.maxFilesize < file.size) {
								done(dz_'.$id.'.options.dictFileTooBig);
							} else {
								done();
							}
						},
						chunksUploaded: function(file, done) {
							$.ajax({
                				url: dz_'.$id.'.options.url,
                   				type: "POST",
                				data: ({ mode:"merge", uuid:file.upload.uuid, fid:'.$field->id.', chunks:file.upload.totalChunkCount, size:file.size, name:file.name }),
								success: function(response){
									'.$success.'
									done();
								}
            				});
						}
					});';

		$js 	=	'jQuery(document).ready(function($){'.$js.'});';

		if ( JCck::on( '4' ) ) {
			$js	=	$discover.$js;
		}

		if ( isset( $config['context']['tmpl'] ) && $config['context']['tmpl'] == 'raw' || isset( $config['formId'] ) && strpos( $config['formId'], '_raw' ) !== false ) {
			echo '<script type="text/javascript">'.$js.'</script>';
		} else {
			$doc->addScriptDeclaration( $js );
		}
	}
}
?>