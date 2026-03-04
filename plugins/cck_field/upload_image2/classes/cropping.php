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
use Joomla\Filesystem\Folder;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

if ( !class_exists( 'watermark', false ) ) {
	include_once ( __DIR__.'/watermark.php' );
}

class cropping
{
	protected $_type		=	'upload_image2';

	public $_fid 			=	0;
	public $_force 			=	0;
	public $_pk 			=	0;
	public $_thumb 			=	0;
	public $_uuid 			=	'';
	public $_version 		=	'';

	//
	public $_options2		=	array();
	public $_field 			=	'';
	public $_field2 		=	'';
	public $_table 			=	'';
	public $_name 			=	'';

	public $_value 			=	'';
	public $_width 			=	0;
	public $_height			=	0;
	public $_wwmax 			=	0;
	public $_whmax 			=	0;

	public $_basename 		=	'';
	public $_dirname 		=	'';
	public $_extension 		=	'';
	public $_filename 		=	'';
	public $_path 			=	'';
	public $_url 			=	'';
	public $_paththumb 		=	'';
	public $_pathtmp 		=	'';
	public $_urltmp 		=	'';

	public $_pl 			=	0;
	public $_wpl 			=	0;
	public $_hpl 			=	0;
	public $_selection 		=	array();
	public $_matrix 		=	1;
	public $_color 			=	'';
	public $_cropped		=	0;

	public $_thumbs 		=	array();

	//
	protected $_quality_webp	=	85;

	//	__construct
    function __construct( $data ) 
    {
		Factory::getLanguage()->load( 'plg_cck_field_'.$this->_type, JPATH_ADMINISTRATOR, null, false, true );
		$app 				= 	Factory::getApplication();
		$thumbs_number		=	10;

		$this->_fid 		=	$data['fid'];
		$this->_force 		=	(int)$data['force'];
		$this->_pk 			=	$data['pk'];
		$this->_thumb 		=	$data['thumb'];
		$this->_uuid 		=	( isset( $data['uuid'] ) ) ? $data['uuid'] : '';

		$this->_wwmax 		=	( isset( $data['ww'] ) ) ? $data['ww'] : 800;
		$this->_whmax 		=	( isset( $data['wh'] ) ) ? $data['wh'] : 500;
		$this->_pl 			=	( isset( $data['pl'] ) ) ? $data['pl'] : 0;
		$this->_wpl 		=	( isset( $data['wpl'] ) ) ? $data['wpl'] : 0;
		$this->_hpl 		=	( isset( $data['hpl'] ) ) ? $data['hpl'] : 0;

		$this->_selection 	=	( isset( $data['selection'] ) ) ? $data['selection'] : array();
		$this->_matrix 		=	( isset( $data['matrix'] ) ) ? $data['matrix'] : 1;
		$this->_color 		=	( isset( $data['color'] ) ) ? $data['color'] : '';
		$this->_cropped		=	( isset( $data['cropped'] ) ) ? (int)$data['cropped'] : 0;

		$query 				=	'SELECT name, storage_table, storage_field, storage_field2, options2 FROM #__cck_core_fields WHERE id='.(int)$this->_fid;
		$field				=	JCckDatabase::loadObject( $query );

		$this->_name 		=	$field->name;
		$this->_options2 	=	json_decode( $field->options2, true );
		$this->_preview		=	$this->_options2['content_preview'];
 		$this->_table 		=	$field->storage_table;
		$this->_field 		=	$field->storage_field;
		$this->_field2 		=	$field->storage_field2;

		// Urls
		$cdn	=	'';

		if ( method_exists( 'JCck', 'getCdn' ) ) {
			$cdn			=	JCck::getCdn();	
		}

		$url_root			=	$cdn ? $cdn.'/' : Uri::root();
		$url_base			=	$cdn ? $cdn.'/' : Uri::base();

		$this->_value		=	$data['value'];
		$this->_version		=	$data['version'];

		$root_folder		=	( $this->_uuid != '' ) ? JPATH_SITE : JPATH_RESOURCES;
		$this->_path 		= 	( $this->_uuid != '' ) ? JPATH_SITE.'/tmp/'.$this->_uuid : JPATH_RESOURCES.'/'.$this->_options2['path'].$this->_pk;

		list( $this->_dirname, $this->_basename, $this->_extension, $this->_filename )	=	array_values( pathinfo( $this->_path.'/'.$this->_value ) );

		$this->_pathtmp 	= 	$this->_dirname.'/tmp_'.$this->_basename;

		// ----

		$this->_url 		= 	str_replace( $root_folder.'/', $url_base, $this->_path.'/'.$this->_value );
		$this->_urltmp 		= 	str_replace( $root_folder.'/', $url_base, $this->_pathtmp ).'?'.substr( microtime(), -10 );

		if ( $app->isClient( 'administrator' ) ) {
			$this->_url 	=	str_replace( 'administrator/', '', $this->_url );
			$this->_urltmp 	=	str_replace( 'administrator/', '', $this->_urltmp );
		}

		list( $this->_width, $this->_height ) 	=	getimagesize( $this->_path.'/'.$this->_value );

		// Thumbs
		if ( isset( $data['thumbs'] ) && $data['thumbs'] != '' ) {
			$thumbs 	=	explode( ',', $data['thumbs'] );
			$from_field =	false;
		} else {
			$thumbs 	=	range( 1, $thumbs_number );
			$from_field =	true;
		}

		$fromModel 	=	array();

		for ( $i = 1 ; $i <= $thumbs_number ; $i++ ) {
			$params 	= 	$this->_getThumbParams( $i );

			if ( $params === false || $params['process'] == '0' ) {			
				continue;
			}
			if ( $params['cropping'] == '-1' ) {
				continue;
			}

			if ( $params['cropping'] == '0' || $params['cropping'] == '-2' ) {
				if ( !in_array( $i, $thumbs ) ) {
					continue;
				}
				if ( $params['cropping'] == '-2' && $from_field ) {
					continue;
				}

				$this->_thumbs[$i] 	= 	$params;
			} else {
				if ( !in_array( $params['cropping'], $thumbs ) ) {
					continue;
				}

				$parent 	=	$this->_getThumbParams( $params['cropping'] );

				if ( $parent['cropping'] == '-2' && $from_field ) {
					continue;
				}

				$fromModel[$params['cropping']][$params['thumb']] 	= 	$params;
			}
		}

		foreach ( $fromModel as $key => $model ) {
			$this->_thumbs[$key]['other'] = $model; 
		}
	}

	//	getArea
    public function getArea() 
    {
    	//	Create Select
    	$options 	= 	array();

    	reset( $this->_thumbs );

    	$k 	=	$this->_thumb;

    	if ( !$k ) {
    		reset( $this->_thumbs );
    		$k	=	key( $this->_thumbs );
    	}

    	$select 		= 	$this->_getSelect( $this->_thumbs, $k );
    	$data_thumbs 	=	array_keys( $this->_thumbs );

    	//
    	$attr 		=	' data-name="'.$this->_name.'" data-fid="'.$this->_fid.'" data-pk="'.$this->_pk.'" data-uuid="'.$this->_uuid.'"';
    	$attr 		.=	' data-wpl="" data-hpl="" data-pl="0" data-thumb="0"';
    	$attr 		.=	' data-value="'.$this->_value.'"';
    	$attr 		.=	' data-thumbs="'.implode( ',', $data_thumbs ).'"';
    	$attr		.=	' data-preview="'.$this->_preview.'"';
    	$attr		.=	' data-version="'.$this->_version.'"';

		//
		if ( JCck::on( '4.0' ) ) {
			$close			=	' data-bs-dismiss="modal"';
			$tool_expand	=	'<button type="button" class="set-expand btn cropTooltip" onclick="JCck.More.CropX.expand(this);" data-desc="expand_desc">
									<span class="icon-expand"></span>
								</button>';
			$tool_contract	=	'<button type="button" class="set-contract btn cropTooltip" onclick="JCck.More.CropX.contract(this);" data-desc="contract_desc">
									<span class="icon-contract"></span>
								</button>';
			$tool_center	=	'<button type="button" class="set-center btn cropTooltip" onclick="JCck.More.CropX.center(this);" data-desc="center_desc">
									<span class="icon-radio-checked"></span>
								</button>';
			$tool_slider	=	'<div class="toolbar-btn">
									<div class="btn-group zoom-buttons ">
										<button type="button" id="zoom-out" class="zoom zoom-out btn hide"><span class="icon-minus"></span></button>
										<div class="btn cropTooltip" data-desc="zoom_desc"><input type="range" id="zoom-range" class="zoom-range hide" min="0.1" max="1" step="0.005" value="1"></div>
										<button type="button" id="zoom-in" class="zoom zoom-in btn hide"><span class="icon-plus"></span></button>
									</div>
								</div>';
			$tools_color	=	'<div class="toolbar-btn cropTooltip" data-desc="color_desc"><input id="crop-color" class="btn" /></div>';								
		} else {
			$close			=	' data-dismiss="modal"';
			$tool_expand	=	'<button type="button" class="set-expand btn cropTooltip" onclick="JCck.More.CropX.expand(this);" data-desc="expand_desc">
									<span class="icon-expand icon-expand-2"></span>
								</button>';
			$tool_contract	=	'<button type="button" class="set-contract btn cropTooltip" onclick="JCck.More.CropX.contract(this);" data-desc="contract_desc">
									<span class="icon-contract icon-contract-2"></span>
								</button>';
			$tool_center	=	'<button type="button" class="set-center btn cropTooltip" onclick="JCck.More.CropX.center(this);" data-desc="center_desc">
									<span class="icon-radio-checked"></span>
								</button>';
			$tool_slider	=	'<div class="toolbar-btn">
									<div class="btn-group zoom-buttons ">
										<button type="button" id="zoom-out" class="zoom zoom-out btn hide"><span class="icon-minus icon-minus-2"></span></button>
										<div class="btn cropTooltip" data-desc="zoom_desc"><input type="range" id="zoom-range" class="zoom-range hide" min="0.1" max="1" step="0.005" value="1"></div>
										<button type="button" id="zoom-in" class="zoom zoom-in btn hide"><span class="icon-plus icon-plus-2"></span></button>
									</div>
								</div>';
			$tools_color	=	'<div class="toolbar-btn cropTooltip" data-desc="color_desc"><input id="crop-color" class="btn" /></div>';
		}
		
		$result['name'] 		= 	$this->_name;
		$result['container'] 	=	'#'.$this->_name;
		$result['area'] 		= 	'<div id="toolbar-crop"'.$attr.'>								
										<div class="toolbar-btn">'.$select.'</div>
										<div class="toolbar-btn"><button type="button" class="set-crop btn btn-success" onclick="JCck.More.CropX.crop(this);">'.Text::_( 'COM_CCK_CROP' ).'</button></div>
										<div class="toolbar-btn btn-group btn-t">
											'.$tool_expand.'								
											'.$tool_contract.'								
											'.$tool_center.'
										</div>
										'.$tool_slider.'
										'.$tools_color.'
										<div class="toolbar-btn">
											<button type="button" class="crop-help btn cropTooltip" data-desc="help_desc">
												<span class="icon-help"></span>
											</button>								
										</div>
										<div><button type="button" class="close"'.$close.'><span>&times;</span></button></div>
									</div>	
									<div id="color-wrapper"></div>
									<div id="canvas">
										<div id="resize-parent2">
											<div id="resize-parent"></div>
										</div>
										<div id="tooltip-desc"></div>
									</div>';

		$result['i8n'] 			= 	array(
										'crop'=>Text::_( 'COM_CCK_CROP' ),
										'again'=>Text::_( 'COM_CCK_CROP_AGAIN' ),
										'cancel'=>Text::_( 'COM_CCK_CANCEL' ),
										'choose'=>Text::_( 'COM_CCK_CHOOSE' ),
										'expand_desc'=>Text::_( 'COM_CCK_EXPAND_DESC' ),
										'expanded'=>Text::_( 'COM_CCK_EXPANDED_SELECTION_DONE' ),
										'contract_desc'=>Text::_( 'COM_CCK_CONTRACT_DESC' ),
										'contracted'=>Text::_( 'COM_CCK_CONTRACTED_SELECTION_DONE' ),
										'cropped'=>Text::_( 'COM_CCK_SUCCESSFULLY_CROPPED' ),
										'error'=>Text::_( 'COM_CCK_CROP_ERROR' ),
										'loaded'=>Text::_( 'COM_CCK_THUMB_LOADED' ),
										'center_desc'=>Text::_( 'COM_CCK_CENTER_DESC' ),
										'centered'=>Text::_( 'COM_CCK_SELECTION_AREA_CENTERED' ),
										'zoom_desc'=>Text::_( 'COM_CCK_ZOOM_DESC' ),
										'color_desc'=>Text::_( 'COM_CCK_COLOR_DESC' ),
										'help_desc'=>Text::_( 'COM_CCK_HELP_DESC' ),
										'alert'=>Text::_( 'COM_CCK_ROTATE_ALERT' )
									);

		return json_encode( $result );
	}

	//	getThumb
	public function getThumb()
	{	
		$thumb 			=	$this->_thumbs[$this->_thumb];
		$extension 		=	strtolower( $this->_extension );
		$alpha 			=	$extension == 'png' || $extension == 'gif' || $extension == 'webp';
		$position 		=	array( 'thumb'=>$this->_thumb );

		//
		$position['method'] 	=	(int)$this->_options2['default_method'];
		$position['picker'] 	=	(boolean)$this->_options2['picker'];

		if ( $position['picker'] ) {
			$colors 				=	explode( ',', str_replace( array( "\n", ',,' ), ',', $this->_options2['palette'] ) );
			foreach ( $colors as $key => $color ) {
				$palette[($key / 2)][] = $color;
				$k = $key / 2;
			}

			if ( $alpha ) {
				$palette[$k][] = 'rgba(255, 255, 255, 0);';
			}

			$position['palette']	=	json_encode( $palette );
		}
		
		$json 			=	$this->_getPositions();
		if ( !isset( $json[$this->_thumb]['x'] ) ) {

			$position['x'] 	= 	'0';
			$position['y'] 	= 	'0';
			$position['w'] 	= 	$thumb['width'];
			$position['h'] 	= 	$thumb['height'];
			$position['cropped'] 	=	0;
		} else {

			$position['x'] 	= 	$json[$this->_thumb]['x'];
			$position['y'] 	= 	$json[$this->_thumb]['y'];
			$position['w'] 	= 	$json[$this->_thumb]['w'];
			$position['h'] 	= 	$json[$this->_thumb]['h'];
			$position['cropped'] 	=	1;
		}

		if ( isset( $json[$this->_thumb]['color'] ) ) {
			$position['color'] 	=	$json[$this->_thumb]['color'];
		} else {
			if ( $this->_options2['default_color'] ) {
				$position['color'] 	=	@$this->_options2['default_color'];
			} else {
				$position['color'] 	=	( $alpha ) ? 'rgba(255,255,255,0);' : '#FFFFFF';
			}
		}
		$color 						=	( $position['color'] != 'rgba(255,255,255,0);' ) ? $position['color'] : 'transparent';

		$position['ext'] 			= 	$extension;
		$position['zoom'] 			=	isset( $json[$this->_thumb]['matrix'] ) ? $json[$this->_thumb]['matrix'] : 1;
		$position['wmin'] 			= 	$thumb['width'];
		$position['hmin'] 			= 	$thumb['height'];
		$position['aspectRatio'] 	= 	$thumb['width'].':'.$thumb['height'];

		$imageResource 				= 	$this->_createResource( $this->_extension, $this->_path.'/'.$this->_value );

		if ( $thumb['width'] >= $this->_width && $thumb['height'] >= $this->_height ) {
    //  Image Embeded in Thumb - IRW = TW / IRH = TH         => PL = 2
    //  CORRECTION: Gestion spéciale quand image = thumb exactement
        $position['pl']             =   2;
        $position['w']              =   (int)$this->_width;
        $position['h']              =   (int)$this->_height;
        $position['wmin']           =   (int)$this->_width;
        $position['hmin']           =   (int)$this->_height;
        $position['zoom']           =   false;
        $position['aspectRatio']    =   $this->_width.':'.$this->_height;
        $position['wtrue']          =   (int)$thumb['width'];
        $position['htrue']          =   (int)$thumb['height'];

        list( $position['wpl'], 
                $position['hpl'] )  =   $this->_checkSizePlaceholder( (int)$thumb['width'], (int)$thumb['height'] );

        // CORRECTION: Calculer le ratio d'échelle pour les conversions JS/PHP
        $scaleX = $position['wpl'] / (int)$thumb['width'];
        $scaleY = $position['hpl'] / (int)$thumb['height'];
        $position['scaleX'] = $scaleX;
        $position['scaleY'] = $scaleY;

        // CORRECTION: Calculer les dimensions d'affichage de l'image
        $imgDisplayW = (int)round($this->_width * $scaleX);
        $imgDisplayH = (int)round($this->_height * $scaleY);

        // CORRECTION: Position centrée par défaut si pas encore croppé
        if (!isset($json[$this->_thumb]['x'])) {
            $position['x'] = (int)floor(((int)$thumb['width'] - $this->_width) / 2);
            $position['y'] = (int)floor(((int)$thumb['height'] - $this->_height) / 2);
        }

        $position['placeholder']    =   '<div id="target" style="position:relative;width:'.$position['wpl'].'px;height:'.$position['hpl'].'px;background-color:'.$color.';overflow:hidden;">'
                                    .   '<img id="panzoom" class="panzoom" style="width:'.$imgDisplayW.'px;height:'.$imgDisplayH.'px;position:absolute;left:0;top:0;display:block;max-width:none;" src="'.$this->_url.'" />'
                                    .   '</div>';
				$position['embeded'] 		= 	$this->_url;
				$newResource 				= 	$this->_createNewResource( $position['wpl'], $position['hpl'], '' );
				$newResource 				=	$this->_resize( 
															$newResource, $imageResource, 
															0, 0, 0, 0, 
															$position['wpl'], $position['hpl'], $this->_width, $this->_height 
														);

		} elseif ( $thumb['width'] >= $this->_width && $thumb['height'] <= $this->_height ) {
			//	Thumb Width > Image Width - IRW = TW / IRH = IRH 	=> PL = 3
				$position['pl']				= 	3;
				$position['zoom']	 		= 	false;
				$position['wtrue'] 			= 	$thumb['width'];
				$position['htrue'] 			= 	$this->_height;

				list( $position['wpl'], 
						$position['hpl'] ) 	= 	$this->_checkSizePlaceholder( $position['wtrue'], $position['htrue'] );

				$width  					=	round( $this->_width / $this->_height * $position['hpl'] );
				$x 							=	( $position['wpl'] - $width ) / 2;

				$position['placeholder'] 	= 	'<div id="target" style="position:relative;width:'.$position['wpl'].'px;height:'.$position['hpl'].'px;background-color:'.$color.';">'
											. 	'<img id="panzoom" class="panzoom" style="height:'.$position['hpl'].'px;position:absolute;left:'.$x.'px;top:0;" src="'.$this->_urltmp.'" />'
											. 	'</div>';
				$newResource 				= 	$this->_createNewResource( $width, $position['hpl'], '' );
				$newResource 				=	$this->_resize( 
															$newResource, $imageResource, 
															0, 0, 0, 0, 
															$width, $position['hpl'], $this->_width, $this->_height 
														);

		} elseif ( $thumb['width'] <= $this->_width && $thumb['height'] >= $this->_height ) {
			//	Thumb Height > Image Height - IRW = IRW / IRH = TH 	=> PL = 4
				$position['pl']				= 	4;
				$position['zoom']	 		= 	false;
				$position['wtrue']			=	$this->_width;
				$position['htrue']			=	$thumb['height'];

				list( $position['wpl'], 
						$position['hpl'] ) 	= 	$this->_checkSizePlaceholder( $position['wtrue'], $position['htrue'] );

				$height  					=	$this->_height / $this->_width * $position['wpl'];
				$y 							=	( $position['hpl'] - $height ) / 2;

				$position['placeholder'] 	= 	'<div id="target" style="position:relative;width:'.$position['wpl'].'px;height:'.$position['hpl'].'px;background-color:'.$color.';">'
											.	'<img id="panzoom" class="panzoom" style="width:'.$position['wpl'].'px;position:absolute;left:0;top:'.$y.'px;" src="'.$this->_urltmp.'" />'
											. 	'</div>';
				$newResource 				= 	$this->_createNewResource( $position['wpl'], $height, '' );
				$newResource 				=	$this->_resize( 
															$newResource, $imageResource, 
															0, 0, 0, 0, 
															$position['wpl'], $height, $this->_width, $this->_height 
														);

		} elseif ( $this->_width >= $thumb['width'] && $this->_height >= $thumb['height'] ) {
			//	Thumb Embeded in Image - IRW = IRW / IRH = IRH 		=> PL = 1
				$position['pl']				= 	1;
				$position['wtrue'] 			= 	(int)$this->_width;
				$position['htrue'] 			= 	(int)$this->_height;

				list( $position['wpl'], 
						$position['hpl'] )	= 	$this->_checkSizePlaceholder( $position['wtrue'], $position['htrue'] );

				$position['placeholder'] 	= 	'<div id="target" style="position:relative;width:'.$position['wpl'].'px;height:'.$position['hpl'].'px;background-color:'.$color.';">'
											. 	'<img id="panzoom" class="panzoom" style="position:absolute;left:0;top:0;" src="'.$this->_urltmp.'" />'
											. 	'</div>';
				$newResource 				= 	$this->_createNewResource( $position['wpl'], $position['hpl'], '' );
				$newResource 				=	$this->_resize( 
															$newResource, $imageResource, 
															0, 0, 0, 0, 
															$position['wpl'], $position['hpl'], $this->_width, $this->_height 
														);
		} else {
			// Bad Size
		}

		//	Create Tmp Placeholder
		$this->_createImage( $this->_extension, $newResource, $this->_pathtmp );

		return json_encode( $position );
	}

	//	cropThumbs
    public function cropThumbs() 
    {
		$this->_paththumb 	= 	$this->_dirname.'/_thumb'.$this->_thumb.'/'.$this->_basename;
		$imageResource = $this->_createResource( $this->_extension, $this->_path.'/'.$this->_value );

    	if ( $this->_matrix != '1' ) {
	    	$imageResource = $this->_zoomOut( $imageResource );
    	}

    	$imageResource 	= 	$this->_generateThumb( $imageResource );

		$this->_createImage( $this->_extension, $imageResource, $this->_paththumb );

		if ( JCck::getConfig_Param( 'media_image_webp', 0 ) ) {
			if ( function_exists( 'imagewebp' ) ) {
				$p 		=	str_replace( '.'.$this->_extension, '.webp', $this->_paththumb );			
				imagewebp( $imageResource, $p, 90 );
			}
		}

		$this->_setPositions();
	
		//	Watermark
		if ( $this->_thumbs[$this->_thumb]['wmk'] ) {
			$wmk 	=	new watermark( $this->_paththumb, $this->_options2 );
			$wmk->setWatermark( );
		}

		//	Thumbs based on model
		if ( isset( $this->_thumbs[$this->_thumb]['other'] ) ) {
	    	foreach ( $this->_thumbs[$this->_thumb]['other'] as $key => $t ) {
				$paththumb 		= 	$this->_dirname.'/_thumb'.$key.'/'.$this->_basename;
				$imageResource 	= 	$this->_createResource( $this->_extension, $this->_paththumb );
				$newResource 	= 	$this->_createNewResource( $t['width'], $t['height'], $this->_color );
				$newResource 	=	$this->_resize( 
												$newResource, $imageResource, 
												0, 0, 0, 0, 
												$t['width'], $t['height'], 
												$this->_thumbs[$this->_thumb]['width'], $this->_thumbs[$this->_thumb]['height']
											);

				$this->_createImage( $this->_extension, $newResource, $paththumb );

				if ( JCck::getConfig_Param( 'media_image_webp', 0 ) ) {
					if ( function_exists( 'imagewebp' ) ) {
						$p 		=	str_replace( '.'.$this->_extension, '.webp', $paththumb );	
						imagewebp( $newResource, $p, 90 );
					}
				}

				//	Watermark
				if ( $t['wmk'] ) {
					$wmk 	=	new watermark( $paththumb, $this->_options2 );
					$wmk->setWatermark( );
				}
	    	}
		}

		return	json_encode( array( 
								"name"=>$this->_name,
								"pk"=>$this->_pk,
								"preview"=>$this->_getPreview(), 
								"refresh"=>substr( microtime(), -10 ),
								"version"=>$this->_version
							) );	
	}

	//	setExpand
    public function setExpand() 
    {
    	$expand 	= 	array();
    	$thumb 		= 	$this->_thumbs[$this->_thumb];
	
	   	switch ( $this->_pl ) {
    		case 2:
    			break;
    		case 3:
				$expand['width']    =   (int)round($thumb['width'] / $thumb['height'] * $this->_height);
				$expand['height']	= 	$this->_height;
				$expand['x'] 		=	0;
				$expand['y'] 		=	0;
				$expand['zoom'] 	=	1;
    			break;
    		case 4:
				$expand['width'] 	= 	$this->_width;    				
				$expand['height']	= 	(int)round($thumb['height'] / $thumb['width'] * $this->_width);
				$expand['x'] 		=	0;
				$expand['y'] 		=	0;
				$expand['zoom'] 	=	1;
    			break;
    		default:
		    	$width 		=	$this->_width / $thumb['width'];
		    	$height 	=	$this->_height / $thumb['height'];

    			if ( $height >= $width ) {
    				$expand['width'] 	= 	$this->_width;
    				$expand['height'] 	= 	(int)round($thumb['height'] / $thumb['width'] * $this->_width);
    				$expand['x'] 		=	0;
    				$expand['y'] 		=	(int)round(( $this->_height - $expand['height'] ) / 2 );
    				$expand['zoom'] 	=	(int)round($expand['height'] / $this->_height );
     			} else {
    				$expand['width'] 	= 	(int)round($thumb['width'] / $thumb['height'] * $this->_height);
    				$expand['height']	= 	$this->_height;
    				$expand['x'] 		=	(int)round(( $this->_width - $expand['width'] ) / 2);
    				$expand['y'] 		=	0;
    				$expand['zoom'] 	=	(int)round($expand['width'] / $this->_width);
    			}
    			break;
    	}

    	return json_encode( $expand );
    }

	//	setExpand
    public function setContract() 
    {
    	$contract 	= 	array();
    	$thumb 		= 	$this->_thumbs[$this->_thumb];
	
	   	switch ( $this->_pl ) {
    		case 2:
    			break;
    		case 3:
				$contract['width'] 	= 	(int)round($thumb['width'] / $thumb['height'] * $this->_height);
				$contract['height']	= 	$this->_height;
				$contract['x'] 		=	0;
				$contract['y'] 		=	0;
    			break;
    		case 4:
				$contract['width'] 	= 	$this->_width;    				
				$contract['height']	= 	(int)round($thumb['height'] / $thumb['width'] * $this->_width);
				$contract['x'] 		=	0;
				$contract['y'] 		=	0;
    			break;
    		default:
		    	$width 		=	$this->_width / $thumb['width'];
		    	$height 	=	$this->_height / $thumb['height'];

    			if ( $height >= $width ) {
    				$contract['width'] 	= 	$this->_width;
    				$contract['height'] = 	(int)round($thumb['height'] / $thumb['width'] * $this->_width);
    				$contract['x'] 		=	0;
			    	switch ( $this->_options2['default_position'] ) {
			    		case 't':
			    			$contract['y']	=	0;
			    			break;
			    		case 'b':
			    			$contract['y']	=	(int)round($this->_height - $contract['height']);
			    			break;
			    		default:
		    				$contract['y'] 	=	(int)round(( $this->_height - $contract['height'] ) / 2);
			    			break;
			    	}
     			} else {
    				$contract['width'] 	= 	(int)round($thumb['width'] / $thumb['height'] * $this->_height);
    				$contract['height']	= 	$this->_height;
    				$contract['x'] 		=	(int)round(( $this->_width - $contract['width'] ) / 2);
    				$contract['y'] 		=	0;
    			}
    			break;
    	}

    	return json_encode( $contract );
    }


	//	rotate
	public function rotate()
	{	
		$image 		=	new JCckDevImage( $this->_path.'/'.$this->_value );
		$image->rotate( 90 );

		$options2 	=	$this->_options2;	
		$image 		=	new JCckDevImage( $this->_path.'/'.$this->_value );

		for ( $i = 1; $i <= $options2['thumbs_number']; $i++ ) {
			$format_name	=	'thumb'.$i.'_process';
			$width_name		=	'thumb'.$i.'_width';
			$height_name	=	'thumb'.$i.'_height';
			$watermark_name	=	'thumb'.$i.'_wmk';

			if ( $options2[$format_name] ) {
				$image->createThumb( '', $i, $options2[$width_name], $options2[$height_name], $options2[$format_name] );

				// Watermark
				if ( isset( $options2[$watermark_name][0] ) ) {
					$wmk 	= 	new watermark( $this->_dirname.'/_thumb'.$i.'/'.$this->_basename, $options2 );
					$wmk->setWatermark( );
				}
			}
		}

		//	Clear JSON
		$buffer = '';
		File::write( str_replace( '.'.$this->_extension, '.json', $this->_path.'/'.$this->_value ), $buffer );

		$rotated 	=	Text::_( 'COM_CCK_SUCCESSFULLY_ROTATED' );
		return	json_encode( array( "preview"=>$this->_getPreview(), "refresh"=>substr( microtime(), -10 ), "rotated"=>$rotated ) );	
	}

	//	cleanFile
	public function cleanFile() 
	{
		if ( is_file( $this->_pathtmp ) ) {
			File::delete( $this->_pathtmp );
		}
	}

	// -- Protected

	//	_checkFileJson
	protected function _checkFileJson( $file )
	{
		if( !is_file( $file ) ){
			$buffer	=	'';
			File::write( $file, $buffer );
		}
	}

	//	_checkMaxfit
	protected function _checkMaxfit( $process, $src_w, $src_h, $dst_w, $dst_h )
	{
		if ( $process == 'maxfit' ) {
			if ( !$dst_w || !$dst_h ) {
				if ( $dst_w > $dst_h ) {
					$dst_h = 0;
				} else {
					$dst_w = 0;
				}
			} else {

				if ( $src_w > $src_h ) {
					$dst_h = 0;
				} else {
					$dst_w = 0;
				}
			}

			if ( $dst_w ) {
				$ratio 	= 	$src_h / $src_w;
				$dst_h 	= 	round( $ratio * $dst_w );
			} else {
				$ratio 	= 	$src_w / $src_h;
				$dst_w 	= 	round( $ratio * $dst_h );
			}
		}
		return array( $dst_w, $dst_h );
	}

	//	_checkSizePlaceholder
	protected function _checkSizePlaceholder( $wpl, $hpl )
	{
		if ( $hpl > $this->_whmax ) {
			$ratio = $wpl / $hpl;
			$hpl = $this->_whmax;
			$wpl = ceil( $hpl * $ratio );
		}

		if ( $wpl > $this->_wwmax ) {
			$ratio = $hpl / $wpl;
			$wpl = $this->_wwmax;
			$hpl = ceil( $wpl * $ratio );
		}

		return array( $wpl, $hpl );
	}

	//	_createFolders
	protected function _createFolders( $image, $i )
	{
		if ( !is_dir( $image['dirname'].'/_thumb'.$i ) ){
			Folder::create( $image['dirname'].'/_thumb'.$i );
		}
		File::copy( $image['dirname'].'/'.$image['basename'], $image['dirname'].'/_thumb'.$i.'/'.$image['basename'] );
	}

	//	_createImage
	protected function _createImage( $ext, $resource, $path )
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
			header('Content-Type: image/webp');			
			imagewebp( $resource, NULL, $this->_quality_webp );
		} else {
			// Bad extension !
		}

		$output	=	ob_get_contents();
		ob_end_clean();
		
		File::write( $path, $output );	
	}

	//	_createNewResource
	protected function _createNewResource( $width, $height, $color )
	{
		ini_set('memory_limit','256M');

		$resource	=	imageCreateTrueColor( $width, $height );
		$extension 	=	strtolower( $this->_extension );

		if ( ( $extension == 'webp' || $extension == 'png' || $extension == 'gif' ) && $color == '' ) {
			imagealphablending( $resource, false );
		}

		if ( $color  ) { 
			$rgb	=	$this->_getRGBColor( $color );
			$color	=	@imagecolorallocate( $resource, $rgb['r'], $rgb['g'], $rgb['b'] );
		} else {
			@imagesavealpha( $resource, true );
		    $color = @imagecolorallocatealpha( $resource, 0, 0, 0, 127 );
		}

		@imagefill( $resource, 0, 0, $color );

		return $resource;
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
	protected function _generateThumb( $imageResource )
	{
		$thumb = $this->_thumbs[$this->_thumb];

    // CORRECTION: Forcer les entiers pour éviter les décalages de pixel
    $src_x      =   (int)round($this->_selection['x1']);
    $src_y      =   (int)round($this->_selection['y1']);
    $src_w      =   (int)round($this->_selection['width']);
    $src_h      =   (int)round($this->_selection['height']);
    
    // CORRECTION: Valider les limites
    $src_x      =   max(0, $src_x);
    $src_y      =   max(0, $src_y);

		switch ( $this->_pl ) {
			case 4: //	Thumb Height > Image Height
				$y 				=	round( ( $thumb['height'] - $this->_height ) / 2 );
				$newResource 	= 	$this->_createNewResource( $this->_width, $thumb['height'], $this->_color );
				$imageResource 	=	$this->_merge( 
												$newResource, $imageResource, 
												0, $y, 0, 0, 
												$this->_width, $this->_height, 
												$pct = 100 
											);

				$newResource 	= 	$this->_createNewResource( $thumb['width'], $thumb['height'], $this->_color );
				$imageResource 	=	$this->_resize( 
												$newResource, $imageResource, 
												0, 0, $src_x, $src_y, 
												$thumb['width'], $thumb['height'], $src_w, $src_h 
											);
				break;

			case 3:	//	Thumb Width > Image Width
				$x 				=	round( ( $thumb['width'] - $this->_width ) / 2 );
				$newResource 	= 	$this->_createNewResource( $thumb['width'], $this->_height, $this->_color );
				$imageResource 	=	$this->_merge( 
											$newResource, $imageResource, 
											$x, 0, 0, 0, 
											$this->_width, $this->_height, 
											$pct = 100 
										);

				$newResource 	= 	$this->_createNewResource( $thumb['width'], $thumb['height'], $this->_color );
				$imageResource 	=	$this->_resize( 
												$newResource, $imageResource, 
												0, 0, $src_x, $src_y, 
												$thumb['width'], $thumb['height'], $src_w, $src_h 
											);
				break;

			case 2:	//	Image Embeded in Thumb
				$newResource 	= 	$this->_createNewResource( $thumb['width'], $thumb['height'], $this->_color );
				$imageResource 	=	$this->_merge( 
											$newResource, $imageResource, 
											$src_x, $src_y, 0, 0, 
											$this->_width, $this->_height, 
											$pct = 100 
										);
				break;
			default:
				$dst_w		=	$this->_thumbs[$this->_thumb]['width'];
				$dst_h		=	$this->_thumbs[$this->_thumb]['height'];

				list( $dst_w, $dst_h ) = $this->_checkMaxfit( $thumb['process'], $src_w, $src_h, $dst_w, $dst_h );

				$newResource 	= 	$this->_createNewResource( $dst_w, $dst_h, $this->_color );
				$imageResource 	=	$this->_resize( 
												$newResource, $imageResource, 
												0, 0, $src_x, $src_y, 
												$dst_w, $dst_h, $src_w, $src_h 
											);
				break;
		}

		return $imageResource;
	}

	//	_getPositions
	protected function _getPositions()
	{
		$json_path = str_replace( '.'.$this->_extension, '.json', $this->_path.'/'.$this->_value );
		$this->_checkFileJson( $json_path );

		return json_decode( file_get_contents( $json_path ), true );
	}

	//	_getPreview
	protected function _getPreview()
	{
		return $this->_name.'Dropzone';		
	}

	//	_getRGBColor
	protected function _getRGBColor( $hexa )
	{
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

	//	_getSelect
	protected function _getSelect( $thumbs, $k )
	{
		$positions 	=	array_keys( (array)$this->_getPositions() );
		$options 	=	'';
		$attr 		=	'';
		$selected 	=	'';

    	foreach ( $thumbs as $key => $thumb ) {
			
			$label 	=	'';
			if ( $thumb['label'] ) {
				$label	=	( JCck::getConfig_Param( 'language_jtext', 0 ) ) ? Text::_( 'COM_CCK_'.str_replace( ' ', '_', $thumb['label'] ) ) : $thumb['label'];
			}

			$cropped 	=	( in_array( $key, $positions ) ) ? 'cropped' : 'to-crop';

    		if ( $k == $key ) {	

    			if ( count( $thumbs ) == 1 ) {
    				if ( $label ) {
	    				$selected 	.= 	'<div class="btn nohover">'.$label.'</div>';
    				}
    				$attr 	=	' style="display: none;"';
    			}

	    		$selected 	.=	'<a class="btn dropdown-toggle" href="javascript:void(0);" onclick="JCck.More.CropX.openSelect();" data-value="'.$key.'" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"'.$attr.'>
	    							</span><span class="desc '.$cropped.'">'.$label.'</span>
	    						</a>';
    		}
    		$options 	.= 	'<li><a href="javascript:void(0);" onclick="JCck.More.CropX.changeSelect(this);" data-value="'.$key.'">
    							<span class="icon '.$cropped.'"></span><span class="desc '.$cropped.'">'.$label.'</span>
    						</a></li>';
    	}

		return '<div class="btn-group dropdown">'.$selected.'<ul class="dropdown-menu">'.$options.'</ul></div>';
	}

	//	_getThumbParams
	protected function _getThumbParams( $i )
	{
		if ( $this->_options2['thumb'.$i.'_process'] == '0' || (int)$this->_options2['thumb'.$i.'_cropping'] == -1 ) {
			return false;
		}

		$params 			=	array();
		$params['thumb']	=	$i;
		if ( $this->_options2['thumb'.$i.'_process'] == 'quotient' ) {
			if ( (int)$this->_options2['thumb'.$i.'_width'] > 0 ) {
				$coef 	=	$this->_options2['thumb'.$i.'_width'] / $this->_options2['thumb'.$i.'_height'];
			} else {
				$coef 	=	floatval( '0.'.$this->_options2['thumb'.$i.'_height'] );
			}

			$params['width']	=	ceil( $this->_options2['thumb'.$this->_options2['thumb'.$i.'_cropping'].'_width'] * $coef );
			$params['height']	=	ceil( $this->_options2['thumb'.$this->_options2['thumb'.$i.'_cropping'].'_height'] * $coef );
			$params['process']	=	$this->_options2['thumb'.$this->_options2['thumb'.$i.'_cropping'].'_process'];
		} else {
		$params['width']	=	$this->_options2['thumb'.$i.'_width'];
			$params['height']	=	$this->_options2['thumb'.$i.'_height'];
			$params['process']	=	$this->_options2['thumb'.$i.'_process'];
		}

		$params['width']	=	( $params['width'] ) ? $params['width'] : 0; 
		$params['height']	=	( $params['height'] ) ? $params['height'] : 0; 
		$params['cropping']	=	$this->_options2['thumb'.$i.'_cropping'];
		$params['label']	=	$this->_options2['thumb'.$i.'_label'];
		$params['wmk']		=	( isset( $this->_options2['thumb'.$i.'_wmk'] ) ) ? 1 : 0;

		return $params;
	}

	//	_merge
	protected function _merge( $dst_res, $src_res, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct = 100 )
	{
	    // Créer une image temporaire avec la gestion de la transparence
	    $tmp = @imagecreatetruecolor( $src_w, $src_h );

	    // Activer la gestion du canal alpha pour l'image temporaire
	    imagealphablending($tmp, false);
	    imagesavealpha($tmp, true);
	    
	    // Remplir l'image temporaire avec une couleur transparente
	    $transparent = imagecolorallocatealpha($tmp, 0, 0, 0, 127);
	    imagefill($tmp, 0, 0, $transparent);
	    
	    // Copier les parties des deux images
	    @imagecopy( $tmp, $dst_res, 0, 0, $dst_x, $dst_y, $src_w, $src_h );
	    @imagecopy( $tmp, $src_res, 0, 0, $src_x, $src_y, $src_w, $src_h );
	    
	    // Utiliser imagecopy() au lieu de imagecopymerge() pour conserver la transparence
	    @imagecopy($dst_res, $tmp, $dst_x, $dst_y, 0, 0, $src_w, $src_h);
	    
	    // Détruire l'image temporaire pour libérer la mémoire
	    imagedestroy($tmp);

	    return $dst_res;
	}	

	//	_resize
	protected function _resize( $dest_res, $src_res, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h )
	{
	    // Désactiver le mélange des couleurs pour gérer la transparence
	    imagealphablending($dest_res, false);
	    imagesavealpha($dest_res, true);  // Conserver la transparence

	    // Redimensionnement avec transparence respectée
	    @imagecopyresampled( $dest_res, $src_res, (int)$dst_x, (int)$dst_y, (int)$src_x, (int)$src_y, (int)$dst_w, (int)$dst_h, (int)$src_w, (int)$src_h );
	    
	    return $dest_res;
	}	

	//	_setPositions
	protected function _setPositions()
	{
		$json_path = str_replace( '.'.$this->_extension, '.json', $this->_path.'/'.$this->_value );

		$this->_checkFileJson( $json_path );

		$json	=	json_decode( file_get_contents( $json_path ), true );

    // CORRECTION: Stocker comme entiers pour cohérence
    $json[$this->_thumb]['matrix']  =   (float)$this->_matrix; 
    $json[$this->_thumb]['x']       =   (int)round($this->_selection['x1']);    
    $json[$this->_thumb]['y']       =   (int)round($this->_selection['y1']);    
    $json[$this->_thumb]['w']       =   (int)round($this->_selection['width']); 
    $json[$this->_thumb]['h']       =   (int)round($this->_selection['height']);    
    $json[$this->_thumb]['color']   =   $this->_color;

		$json	=	json_encode( $json );

		File::write( $json_path, $json );
	}

	//	_zoomOut
	protected function _zoomOut( $imageResource )
	{
		$dst_w 	= 	$this->_matrix * $this->_width;
		$dst_x	=	( $this->_width - $dst_w ) / 2;
		$dst_h = 	$this->_matrix * $this->_height;
		$dst_y	=	( $this->_height - $dst_h ) / 2;

		//	Create New Resourse
		$newResource 	= 	$this->_createNewResource( $this->_width, $this->_height, $this->_color );

		// Resize original image depending on zoom
		return	$this->_resize( 
							$newResource, $imageResource, 
							$dst_x, $dst_y, 0, 0, 
							$dst_w, $dst_h, $this->_width, $this->_height 
						);
	}
}
