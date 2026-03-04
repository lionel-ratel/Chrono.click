<?php
/**
* @version 			SEBLOD 3.x More
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/
defined( '_JEXEC' ) or die;

use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

class upload_ajax
{
	public $uuid 			=	'';
	public $fid 			=	0;
	public $pk 				=	0;

	public $folder 			=	'';
	public $name 			=	'';
	public $tmp_name 		=	'';

	public $options 		=	array();
	
	//	__construct
    function __construct( $uuid, $fid = 0, $pk = 0 ) 
    {
    	$this->uuid 		=	$uuid;
		$this->fid 			= 	$fid;
		$this->pk 			= 	$pk;
		
		if ( $this->fid ) {
			$this->options 	=	json_decode( JCckDatabase::loadResult( 'SELECT options2 FROM #__cck_core_fields WHERE id='.(int)$this->fid ), true );
		}
	}	

    //  deleteFile
    public function deleteFile( $name )
    {
    	if ( $this->uuid ) {
	    	$this->folder 	=	JPATH_SITE.'/tmp/'.$this->uuid;
			if ( is_dir( $this->folder ) ) {
				Folder::delete( $this->folder );
			}

			$name 	=	$this->_getSafeName( $name );

    	} 
    	
		return $name;
    }   

    //  mergeChunks
    public function mergeChunks( $name, $count, $size )
    {
    	$this->folder 		=	JPATH_SITE.'/tmp/'.$this->uuid;
    	$this->name 		=	$this->_getSafeName( $name );
		$tmp_file 			= 	$this->folder.'/'.$this->uuid;
    	$this->tmp_name 	=	$tmp_file.'_0';

		for ( $i=1; $i < $count; $i++ ) { 
			$buffer	=	file_get_contents( $tmp_file.'_'.$i );	
			file_put_contents( $this->tmp_name, $buffer, FILE_APPEND );

			if ( is_file( $tmp_file.'_'.$i ) ) {
				File::delete( $tmp_file.'_'.$i );
			}			
		}

		$this->_setSession();

		return $this->name;
    }

    //  upload
    public function uploadFile( $file, $index )
    {
    	$this->folder 		=	JPATH_SITE.'/tmp/'.$this->uuid;
    	$this->name 		=	$this->_getSafeName( $file['name'] );
    	$this->tmp_name 	=	$this->uuid.'_'.$index;

    	if ( $index == 0 ) {
    		$this->_setSession();	
    	}
		
		$this->_checkFolder();

		if ( File::upload( $file['tmp_name'], $this->folder.'/'.$this->name, false, false, $this->_getSafeExtensions() ) ) {
			File::move( $this->folder.'/'.$this->name, $this->folder.'/'.$this->tmp_name );

			return $this->name;
		} else {
			return false;
		}		
    }

    // -------- -------- -------- -------- -------- -------- -------- -------- // Stuff & Script

    //  _checkFolder
    protected function _checkFolder()
    {
		if ( !is_dir( $this->folder ) ) {
			Folder::create( $this->folder );
		}
    }

    //  _getSafeExtensions
    public function _getSafeExtensions()
    {
    	$safeFileOptions		=	array();

		if ( (int)$this->options['forbidden_extensions'] ) {
			$forbiddenExtensions	=	array( 'php', 'phps', 'php5', 'php3', 'php4', 'inc', 'pl', 'cgi', 'fcgi', 'java', 'jar', 'py' );
			$safeExtensions			=	JCck::getConfig_Param( 'media_content_forbidden_extensions_whitelist', 'php' );

			if ( $safeExtensions != '' ) {
				$safeExtensions		=	explode( ',', $safeExtensions );

				if ( count( $safeExtensions ) ) {
					$safeExtensions		=	array_diff( $forbiddenExtensions, $safeExtensions );
					$safeFileOptions	=	array(
												'forbidden_extensions'=>$safeExtensions
											);
				}
			}
		}

		return $safeFileOptions;
    }

    //  _getSafeName
    protected function _getSafeName( $name )
    {
		$file_name	=	File::stripExt( $name );
		$replace	=	JCckDev::toSafeSTRING( $file_name, JCck::getConfig_Param( 'media_characters', '-' ), JCck::getConfig_Param( 'media_case', 0 ) );

		return str_replace( $file_name, $replace, $name );
    }

    //  _outputError
    protected function _outputError()
    {
    	header( 'HTTP/1.1 400' );

    	return Text::_( 'COM_CCK_DROPZONE_UPLOAD_NOT_AUTHORIZED_FOR_SECURITY_REASONS' );
    }

    //  _setSession
    protected function _setSession()
    {
		$session				=	Factory::getSession();
		$files 					=	$session->get( $this->fid, array() );
		$files[$this->uuid] 	=	$this->name;

		$session->set( $this->fid, $files );	
    }
}
?>