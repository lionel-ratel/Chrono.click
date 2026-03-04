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
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;

// Plugin
class plgCCK_FieldUpload_Image2 extends JCckPluginField
{
	protected static $type		=	'upload_image2';
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

		$root_folder	=	JCckDevHelper::getRootFolder( 'resources', ( (int)$data['json']['options2']['path_type'] == 1 ) );

		JCckDevHelper::createFolder( $root_folder.'/'.$data['json']['options2']['path'] );

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
		
		parent::onCCK_FieldConstruct_SearchSearch( $field, $style, $data, $config );
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Delete

	// onCCK_FieldDelete
	public function onCCK_FieldDelete( &$field, $value = '', &$config = array() )
	{
		if ( self::$type != $field->type ) {
			return;
		}

		$values 	= 	( strpos( $value, ',' ) !== false ) ?	explode( ',', $value ) : (array)$value;

		if ( empty( $values ) ) {
			return;
		}

		$options2	=	self::_getOptions2( $field->options2 );

		if ( $options2['isAlias'] ) {
			return;
		}

		$root_folder	=	JCckDevHelper::getRootFolder( 'resources', ( isset( $options2['path_type'] ) && (int)$options2['path_type'] == 1 ) );
		$path 		=	$root_folder.'/'.$options2['path'].$config['pk'];

		if ( $options2['path'] != '' && $config['pk'] != '' && is_dir( $path ) ) {
			if ( Folder::delete( $path ) ) {
				return true;
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
		
		// Set
		$isDefault		=	false;
		$options2		=	self::_getOptions2( $field->options2 );

		if ( trim( $value ) == '' ) {
			$value		=	trim( $field->defaultvalue );
			$isDefault	=	true;
		} else {
			$value		=	trim( $value );
		}

		list( $value, $version )	=	self::_getNameVersion( $value );

		$file_name	=	( $value == '' ) ? '' : File::stripExt( $value );
		$img_title	=	$file_name;
		$img_desc	=	$file_name;

		if ( $version !== '' ) {
			$version	=	'?v='.$version;
		}

		if ( $isDefault ) {
			$value	=	$options2['path'].$value;
		} else {
			$value	=	$options2['path'].$config['pk'].'/'.$value;
		}

		$root_folder	=	JCckDevHelper::getRootFolder( 'resources', ( isset( $options2['path_type'] ) && (int)$options2['path_type'] == 1 ) );

		if ( $value && is_file( $root_folder.'/'.$value ) ) {
			$path		=	substr( $value, 0, strrpos( $value, '/' ) ).'/';

			for ( $i = 1; $i < 11; $i++ ) {
				$thumb					=	$path.'_thumb'.$i.'/'.substr( strrchr( $value, '/' ), 1 );
				$field->{'thumb'.$i}	=	( is_file( $root_folder.'/'.$thumb ) ) ? $thumb.$version : '';
			}

			self::_addThumbs( $field, $options2, $value, $path );

			if ( isset( $options2['content_preview'] ) && $options2['content_preview'] ) {
				$i				=	(int)$options2['content_preview'];
				$field->html	=	( $field->{'thumb'.$i} ) ?  '<img src="'.$field->{'thumb'.$i}.$version.'" title="'.$img_title.'" alt="'.$img_desc.'" />' : '<img src="'.$value.$version.'" title="'.$img_title.'" alt="'.$img_desc.'" />';
			} else {
				$field->html	=	'<img src="'.$value.$version.'" title="'.$img_title.'" alt="'.$img_desc.'" />';
			}

			$field->file_size	=	( file_exists( $value ) ) ? self::_formatBytes( filesize( $value ) ) : self::_formatBytes( 0 );
			$field->extension	=	( strrpos( $value, '.' ) ) ? substr( $value, strrpos( $value, '.' ) + 1 ) : '';
			$field->value		=	$value.$version;
			$field->image_title	=	$img_title;
			$field->image_alt	=	$img_desc;
		} else {
			$field->file_size	= 	'';
			$field->extension	= 	'';
			$field->value		=	'';
			$field->html		=	'';
			$field->image_title	=	'';
			$field->image_alt	=	'';
		}
		$field->typo_target	=	'html';	
	}
	
	// onCCK_FieldPrepareDownload
	public function onCCK_FieldPrepareDownload( &$field, $value = '', &$config = array() )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		$options2	=	self::_getOptions2( $field->options2 );
		$path		=	$options2['path'].$config['pk'];

		list( $value, $version )	=	self::_getNameVersion( $value );

		if ( !$field->bool ) {
			$filename 	=	$path.'/'.$value;
		} else {
			$values 	= 	( strpos( $value, ',' ) !== false ) ?	explode( ',', $value ) : (array)$value;
			$filename 	=	$path.'/'.$values[$config['xi']];
		}
	
		if ( isset( $options2['path_type'] ) && (int)$options2['path_type'] == 1 ) {
			$field->filepath	=	JCckDevHelper::getRootFolder( 'resources' );
		}

		$field->task 		=	'download';
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
		
		// Init
		if ( count( $inherit ) ) {
			$id		=	( isset( $inherit['id'] ) && $inherit['id'] != '' ) ? $inherit['id'] : $field->name;
			$name	=	( isset( $inherit['name'] ) && $inherit['name'] != '' ) ? $inherit['name'] : $field->name;
		} else {
			$id		=	$field->name;
			$name	=	$field->name;
		}

		list( $value, $version )	=	self::_getNameVersion( $value );

		// Clear Value for assets
		if ( isset( $config['copyfrom_id'] ) && $config['copyfrom_id'] ) {
			$value	=	'';
		}

		$options2	=	self::_getOptions2( $field->options2 );

		if ( $options2['isAlias'] ) {
			return;
		}

		$session 	=	Factory::getSession();
		$session->set( $field->id, '' );

		// Validate
		$validate	=	'';

		if ( $config['doValidation'] > 1 ) {
			plgCCK_Field_ValidationRequired::onCCK_Field_ValidationPrepareForm( $field, $id, $config );
			$validate			=	( count( $field->validate ) ) ? ' validate['.implode( ',', $field->validate ).']' : '';
		}

		// Prepare
		$class		=	'inputbox text' . $validate . ( $field->css ? ' '.$field->css : '' );
		$attr		=	'class="'.$class.'" ' . ( $field->attributes ? ' '.$field->attributes : '' );
		$form 		=	'<div class="upload_image2">'
						.	'<input type="text" id="'.$id.'" name="'.$name.'" value="'.$value.'" '.$attr.' />'
						.	'<input type="hidden" name="'.$name.'_hidden" value="'.$value.'"/>'
						.	'<input type="hidden" name="'.$name.'_version" value="'.$version.'"/>'
						.	'<div class="dropzone" id="'.$id.'Dropzone" name="'.$name.'Dropzone">'
							.	'<div class="fallback">'
								.	'<input id="'.$id.'" name="'.$name.'" '.$attr.' type="file" />'
							.	'</div>'
						.	'</div>'
					.	'</div>';

		self::_addScripts( (int)$options2['images_cropping'], $config );
		self::_init( $field, $value, $version, JCckDev::fromJSON( $field->options2 ), $config );

		// Set
		if ( ! $field->variation ) {
			$field->form	=	$form;

			if ( $field->script ) {
				parent::g_addScriptDeclaration( $field->script );
			}
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
		$options2 		=	self::_getOptions2( $field->options2 );

		// Prepape
		$value			=	$options2['path'].$config['pk'].'/'.$value;		

		// Set
		$cdn			=	'';

		if ( method_exists( 'JCck', 'getCdn' ) ) {
			$cdn			=	JCck::getCdn();	
		}

		$field->data	=	( $cdn ? $cdn.'/' : Uri::root() ).$value;
	}

	// onCCK_FieldPrepareSearch
	public function onCCK_FieldPrepareSearch( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		parent::g_onCCK_FieldPrepareSearch( $field, $config );
		
		// Init
		$divider	=	$field->match_value ? $field->match_value : ' ';

		if ( is_array( $value ) ) {
			$value	=	implode( $divider, $value );
		}
		
		// Prepare
		$form	=	JCckDevField::getForm( 'core_not_empty_image', $value, $config, array( 'id'=>$field->id, 'name'=>$field->name, 'variation'=>$field->variation ) );
		
		// Set
		$field->form		=	$form;
		
		if ( $field->match_mode != 'none' ) {
			if ( $value != '' ) {
				$field->match_mode	=	'not_empty';
			} else {
				$field->match_mode	=	'';
			}
		}
		$field->type		=	'checkbox';
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
			$old_value	=	( isset( $inherit['post'] ) ) ? $inherit['post'][$name.'_hidden'] : @$config['post'][$name.'_hidden'];
			$version	=	( isset( $inherit['post'] ) ) ? $inherit['post'][$name.'_version'] : @$config['post'][$name.'_version'];
		} else {
			$name		=	$field->name;
			$old_value	=	@$config['post'][$name.'_hidden'];
			$version	=	@$config['post'][$name.'_version'];
		}

		$options2		=	self::_getOptions2( $field->options2 );		

		if ( $options2['isAlias'] ) {
			return;
		}

		// Validate
		parent::g_onCCK_FieldPrepareStore_Validation( $field, $name, $value, $config );
		
		$session 		=	Factory::getSession();
		$file 			=	$session->get( $field->id, '' );
		$uuid 			=	'';
		$name 			=	'';

		$session->set( $field->id, '' );

		if ( strpos( $file, '||' ) !== false ) {
			$file 	=	explode( '||', $file );
			$uuid 	=	$file[0];
			$name 	=	$file[1];
		}

		if ( $old_value && $uuid !== '' ) {
			parent::g_addProcess( 'beforeStore', self::$type, $config, array( 'old_name'=>$old_value, 'path'=>$options2['path'], 'path_type'=>( isset( $options2['path_type'] ) ? (int)$options2['path_type'] : 0 ) ) );
		}

		if ( $uuid ) {
			$update_name	=	(string)$options2['file_name'] === '' ? JCck::getConfig_Param( 'media_filename', 'uploaded' ) : $options2['file_name'];

			if ( $update_name === 'existing' && $old_value !== '' ) {
				$ext_old	=	strtolower( File::getExt( $old_value ) );
				$ext_new	=	strtolower( File::getExt( $name ) );

				if ( $ext_old === $ext_new ) {
					$value	=	$old_value;
				}
			}

			if ( $old_value !== '' ) {
				$version	=	(string)( $version === '' ? 2 : (int)$version + 1 );
			}
			
			parent::g_addProcess( 'afterStore', self::$type, $config, array( 'name'=>$name, 'new_name'=>$value, 'path'=>$options2['path'], 'uuid'=>$uuid, 'path_type'=>( isset( $options2['path_type'] ) ? (int)$options2['path_type'] : 0 ) ) );
		}

		if ( isset( $value, $version ) && $value !== '' && $version !== '' ) {
			$value	.=	'?v='.$version;
		}

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
		if ( $process['path'] == '' ) {
			return;
		}

		$name 			=	$process['old_name'];
		$ext 			=	File::getExt( $name );
		$json 			=	str_replace( '.'.$ext, '.json', $name );
		$webp 			=	str_replace( '.'.$ext, '.webp', $name );

		$root_folder		=	JCckDevHelper::getRootFolder( 'resources', (int)$process['path_type'] == 1 );
		$path 			=	$root_folder.'/'.$process['path'].$config['pk'];

		if ( is_file( $path.'/'.$name ) ) {
			File::delete( $path.'/'.$name );

			if ( is_file( $path.'/'.$json ) ) {
				File::delete( $path.'/'.$json );
			}
			if ( is_file( $path.'/'.$webp ) ) {
				File::delete( $path.'/'.$webp );
			}
		}

		for ( $i=0; $i < 11; $i++ ) { 
			if ( is_file( $path.'/_thumb'.$i.'/'.$name ) ) {
				File::delete( $path.'/_thumb'.$i.'/'.$name );
			}
			if ( is_file( $path.'/_thumb'.$i.'/'.$webp ) ) {
				File::delete( $path.'/_thumb'.$i.'/'.$webp );
			}
		}
	}

	// onCCK_FieldAfterStore
	public static function onCCK_FieldAfterStore( $process, &$fields, &$storages, &$config = array() )
	{	
		$name 			=	$process['name']; 
		$new_name 		=	$process['new_name'];
		$root_folder		=	JCckDevHelper::getRootFolder( 'resources', (int)$process['path_type'] == 1 );
		$path 			=	$root_folder.'/'.$process['path'].$config['pk'];
		$uuid 			=	$process['uuid'];

		if ( $uuid != '' && $name != '' ) {
			$ext 		=	File::getExt( $name );
			$json 		=	str_replace( '.'.$ext, '.json', $name );
			$new_json	=	str_replace( '.'.$ext, '.json', $new_name );
			$webp 		=	str_replace( '.'.$ext, '.webp', $name );
			$new_webp	=	str_replace( '.'.$ext, '.webp', $new_name );
			$tmp_folder =	JPATH_SITE.'/tmp/'.$uuid;

			if ( is_dir( $tmp_folder ) ) {
				if ( !is_dir( $path ) ) {
					Folder::create( $path );
				}

				if ( is_file( $tmp_folder.'/'.$name ) ) {
					File::move( $tmp_folder.'/'.$name, $path.'/'.$new_name );

					if ( is_file( $tmp_folder.'/'.$webp ) ) {
						File::move( $tmp_folder.'/'.$webp, $path.'/'.$new_webp );
					}
					if ( is_file( $tmp_folder.'/'.$json ) ) {
						File::move( $tmp_folder.'/'.$json, $path.'/'.$new_json );
					}
				}

				for ( $i=0; $i < 11;  $i++ ) { 
					if ( is_dir( $tmp_folder.'/_thumb'.$i ) ) {
						if ( !is_dir( $path.'/_thumb'.$i ) ) {
							Folder::create( $path.'/_thumb'.$i );
						}
						if ( is_file( $tmp_folder.'/_thumb'.$i.'/'.$name ) ) {
							File::move( $tmp_folder.'/_thumb'.$i.'/'.$name, $path.'/_thumb'.$i.'/'.$new_name );
						}
						if ( is_file( $tmp_folder.'/_thumb'.$i.'/'.$webp ) ) {
							File::move( $tmp_folder.'/_thumb'.$i.'/'.$webp, $path.'/_thumb'.$i.'/'.$new_webp );
						}
					}
				}

				Folder::delete( $tmp_folder );
			}
		}
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Stuff & Script

	// _addScripts
	protected function _addScripts( $crop, $config )
	{
		$app 	= 	Factory::getApplication();
		$doc	=	Factory::getDocument();
		$cdn	=	'';

		if ( method_exists( 'JCck', 'getCdn' ) ) {
			$cdn			=	JCck::getCdn();	
		}

		$url_root	=	substr( ( $cdn ? $cdn.'/' : Uri::root() ), 0, -1 );

		JCck::loadjQuery();

		if ( !isset( $app->cck_dropzone ) ) {
			$app->cck_dropzone 	= 	1;

			if ( isset( $config['context']['tmpl'] ) && $config['context']['tmpl'] == 'raw' || isset( $config['formId'] ) && strpos( $config['formId'], '_raw' ) !== false ) {
				echo '<link rel="stylesheet" href="'.$url_root.self::$path.'assets/css/dropzone.min.css?'.JCckDev::getMediaVersion().'" type="text/css">';
				echo '<script src="'.$url_root.self::$path.'assets/js/dropzone.min.js?'.JCckDev::getMediaVersion().'" type="text/javascript"></script>';
			} else {
				$doc->addStyleSheet( $url_root.self::$path.'assets/css/dropzone.min.css', array( 'version'=>JCckDev::getMediaVersion() ) );
				$doc->addScript( $url_root.self::$path.'assets/js/dropzone.min.js', array( 'version'=>JCckDev::getMediaVersion() ) );
			}
		}		

		if ( $crop && !isset( $app->cck_crop ) ) {
			$app->cck_crop 	= 	1;

			if ( isset( $config['context']['tmpl'] ) && $config['context']['tmpl'] == 'raw' || isset( $config['formId'] ) && strpos( $config['formId'], '_raw' ) !== false ) {
				if ( !JCck::is( '4.0' ) ) {
					echo '<link rel="stylesheet" href="'.$url_root.self::$path.'assets/css/modal.css?'.JCckDev::getMediaVersion().'" type="text/css">';	
				}				
				echo '<link rel="stylesheet" href="'.$url_root.self::$path.'assets/css/upload_image2.css?'.JCckDev::getMediaVersion().'" type="text/css">';
				echo '<script src="'.$url_root.self::$path.'assets/js/upload_image2.js?'.JCckDev::getMediaVersion().'" type="text/javascript"></script>';
				echo '<script src="'.$url_root.self::$path.'assets/js/upload_image2_patch.js?'.JCckDev::getMediaVersion().'" type="text/javascript"></script>';
			} else {
				if ( !JCck::is( '4.0' ) ) {
					$doc->addStyleSheet( $url_root.self::$path.'assets/css/modal.css', array( 'version'=>JCckDev::getMediaVersion() ) );
				}
				$doc->addStyleSheet( $url_root.self::$path.'assets/css/upload_image2.css', array( 'version'=>JCckDev::getMediaVersion() ) );
				$doc->addScript( $url_root.self::$path.'assets/js/upload_image2.js', array( 'version'=>JCckDev::getMediaVersion() ) );
				$doc->addScript( $url_root.self::$path.'assets/js/upload_image2_patch.js', array( 'version'=>JCckDev::getMediaVersion() ) );
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

	// _addThumbs
	protected static function _addThumbs( &$field, $options2, $value, $path )
	{
		$root_folder	=	JCckDevHelper::getRootFolder( 'resources', ( isset( $options2['path_type'] ) && (int)$options2['path_type'] == 1 ) );

		switch ( @$options2['force_thumb_creation'] ) {
			case '0':
				break;
			case '1':
				$image	=	new JCckDevImage( $root_folder.'/'.$options2['path'].$value );
				for ( $i = 1; $i < 11; $i++ ) {
					if ( ! $field->{'thumb'.$i} || $field->{'thumb'.$i} == '' ) {
						$thumb_result			=	self::_addThumb( $image, $options2, $i );
						$field->{'thumb'.$i}	=	( $thumb_result ) ? $path.'_thumb'.$i.'/'.substr( strrchr( $value, '/' ), 1 ) : '';
					}
				}
				break;
			case '2' :
				$image	=	new JCckDevImage( $root_folder.'/'.$options2['path'].$value );
				for ( $i = 1; $i < 11; $i++ ) {
					if( !$options2['thumb'.$i.'_cropping'] ){
						$thumb_result			=	self::_addThumb( $image, $options2, $i );
						$field->{'thumb'.$i}	=	( $thumb_result ) ? $path.'_thumb'.$i.'/'.substr( strrchr( $value, '/' ), 1 ) : '';
					}
				}
				break;
		}

		$field->thumbnails	=	array();

		for ( $i = 1; $i < 11; $i++ ) {
			$k	=	'thumb'.$i;

			if ( $options2[$k.'_process'] ) {
				$field->thumbnails[$k]	=	array(
												'height'=>$options2[$k.'_height'],
												'width'=>$options2[$k.'_width']
											);	
			}
		}
	}

	// _addThumb
	protected static function _addThumb( $image, $options, $thumb )
	{
		if ( count( $options ) ) {
			$format_name	=	'thumb'.$thumb.'_process';
			$width_name		=	'thumb'.$thumb.'_width';
			$height_name	=	'thumb'.$thumb.'_height';

			if ( trim( $options[$format_name] ) ) {
				return $image->createThumb( '', $thumb, $options[$width_name], $options[$height_name], $options[$format_name] );
			} else {
				return false;
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

	// _getNameVersion
	protected static function _getNameVersion( $value )
	{
		$version	=	'';

		if ( strpos( $value, '?v=' ) !== false ) {
			$tmp		=	explode( '?v=', $value );
			$value		=	$tmp[0];
			$version	=	$tmp[1];
		}

		return array( $value, $version );
	}

	// _getOptions2
	protected static function _getOptions2( $options2 )
	{ 
		$options2 				=	JCckDev::fromJSON( $options2 );
		$options2['isAlias'] 	=	0;

		if ( !isset( $options2['behavior'] ) || $options2['behavior'] == 'standard' ) {
			return $options2;
		}

		$opts2 		=	JCckDatabase::loadResult( 'SELECT options2 FROM #__cck_core_fields WHERE name="'.$options2['field_alias'].'"' );

		if ( strrpos( $opts2, '{' ) === false ) {
			return $options2;
		}

		$opts2 				=	JCckDev::fromJSON( $opts2 );
		$opts2['isAlias'] 	=	1;

		return $opts2;
	}

	// _init
	protected function _init( $field, $value, $version, $options2, $config )
	{
		$app		=	Factory::getApplication();
		$doc		=	Factory::getDocument();
		$id 		=	$field->name;
		$legal_ext	=	isset( $options2['media_extensions'] ) ? $options2['media_extensions'] : 'custom';
		$pk 		=	$config['pk'];
		$url 		=	'format=raw&task=ajax&'.Session::getFormToken().'=1'
					. 	'&referrer=plugin.cck_field.'.self::$type
					.	'&file=plugins/cck_field/'.self::$type.'/assets/ajax/script.php';				
		$url 		=	JCckDevHelper::getAbsoluteUrl( 'auto', $url );	

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

		switch ( @$options2['size_unit'] ) {
			case '0' : $unit_prod = 1; break;
			case '1' : $unit_prod = 1000; break;
			case '2' : $unit_prod = 1000000; break;
			default  : $unit_prod = 1; break;
		}
		$maxsize		=	floatval( $options2['max_size'] ) * $unit_prod;

		$existing 		=	'var '.$id.'mockFiles = [];';
		$preview_width 	=	282;
		$preview_height =	141;
		$preview_path	=	'';

		if ( (int)$options2['content_preview'] > 0 ) {
			$process	=	$options2['thumb'.$options2['content_preview'].'_process'];

			if ( $process != '' && $process != '0' ) {
				if ( $options2['thumb'.$options2['content_preview'].'_process'] == 'quotient' ) {
					if ( (int)$options2['thumb'.$options2['content_preview'].'_width'] > 0 ) {
						$coef 	=	$options2['thumb'.$options2['content_preview'].'_width'] / $options2['thumb'.$options2['content_preview'].'_height'];
					} else {
						$coef 	=	floatval( '0.'.$options2['thumb'.$options2['content_preview'].'_height'] );
					}

					$th 			=	$options2['thumb'.$options2['content_preview'].'_cropping'];
					$preview_width 	=	ceil( $options2['thumb'.$th.'_width'] * $coef );
					$preview_height =	ceil( $options2['thumb'.$th.'_height'] * $coef );
				} else {
					$preview_width 	=	$options2['thumb'.$options2['content_preview'].'_width'];
					$preview_height =	$options2['thumb'.$options2['content_preview'].'_height'];
				}

				$preview_width 		=	( (int)$preview_width ) ? $preview_width : 282;
				$preview_height 	=	( (int)$preview_height ) ? $preview_height : 141;

				$preview_path		=	'/_thumb'.$options2['content_preview'];
			}
		}

		$preview		=	'thumbnailWidth: '.$preview_width.',thumbnailHeight: '.$preview_height.',';
		$root_folder	=	JCckDevHelper::getRootFolder( 'resources', ( isset( $options2['path_type'] ) && (int)$options2['path_type'] == 1 ) );

		if ( $value ) {
			$file_path		=	$root_folder.'/'.$options2['path'].$config['pk'].'/'.$value;
			$size 			=	@filesize( $file_path );
			$size 			=	( $size ) ? $size : 0;

			$download_link	=	'task=download&file='.$field->name.'&id='.$config['id'].'&allow_permissions=1';
			$cdn			=	'';

			if ( method_exists( 'JCck', 'getCdn' ) ) {
				$cdn			=	JCck::getCdn();	
			}
			
			$preview_url 	=	( $cdn ? $cdn.'/' : Uri::root() ).$options2['path'].$config['pk'].$preview_path.'/'.$value.'?'.uniqid();
			$existing		=	'var '.$id.'mockFiles = [{name: "'.$value.'",size:'.$size.',accepted:true,preview:"'.$preview_url.'",dl:"'.JCckDevHelper::getAbsoluteUrl( 'auto', $download_link ).'"}];';
		}

		$crop 		=	'';
		$tmpl 		=	$app->input->get( 'tmpl', '', 'STRING' );
		$to_crop 	=	false;			

		for ( $i=1;  $i <= 10 ;  $i++ ) { 
			if ( $options2['thumb'.$i.'_process'] != '0' && $options2['thumb'.$i.'_cropping'] == '0' ) {
				$to_crop 	=	true;
				break;
			}
		}

		if ( $tmpl != 'raw' && (int)$options2['images_cropping'] && $to_crop ) {
			if ( !$config['isNew'] ) {
				$version	=	$version === '' ? 1 : (int)$version + 1;
			}

			$crop 		=	'var name = (undefined != file.upload) ? file.upload.filename : file.name;
							var uuid = (undefined != file.upload) ? file.upload.uuid : "";
							var cropButton = "<a href=\"javascript:void(0);\" class=\"getcrop o-btn-solid o-btn-default o-btn-small\"  data-value=\""+name+"\" data-uuid=\""+uuid+"\" data-fid=\"'.$field->id.'\" data-pk=\"'.$pk.'\" data-thumb=\"0\" data-version=\"'.$version.'\">'.Text::_( 'COM_CCK_CROP' ).'</a>";
							var elCrop = Dropzone.createElement(cropButton);
							elCrop.addEventListener("click", function (e) {
								JCck.More.CropX.getArea(this);
							});
							file.previewElement.appendChild(elCrop);
		  					';
		}

		$lang   	=	Factory::getLanguage();
		$lang->load( 'plg_cck_field_upload_image2', JPATH_ADMINISTRATOR, null, false, true );
		
		$translations 	=	'dictDefaultMessage:"'.Text::_( 'COM_CCK_DROPZONE_UPLOAD_FILE' ).'",'
						.	'dictFileTooBig:"'.Text::_( 'COM_CCK_DROPZONE_EXCEEDED_FILE_SIZE' ).'",'
						.	'dictInvalidFileType:"'.Text::_( 'COM_CCK_DROPZONE_INVALID_FILE_TYPE' ).'",'
						.	'dictRemoveFile:"'.Text::_( 'COM_CCK_DROPZONE_REMOVE_FILE' ).'",'
						;

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
						chunkSize:1000000,
						'.$preview.'
						maxFiles: 1,
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
		  						formData.append("chunks",file.upload.totalChunkCount);
		  					});						
							this.on("addedfile", function(file) {
								if (typeof jQuery.fn.validationPlugin === "function") {
									el_'.$id.'.validationEngine("hide");
								}
		  						if (dz_'.$id.'.files.length > 1) {
		  							dz_'.$id.'.removeFile(dz_'.$id.'.files[0]);
		  						}
		  						$(file.previewTemplate).find(".dz-remove").fadeTo( "fast", 1 );
		  					});
		  					this.on("removedfile", function(file) {
								if (file.accepted) {
									var uuid = (undefined != file.upload) ? file.upload.uuid : "";
									el_'.$id.'.attr("value","");
									if (uuid != "") {
										$.ajax({
											url: dz_'.$id.'.options.url,
											type: "POST",
											data: ({ mode:"delete", fid:'.$field->id.', pk:'.$pk.', uuid:uuid, name:file.name })
										});	
									}
								}
							});
		  					this.on("success", function(file, response) {
								if (!file.upload.chunked) {
									file.upload.filename =	response;
									el_'.$id.'.attr("value",response);
									if (typeof jQuery.fn.validationPlugin === "function") {
										el_'.$id.'.validationEngine("hide");
									}
								}
								if (undefined != $("a[data-pk='.$pk.']") ) {
									$("a[data-pk='.$pk.']").attr("data-value",file.name);
								}
								$(file.previewTemplate).find(".dz-remove").fadeTo( "fast", 1 );
		  					});
		  					this.on("complete", function(file) {
		  						if (file.dl != undefined) {
		  							var downloadButton = "<div class=\"dz-download\"><a href=\""+file.dl+"\" class=\"o-btn-solid o-btn-default o-btn-small o-btn-auto o-btn-icon\"><span class=\"icon-download\"></span><a></div>";
		  							var elDownload = Dropzone.createElement(downloadButton);
		  							$(file.previewTemplate).find(".dz-details").append(elDownload);
		  						}
		  						'.$crop.'
								$(file.previewTemplate).find(".dz-remove").addClass("o-btn-outlined o-btn-default o-btn-small").fadeTo( "fast", 1 );
		  					});
		  					this.on("maxfilesexceeded", function(file) {
		  						this.removeFile(file);
		  					});
		  					this.on("error", function(file, errorMessage, xhr) {
            					$(file.previewElement).find(".dz-error-message").remove();
            					this.removeFile(file);
            					if (typeof jQuery.fn.validationPlugin === "function") {
            						el_'.$id.'.validationEngine("showPrompt", errorMessage, "load", true);
            					}
		  					});							
							'.$existing.'
							if ('.$id.'mockFiles.length > 0) {
								$.each( '.$id.'mockFiles, function(i,v) {
									dz_'.$id.'.files.push(v);
									dz_'.$id.'.emit("addedfile", v);
									dz_'.$id.'.emit("complete", v);
									if (v.preview != undefined) {
										dz_'.$id.'.emit("thumbnail", v, v.preview);	
									}
								});
							};							
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
									file.upload.filename =	response;
									el_'.$id.'.attr("value",response);
									done();
								}
            				});
						}
					});';

		$js 	=	'jQuery(document).ready(function($){'.$js.'});';

		if ( JCck::on( '4' ) ) {
			$js	=	$discover.$js;
		}

		if ( $to_crop && isset( $app->cck_crop ) && $app->cck_crop === 1 ) {
			$app->cck_crop	=	2;
			$crop_link 		=	'format=raw&task=ajax&'.Session::getFormToken().'=1&mode=crop'
							. 	'&referrer=plugin.cck_field.'.self::$type
							.	'&file=plugins/cck_field/'.self::$type.'/assets/ajax/script.php';
			$crop_link 		=	JCckDevHelper::getAbsoluteUrl( 'auto', $crop_link );
			$js				.=	'jQuery(document).ready(function($){JCck.More.CropX.link ="'.$crop_link.'";});';
		}

		if ( isset( $config['context']['tmpl'] ) && $config['context']['tmpl'] == 'raw' || isset( $config['formId'] ) && strpos( $config['formId'], '_raw' ) !== false ) {
			echo '<script type="text/javascript">'.$js.'</script>';
		} else {
			$doc->addScriptDeclaration( $js );
		}
	}
}
?>