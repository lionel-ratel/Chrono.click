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

if ( !class_exists( 'watermark', false ) ) {
	include_once ( __DIR__.'/watermark.php' );
}

class upload_image2
{
	public $uuid 			=	'';
	public $fid 			=	0;
	public $pk 				=	0;

	public $extension 		=	'';
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
    public function deleteFile()
    {
    	if ( $this->uuid ) {
	    	$this->folder 	=	JPATH_SITE.'/tmp/'.$this->uuid;
	    	
		if ( is_dir( $this->folder ) ) {
			Folder::delete( $this->folder );
		}
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

		if ( File::move( $this->tmp_name, $this->folder.'/'.$this->name ) ) {
			$this->_checkOrientation();
			$this->_createThumbs();
		}

		$this->_setSession();

		return $this->name;
    }    

    //  upload
    public function uploadFile( $file, $index, $count )
    {
    	$this->folder 		=	JPATH_SITE.'/tmp/'.$this->uuid;

    	if ( $count == 1) {
	    	$this->name 		=	$this->_getSafeName( $file['name'] );
	    	$this->extension	=	File::getExt( $this->name );

    	} else {
	    	$this->name 		=	$this->uuid.'_'.$index;
    	}

    	if ( $index == 0 ) {
    		$this->_setSession();	
    	}
		
		$this->_checkFolder();

		if ( File::upload( $file['tmp_name'], $this->folder.'/'.$this->name, false, false, array() ) ) {
			if ( $count == 1 ) {
				$this->_checkOrientation();
				$this->_createThumbs();
			}
		}

		return $this->name;
    }

    // -------- -------- -------- -------- -------- -------- -------- -------- // Stuff & Script

    //  _checkFolder
    protected function _checkFolder()
    {
		if ( !is_dir( $this->folder ) ) {
			Folder::create( $this->folder );
		}
    }

    //  _checkOrientation
    protected function _checkOrientation()
    {
    	$location 					=	$this->folder.'/'.$this->name;
		$image 						=	new JCckDevImage( $location );
		$image->rotate();
    }

    //  _createThumbs
    protected function _createThumbs()
    {
    	$location 					=	$this->folder.'/'.$this->name;
    	$options 					=	$this->options;
		$image 						=	new JCckDevImage( $location );

		if ( $this->extension !== 'webp' ) {
			$image->createWebp();
		}
		
		$src_w						=	$image->getWidth();
		$src_h						=	$image->getHeight();
		
		$options['thumb0_process']	=	$options['image_process'];
		$options['thumb0_width']	=	$options['image_width'];
		$options['thumb0_height']	=	$options['image_height'];	

		for ( $i = 0; $i <= 10; $i++ ) {
			$coef 			=	1;
			$format_name	=	'thumb'.$i.'_process';

			if ( $options[$format_name] == '0' || $options[$format_name] == '' ) {
				continue;
			}			

			if ( $options[$format_name] == 'quotient' ) {
				if ( (int)$options['thumb'.$i.'_width'] > 0 ) {
					$coef 	=	$options['thumb'.$i.'_width'] / $options['thumb'.$i.'_height'];
				} else {
					$coef 	=	floatval( '0.'.$options['thumb'.$i.'_height'] );
				}
				$width_name		=	'thumb'.$options['thumb'.$i.'_cropping'].'_width';
				$height_name	=	'thumb'.$options['thumb'.$i.'_cropping'].'_height';
				$watermark_name	=	'thumb'.$options['thumb'.$i.'_cropping'].'_wmk';	
				$format_name 	=	'thumb'.$options['thumb'.$i.'_cropping'].'_process';
			} else {
				$width_name		=	'thumb'.$i.'_width';
				$height_name	=	'thumb'.$i.'_height';
				$watermark_name	=	'thumb'.$i.'_wmk';
			}

			$width 	=	ceil( $options[$width_name] * $coef );
			$height =	ceil( $options[$height_name] * $coef );

			if ( $i == 0 && $src_w == $options[$width_name] && $src_h == $options[$height_name] ) {
				continue;
			}

			if ( $options[$format_name] != '0' && $options[$format_name] != '' ) {

				$image->createThumb( '', $i, $width, $height, $options[$format_name] );

				// Watermark
				if ( isset( $options[$watermark_name][0] ) ) {
					$wmk 		= 	new watermark( $this->folder.'/_thumb'.$i.'/'.$this->name, $options );
					$wmk->setWatermark();
				}
			}
		}
    }

    //  _getSafeName
    protected function _getSafeName( $name )
    {
		$file_name	=	File::stripExt( $name );
		$file_ext	=	strtolower( File::getExt( $name ) );

		return JCckDev::toSafeSTRING( $file_name, JCck::getConfig_Param( 'media_characters', '-' ), JCck::getConfig_Param( 'media_case', 0 ) ).'.'.$file_ext;
    }

    //  _setSession
    protected function _setSession()
    {
		$session				=	Factory::getSession();
		$files 					=	$session->get( $this->fid, '' );
		$session->set( $this->fid, $this->uuid.'||'.$this->name );	
    }
}
?>