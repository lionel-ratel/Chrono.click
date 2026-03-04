<?php
/**
* @version 			SEBLOD 2.x More
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2012 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/
defined( '_JEXEC' ) or die;

use Joomla\Filesystem\File;

class watermark
{
	public $_height 			=	0;
	public $_extension 		=	'';
	public $_pathinfo 		=	NULL;
	public $_quality_jpg		=	90;
	public $_quality_png		=	3;
	public $_ratio 			=	0;
	public $_resource 		=	NULL;
	public $_width 			=	0;
	public $_font 			=	'';
	public $_type 			=	1;
	public $_options 		=	array();

	// __construct
	function __construct( $path, $options )
	{
		$this->_quality_jpg	=	JCck::getConfig_Param( 'media_quality_jpeg', 90 );
		$this->_quality_png	=	JCck::getConfig_Param( 'media_quality_png', 3 );
		
		$this->_pathinfo 	=	pathinfo( $path );
		$this->_extension	=	strtolower( $this->_pathinfo['extension'] );
		
		$this->_resource 	= 	$this->_createResource( $this->_extension, $path );
		list( $this->_width, $this->_height )	=	getimagesize( $path );
		$this->_ratio 		= 	$this->_width / $this->_height;

		$this->_options 	=	$options;
		$this->_type 		=	$this->_options['add_watermark'];
		$this->_font 		=	JPATH_SITE . '/plugins/cck_field/upload_image2/assets/fonts/arial.ttf';		

		if ( $this->_type == 2 && $this->_options['watermark_text_font'] ) {
			$font = JPATH_SITE . $this->_options['watermark_text_font'];
			if ( is_file( $font ) ) {
				$this->_font 	=	$font;
			} else {
			}
		}
		
	}

	// __call
	public function __call( $method, $args )
	{
		$prefix		=	strtolower( substr( $method, 0, 3 ) );
		$property	=	strtolower( substr( $method, 3 ) );
		
		if ( empty( $prefix ) ) {
			return;
		}
		
        if ( $prefix == 'get' ) {
        	$target	=	'_'.$property;

        	if ( isset( $this->$target ) ) {
        		return $this->$target;
        	}
		}
	}

	// setWatermark
	public function setWatermark( )
	{
		if ( $this->_type == 1 ) {
			$this->_setImageWatermark( 
							$this->_options['watermark_position'], $this->_options['watermark_offsetX'], $this->_options['watermark_opacity'],
							JPATH_SITE.'/'.$this->_options['watermark_image_path'], $this->_options['watermark_image_scale']
						);
		} else {
			$this->_setTextWatermark( 
							$this->_options['watermark_position'], $this->_options['watermark_offsetX'], $this->_options['watermark_opacity'],
							$this->_options['watermark_text'], $this->_options['watermark_text_size'], $this->_options['watermark_text_color']
						);
		}

		return true;
	}

	// _setImageWatermark
	protected function _setImageWatermark( $position, $margin, $opacity = 50, $path, $scale = 100 )
	{
		if ( is_file( $path ) ) {

			$newExt 		=	File::getExt( $path );
			$mask 			= 	new JCckDevImage( $path );
			$mask_resource 	=	$mask->getResource();
			$waterW 		=	$mask->getWidth();
			$waterH 		=	$mask->getHeight();
			$waterRatio 	=	$mask->getRatio();

			// set Watermark Size
			$newWidth 		=	floor( ( $this->_width * $scale ) / 100 );
			$newHeight 		=	floor( ( $this->_height * $scale ) / 100 );

			//	Maxfit if needed
			$width			=	( $waterW > $newWidth ) ? $newWidth : $waterW;
			$height			=	( $waterH > $newHeight ) ? $newHeight : $waterH;
			$width			=	( $this->_ratio > $waterRatio ) ? round( $height * $waterRatio ) : $width;
			$height			=	( $this->_ratio < $waterRatio ) ? round( $width / $waterRatio ) : $height;

			$resize = $width != $waterW || $height != $waterH;

			//	Resize Watermark
			if( $resize ) {
				$thumbMask	=	imageCreateTrueColor( $width, $height );
				$color 		= 	@imagecolortransparent( $thumbMask, @imagecolorallocate( $thumbMask, 0, 0, 0 ) );
				if ( $newExt == 'png' || $newExt == 'PNG' ) {
					imagealphablending( $thumbMask, false );
				}
				imagecopyresampled( $thumbMask, $mask_resource, 0, 0, 0, 0, (int)$width, (int)$height, (int)$waterW, (int)$waterH );
			} else {
				$thumbMask = $mask_resource;
			}

			$dst_x 	=	$this->_getWatermarkX( $position, $margin, $this->_width, $width );
			$dst_y 	=	$this->_getWatermarkY( $position, $margin, $this->_height, $height );

			$this->_imagecopymerge_alpha( $this->_resource, $thumbMask, $dst_x, $dst_y, 0, 0, $width, $height, $opacity );
			$this->_generateThumb( $this->_extension, $this->_resource, $this->_pathinfo['dirname'].'/'.$this->_pathinfo['basename'] );
		}
	}

	// _setTextWatermark
	protected function _setTextWatermark( $position, $margin, $opacity = 0, $text, $size = 32, $text_color = '#FFFFFF' )
	{
		$rgb 		= 	$this->_getRGBColor( $text_color );
		$color 		= 	imagecolorallocatealpha( $this->_resource, $rgb['r'], $rgb['g'], $rgb['b'], 127 - ( $opacity / 100 * 127 )  );
		$size 		=	( is_numeric( $size ) )? $size : 32;
		$bbox 		= 	imagettfbbox( $size, 0, $this->_font, $text );
		$dst_x 		=	$this->_getWatermarkX( $position, $margin, $this->_width, $bbox['2'] );
		$dst_y 		=	$this->_getWatermarkY( $position, $margin, $this->_height, abs( $bbox['5'] ) ) + abs( $bbox['5'] );
		imagettftext( $this->_resource, $size, 0, $dst_x, $dst_y, $color, $this->_font, $text );
		$this->_generateThumb( $this->_extension, $this->_resource, $this->_pathinfo['dirname'].'/'.$this->_pathinfo['basename'] );
	}

	// _createResource
	protected function _createResource( $ext, $path )
	{
		$ext = strtolower( $ext );
		if ( $ext == 'gif' ) {
			$res	=	@imagecreatefromgif( $path );
		} elseif( $ext == 'jpg' || $ext == 'jpeg' ) {
			$res	=	@imagecreatefromjpeg( $path );
		} elseif( $ext == 'png' ) {
			$res	=	@imagecreatefrompng( $path );
		} elseif( $ext == 'webp' ) {
			$res	=	@imagecreatefromwebp( $path );
		} else {
			$res 	= 	false;
		}

		return $res;
	}

	//	_generateThumb
	protected function _generateThumb( $ext, $resource, $path )
	{
		ob_start();	
		$ext = strtolower( $ext );
		if ( $ext == 'gif' ) {
			imagegif( $resource );
		} elseif ( $ext == 'jpg' || $ext == 'jpeg' ) {
			imagejpeg( $resource, NULL, JCck::getConfig_Param( 'media_quality_jpeg', 90 ) );
		} elseif ( $ext == 'png' ) {
			header('Content-Type: image/png');			
			imagepng( $resource, NULL, JCck::getConfig_Param( 'media_quality_png', 8 ) );
		} elseif ( $ext == 'webp' ) {
			imagewebp( $resource, null, $this->_quality_webp );
		} else {
			// Bad extension !
		}

		$output	=	ob_get_contents();
		ob_end_clean();
		
		File::write( $path, $output );	
	}

	// _imagecopymerge_alpha 
    protected function _imagecopymerge_alpha( &$dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct ) {

        $tmp = imagecreatetruecolor( $src_w, $src_h );
        imagecopy( $tmp, $dst_im, 0, 0, $dst_x, $dst_y, $src_w, $src_h );
        imagecopy( $tmp, $src_im, 0, 0, $src_x, $src_y, $src_w, $src_h );
       	imagecopymerge( $dst_im, $tmp, $dst_x, $dst_y, 0, 0, $src_w, $src_h, $pct );

       	return $dst_im;
    }

	// _getWatermarkX 
    protected function _getWatermarkX( $wp, $m, $iw, $w ) {

		if( $wp == 1 || $wp == 4 || $wp == 7 ) {
			$x 	=	( $iw - $w )  / 2;
		} elseif( $wp == 2 || $wp == 5 || $wp == 8 ) {
			$x 	=	( $iw - $w ) - $m;
		} else {
			$x 	=	$m;
		}
		return $x;
    }

	// _getWatermarkY
    protected function _getWatermarkY( $wp, $m, $ih, $h ) {

		if( $wp == 3 || $wp == 4 || $wp == 5 ) {
			$y 	=	( $ih - $h )  / 2;
		} elseif( $wp == 6 || $wp == 7 || $wp == 8 ) {
			$y 	=	( $ih - $h ) - $m;
		} else {
			$y 	=	$m;
		}
		return $y;
    }

	//	_getRGBColor
	protected function _getRGBColor( $hexa ) {
		$hexa 	= 	str_replace( '#', '', $hexa );
		$rgb 	=	array(); 
		if( strlen( $hexa ) == 3 ){
			$hexa = substr( $hexa, 0, 1 ).substr( $hexa, 0, 1 ).substr( $hexa, 1, 1 ).substr( $hexa, 1, 1 ).substr( $hexa, 2, 1 ).substr( $hexa, 2, 1 );
		} else {
			$hexa = str_pad( $hexa, 6, "0", STR_PAD_LEFT);
		}
		$rgb['r'] = (int) hexdec( substr( $hexa, 0, 2 ) );
		$rgb['g'] = (int) hexdec( substr( $hexa, 2, 2 ) );
		$rgb['b'] = (int) hexdec( substr( $hexa, 4, 2 ) );
		return $rgb;
	}

}