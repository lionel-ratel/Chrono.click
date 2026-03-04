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

use Joomla\Filesystem\File;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\HTML\Helpers\Bootstrap;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Uri\Uri;

// Plugin
class plgSystemCCK_Toolbox extends CMSPlugin
{
	protected $base			=	'';
	protected $browser		=	'';
	protected $options		=	null;
	protected $optimize		=	false;
	protected $optimized	=	false;
	protected $path			=	'';
	
	// __construct
	public function __construct( &$subject, $config )
	{
		parent::__construct( $subject, $config );

		/* TODO#SEBLOD: use JBrowser */
		if ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
			if ( strpos( $_SERVER['HTTP_USER_AGENT'], 'MSIE 8.0' ) !== false ) {
				$this->browser	=	'ie8';
			} elseif ( strpos( $_SERVER['HTTP_USER_AGENT'], 'MSIE 7.0' ) !== false ) {
				$this->browser	=	'ie7';
			}
		}
		
		$this->options	=	ComponentHelper::getParams( 'com_cck_toolbox' );
		$this->base		=	Uri::root( true );
		$this->path		=	$this->base.'/media/cck_toolbox';
	}
	
	// onAfterDispatch
	public function onAfterDispatch()
	{
		$app	=	Factory::getApplication();
		$doc	=	Factory::getDocument();
		$lang	=	Factory::getLanguage();
		$option	=	$app->input->get( 'option', '' );
		$user	=	Factory::getUser();

		if ( $app->isClient( 'administrator' ) ) {
			if ( $this->options->get( 'admin_url', 0 ) ) {
				
				$var		=	$this->options->get( 'admin_url_var', 'protected' );
				$var_match	=	$this->options->get( 'admin_url_var_match', '' );
				if ( !$user->id && $user->guest ) {
					if ( $var_match != '' ) {
						if ( $app->input->get( $var ) != $var_match ) {
							Factory::getApplication()->redirect( Uri::root() );
						}
					} elseif ( Uri::getInstance()->getQuery() != $var ) {
						Factory::getApplication()->redirect( Uri::root() );
					}
				}
			}
		}

		// INTERNET EXPLORER
		if ( $this->browser == 'ie7' || $this->browser == 'ie8' ) {
			// Css
			if ( $this->options->get( 'cck_core_css', '0' ) == '1' ) {
				$doc->addStyleSheet( $this->path.'/css/cck.'.( ( $app->isClient( 'site' ) ) ? 'site' : 'admin' ).'-ie.css' );
				$doc->addStyleSheet( $this->path.'/css/cck.responsive-ie.css' );
			}

			// Js
			if ( $this->options->get( 'html5_shiv' ) == '1' ) {
				$doc->addScript( $this->path.'/js/html5shiv.min.js' );
			}
			if ( $this->options->get( 'css3_mediaqueries' ) == '1' ) {
				$doc->addScript( $this->path.'/js/css3-mediaqueries.js' );
			}
			if ( $this->options->get( 'excanvas' ) == '1' ) {
				$doc->addScript( $this->path.'/js/excanvas.js' );
			}
			if ( $this->options->get( 'html5_placeholder' ) == '1' ) {
				$doc->addScript( $this->path.'/js/jquery.placeholder.min.js' );
				$selector	=	$this->options->get( 'html5_placeholder_selector', 'input' );
				$js			=	'jQuery(document).ready(function($){ $("'.$selector.'").placeholder(); });';
				$doc->addScriptDeclaration( $js );
			}
			if ( $this->options->get( 'svgeezy' ) == '1' ) {
				$doc->addScript( $this->path.'/js/svgeezy.min.js' );
				$js			=	'jQuery(document).ready(function($){ svgeezy.init(false, "png"); });';
				$doc->addScriptDeclaration( $js );
			}
		}

		// SCRIPTS & STYLES
		if ( $app->isClient( 'site' ) ) {
			$optimize	=	(string)$this->options->get( 'optimize', '0' );

			if ( $optimize != '0' ) {
				$excluded			=	array(
											'com_acym'=>true,
											'com_acymailing'=>true
										);
				$excluded_ids		=	$this->options->get( 'optimize_bypass', '' );
				$groups				=	$user->getAuthorisedGroups();
				$optimize_groups	=	$this->options->get( 'optimize_groups', array() );

				if ( $option && !isset( $excluded[$option] ) ) {
					if ( !empty( $optimize_groups ) ) {
						if ( !empty( array_intersect( $optimize_groups, $groups ) ) ) {
							$this->optimize	=	$optimize;
						}
					}
				}

				if ( $excluded_ids != '' ) {
					$excluded_ids	=	explode( ',', $excluded_ids );

					if ( count( $excluded_ids ) ) {
						$excluded_ids	=	array_flip( $excluded_ids );
						$itemId			=	(int)$app->input->getInt( 'Itemid' );

						if ( isset( $excluded_ids[$itemId] ) ) {
							$this->optimize	=	false;
						}
					}
				}
			}

			// Bootstrap
			if ( $this->options->get( 'bootstrap_css', 0 ) == 1 ) {
				Bootstrap::loadCss( true, $doc->direction );
				$doc->addStyleDeclaration( '[class^="icon-"], [class*=" icon-"] {background-image: none;}' );
			} elseif ( $this->options->get( 'bootstrap_css', 0 ) == 2 ) {
				HTMLHelper::_('stylesheet', 'cck_toolbox/bootstrap.css', array(), true);
			}
			if ( $this->options->get( 'bootstrap_tooltip', 0 ) == 1 ) {
				$selector	=	$this->options->get( 'bootstrap_tooltip_selector', '.hasTooltip' );
				$params		=	$this->options->get( 'bootstrap_tooltip_params', '' );
				if ( $params != '' ) {
					$params	=	json_decode( $params, true );
				} else {
					$params	=	array();
				}
				HTMLHelper::_( 'bootstrap.tooltip', $selector, $params );
			}

			// Bootstrap Filestyle
			if ( $this->options->get( 'bootstrap_filestyle', 0 ) == 1 ) {
				JCck::loadjQuery();
				$doc->addScript( $this->base.'/media/cck_toolbox/js/jquery.bootstrap-filestyle.min.js' );
				$selector	=	$this->options->get( 'bootstrap_filestyle_selector', ':file' );
				$js			=	'$(".upload-wrapper").on("change", "input:file", function() { $(this).parent().find(".cck_preview").hide(); });';
				$js			=	'jQuery(document).ready(function($){ $("'.$selector.'").filestyle({buttonText:"'.Text::_( 'COM_CCK_FILEUPLOAD_TEXT' ).'",iconName:"'.$this->options->get( 'bootstrap_filestyle_icon', '' ).'"}); '.$js.' });';
				$doc->addScriptDeclaration( $js );
			}
			
			$accessLevels	=	$user->getAuthorisedViewLevels();
			$isRegistered	=	$user->id && !$user->guest;
			$isManager		=	$isRegistered && in_array( 3, $accessLevels );

			// Bootstrap Select
			if ( $this->options->get( 'boostrap_select', 0 ) == 1 || ( $this->options->get( 'boostrap_select', 0 ) == 2 && $isRegistered ) ) {
				JCck::loadjQuery();
				
				$v			=	$this->options->get( 'boostrap_select_version', '0' );

				if ( strpos( $v, '.' ) !== false ) {
					$version	=	$v;
				} else {
					$versions	=	array(
										'0'=>'1.12.4',
										'1'=>'1.13.18'
									);
					$version	=	$versions[$v];
				}
				
				$doc->addStyleSheet( $this->base.'/media/cck_toolbox/css/jquery.bootstrapselect-'.$version.'.min.css' );
				$doc->addScript( $this->base.'/media/cck_toolbox/js/jquery.bootstrapselect-'.$version.'.min.js' );
				
				$path	=	'/media/cck_toolbox/js/bootstrapselect/defaults-'.str_replace( '-', '_', $lang->getTag() ) .'.js';
				
				if ( is_file( JPATH_SITE.$path ) ) {
					$doc->addScript( $this->base.$path );
				}

				$selector	=	$this->options->get( 'bootstrap_select_selector', 'select' );
				$js			=	'$(".dropdown-menu.noclose").on("click", function (e) { e.stopPropagation(); });';

				if ( JCck::is( '4.0' ) ) {
					$js		.=	'$(".bs-actionsbox").find(".btn-group").addClass("o-grid o-colauto-2 o-between").removeClass("btn-group btn-group-sm btn-block");'
							.	'$(".bs-select-all").removeClass("btn btn-default").addClass("o-btn-solid o-btn-auto o-btn-small");'
							.	'$(".bs-deselect-all").removeClass("btn btn-default").addClass("o-btn-solid o-btn-auto o-btn-small");';
				}

				$js			=	'jQuery(document).ready(function($){ $("'.$selector.'").selectpicker(); '.$js.' });';
				$doc->addScriptDeclaration( $js );
			}
			
			// Chosen
			if ( $this->options->get( 'chosen', 0 ) == 1 || ( $this->options->get( 'chosen', 0 ) == 3 && $isManager ) ) {
				$selector	=	$this->options->get( 'chosen_selector', 'select' );
				HTMLHelper::_( 'formbehavior.chosen', $selector );
			}

			// Select2
			if ( $this->options->get( 'select2', 0 ) == 1 ) {
				JCck::loadjQuery();
				$doc->addStyleSheet( $this->base.'/media/cck_toolbox/css/jquery.select2.min.css' );
				$doc->addScript( $this->base.'/media/cck_toolbox/js/jquery.select2.min.js' );
				$selector	=	$this->options->get( 'select2_selector', 'select' );
				$js 		=	'$.each($(".select2-container"), function (i, n) {'
            				.		'$(n).next().show().fadeTo(0, 0).height("0px").css({"left":0,"position":"absolute","top":0});'
            				.		'$(n).prepend($(n).next());'
            				.		'$(n).delay(500).queue(function () {'
                			.			'$(this).removeClass("validate[required]");'
                			.			'$(this).dequeue();'
            				.		'});'
        					.	'});';
				$js			=	'jQuery(document).ready(function($){ $("'.$selector.'").select2();'.$js.'});';
				$doc->addScriptDeclaration( $js );
			}

			// Fonts
			$icomoon	=	(int)$this->options->get( 'icomoon', 0 );
			
			if ( $icomoon == 1 ) {
				$doc->addStyleSheet( $this->base.'/media/jui/css/icomoon.css' );
				$doc->addStyleDeclaration( '[class^="icon-"], [class*=" icon-"] {background-image: none;}' );
			} elseif ( $icomoon == 2 ) {
				$doc->addStyleSheet( $this->base.'/media/cck_toolbox/css/icomoon.css' );
			} elseif ( $icomoon == -1 ) {
				$custom_path	=	$this->options->get( 'icomoon_custom', '' );
				
				if ( $custom_path && is_file( JPATH_SITE.'/'.$custom_path ) ) {
					$doc->addStyleSheet( $this->base.'/'.$custom_path );
				}	
			}

			// Lazy Sizes
			if ( $this->options->get( 'lazy_sizes', 0 ) == 1 ) {
				$doc->addScript( $this->base.'/media/cck_toolbox/js/lazysizes.min.js' );
			}

			if ( $doc->getType() == 'html' ) {
				// Optimize
				if ( $this->optimize === 'onAfterDispatch' ) {
					$css	=	$this->options->get( 'css_combine', 0 );
					$js		=	$this->options->get( 'js_combine', 0 );

					if ( $css || $js ) {
						$head	=	$doc->getHeadData();
						$this->_optimize( $css, $js, $head );
					}

					$this->_optimize_inline( $head );
				}
			}
		}
	}
	
	// onBeforeRender
	public function onBeforeRender()
	{
		$app	=	Factory::getApplication();
		$doc	=	Factory::getDocument();

		if ( !$app->isClient( 'site' ) ) {
			return;
		}
		
		if ( $doc->getType() == 'html' ) {
			$head	=	$doc->getHeadData();
			
			// Complete
			JCckToolbox::setHead( $head );
			
			// Optimize
			if ( $this->optimize === 'onBeforeRender' ) {
				$css	=	$this->options->get( 'css_combine', 0 );
				$js		=	$this->options->get( 'js_combine', 0 );

				if ( $css || $js ) {
					$this->_optimize( $css, $js, $head );
				}
				
				$this->_optimize_inline( $head );
			}
		}
	}
	
	// onAfterRender
	public function onAfterRender()
	{
		$app	=	Factory::getApplication();
		$doc	=	Factory::getDocument();

		if ( !$app->isClient( 'site' ) ) {
			return;
		}
		
		if ( $doc->getType() == 'html' ) {
			// Optimize
			if ( $this->optimize == 'onBeforeRender' && $this->optimized !== false ) {
				$body	=	$app->getBody();
				$search	=	array(
								'css'=>array( 0=>'<link href=', 1=>' (.*)/>' ),
								'js'=>array( 0=>'<script src=', 1=>'(.*)>' )
							);
				$total	=	0;

				foreach ( $this->optimized as $k=>$optimized_files ) {
					foreach ( $optimized_files as $optimized_file ) {
						$count	=	0;
						$body	=	preg_replace( '#\n\t'.$search[$k][0].'"'.$optimized_file.'([^"]*)"'.$search[$k][1].'#', '', $body, -1, $count );		

						if ( $count ) {
							$total	+=	$count;
						}
					}
				}

				if ( $total ) {
					$app->setBody( $body );
				}
			} elseif ( $this->optimize !== false ) {
				if ( $this->optimize === 'onAfterRender' ) {
					$css	=	$this->options->get( 'css_combine', 0 );
					$js		=	$this->options->get( 'js_combine', 0 );

					if ( $css || $js ) {
						$body	=	$app->getBody();
						
						if ( $css ) {
							$body	=	$this->_parse( 'css', $body, '#<link rel="stylesheet" href="([^"]*)" (.*)/>#', '<link rel="stylesheet" href="##" type="text/css" />' );
						}
						if ( $js ) {
							$body	=	$this->_parse( 'js', $body, '#<script src="([^"]*)" ?(.*)?></script>#', '<script src="##" type="text/javascript"></script>' );
						}

						$app->setBody( $body );
					}
				}
				if ( $this->options->get( 'html_minify', 0 ) ) {
					$body		=	str_replace( array( "\r\n", "\r", "\n", "\t" ), '', $app->getBody() );
					if ( $this->options->get( 'html_minify', 0 ) == 2 ) {
						$body	=	str_replace( array( '  ', '    ', '    ' ), ' ', $body );
					}
					if ( $this->options->get( 'html_return_tags', '' ) ) {
						$page	=	explode( '</head>', $body );
						$tags	=	strtr( $this->options->get( 'html_return_tags', '' ), array( "\r\n"=>'<br />', "\r"=>'<br />', "\n"=>'<br />' ) );
						$tags	=	explode( '<br />', $tags );

						if ( count( $tags ) ) {
							$search		=	array();
							$replace	=	array();

							foreach ( $tags as $t ) {
								if ( $t != '' ) {
									$search[]	=	$t.'="';
									$replace[]	=	"\n".$t.'="';
								}
							}
							if ( count( $search ) ) {
								$page[1]	=	str_replace( $search, $replace, $page[1] );
							}
						}
						$body	=	$page[0].'</head>'.$page[1];
					}
					$app->setBody( $body );
				}
			}
		}
	}

	// _combine
	protected function _combine( $ext, &$files, $options = array() )
	{
		$base			=	Uri::base();
		$base_len		=	strlen( $base );
		$file			=	'';
		$files2			=	array();
		$media_version	=	$this->options->get( 'optimize_query_version', '' );
		$root			=	JPATH_SITE;
		
		$exclusions	=	array();

		if ( $ext == 'js' && $options['js_exclusions'] != '' ) {
			$exclusions	=	strtr( $options['js_exclusions'], array( "\r\n"=>'<br />', "\r"=>'<br />', "\n"=>'<br />' ) );
			$exclusions	=	explode( '<br />', $exclusions );	
		}

		if ( count( $files ) ) {
			foreach ( $files as $k=>$v ) {
				if ( strpos( $k, '/cck.validation.' ) !== false ) {
					if ( $this->optimized === false ) {
						$this->optimized	=	array(
													'css'=>array(),
													'js'=>array()
												);
					}
					$this->optimized[$ext][]	=	$k;
				}

				$pos	=	strpos( $k, '//' );

				if ( $pos !== false && $pos == 0 ) {
					$files2[$k]	=	$v;
					continue;
				} elseif ( strpos( $k, 'http' ) !== false ) {
					$pos	=	strpos( $k, $base );

					if ( $pos !== false && $pos == 0 ) {
						$k	=	substr( $k, $base_len );

						if ( $k[0] != '/' ) {
							$k	=	'/'.$k;
						}
					} else {
						$files2[$k]	=	$v;
						continue;	
					}
				} elseif ( count( $exclusions ) ) { // Exclusions
					foreach ( $exclusions as $exclusion ) {
						if ( strpos( $k, $exclusion ) !== false ) {
							unset( $files[$k] );
							$files2[$k]	=	$v;
							continue;		
						}
					}
				}
				
				// Combine
				$file	.=	md5( $k );
			}
		}

		if ( $media_version ) {
			$file	.=	'?'.Factory::getApplication()->input->get( $media_version, '' );
		}

		$file	=	'/cache/com_cck_toolbox/'.md5( $file ).'.'.$ext;

		if ( $file && !is_file( $root.$file ) ) {
			$buffer	=	'';
			$glue	=	( $ext == 'js' ) ? ';' : '';
			$header	=	'';

			// Read
			foreach ( $files as $k=>$v ) {
				$pos	=	strpos( $k, '//' );

				if ( $pos !== false && $pos == 0 ) {
					continue;
				} elseif ( strpos( $k, 'http' ) !== false ) {
					$pos	=	strpos( $k, $base );
					
					if ( $pos !== false && $pos == 0 ) {
						$k	=	substr( $k, $base_len );

						if ( $k[0] != '/' ) {
							$k	=	'/'.$k;
						}
					} else {
						continue;
					}
				}

				// Combine
				if ( is_file( $root.$k ) ) {
					$buf	=	file_get_contents( $root.$k );

					if ( $ext == 'css' ) {
						if ( strpos( $buf, '../' ) !== false ) {
							$parts	=	explode( '/', $k );
							$n		=	count( $parts ) - 2;
							if ( $n ) {
								for ( $n; $n != 0; $n-- ) {
									$search		=	str_repeat( '../', $n );
									if ( strpos( $buf, $search ) ) {
										$buf	=	str_replace( $search, $this->_substrrpos( $k, '/', $n ), $buf );
									}
								}
							}
						} elseif ( strpos( $buf, './' ) !== false ) {
							$path	=	'';
							$parts	=	explode( '/', $k );
							$n		=	count( $parts ) - 1;
							if ( $n ) {
								for ( $i = 0; $i < $n; $i++ ) {
									if ( isset( $parts[$i] ) && $parts[$i] != '' ) {
										$path	.=	'/'.$parts[$i];
									}
								}
								$buf	=	str_replace( './', $path.'/', $buf );
							}
						}
					}
					$buffer	.=	$buf.$glue;
				}
			}
			if ( $ext == 'css' ) {
				$matches	=	array();

				preg_match_all( '#\@import(.*);#', $buffer, $matches );

				if ( count( $matches[1] ) ) {
					$pre	=	'';
					$search	=	array();

					foreach ( $matches[1] as $match ) {
						$pre	.=	'@import'.$match.";\n";
					}
					$buffer	=	str_replace( $matches[1], '', $buffer );
					$buffer	=	$pre.$buffer;
				}
			}

			// Minify
			if ( $options['minify'] ) {
				$buffer	=	preg_replace( '#\s+#', ' ', $buffer );
				$buffer	=	preg_replace( '#/\*.*?\*/#s', '', $buffer );
				$buffer	=	str_replace( array( "\r\n", "\r", "\n", "\t", '  ', '    ', '    ' ), '', $buffer );
			}
			
			// Write
			$buffer	=	$header.$buffer;
			File::write( $root.$file, $buffer );
		}

		if ( $options['alter'] !== false ) {
			$files	=	$files2;
		}

		return $file;
	}

	// _optimize
	protected function _optimize( $css, $js, $head )
	{
		$doc	=	Factory::getDocument();

		if ( $css ) {
			$file		=	$this->_combine( 'css', $head['styleSheets'], array( 'alter'=>true, 'js_exclusions'=>'', 'minify'=>$this->options->get( 'css_minify', 0 ) ) );
			$files		=	array_merge( array( $file=>array( 'mime'=>'text/css' ) ), $head['styleSheets'] );

			$doc->setHeadData( array( 'styleSheets'=>$files ) );
		}
		if ( $js ) {
			$file		=	$this->_combine( 'js', $head['scripts'], array( 'alter'=>true, 'js_exclusions'=>$this->options->get( 'js_exclusions', '' ), 'minify'=>false ) );

			$async		=	false;
			$defer		=	false;

			if ( $exec = $this->options->get( 'js_execution', '' ) ) {
				${$exec}	=	true;
			}
			
			$files		=	array_merge( array( $file=>array( 'mime'=>'text/javascript', 'defer'=>$defer, 'async'=>$async ) ), $head['scripts'] );

			$doc->setHeadData( array( 'scripts'=>$files ) );
		}
	}

	// _optimize_inline
	protected function _optimize_inline( $head )
	{
		$doc	=	Factory::getDocument();
		$inline	=	array();

		if ( $this->options->get( 'css_minify_raw', 0 ) ) {
			$head['style']['text/css']			=	str_replace( array( "\r\n", "\r", "\n", "\t", '  ', '    ', '    ' ), '', $head['style']['text/css'] );
			$inline['style']					=	$head['style'];
		}
		if ( $this->options->get( 'js_minify_raw', 0 ) ) {
			$head['script']['text/javascript']	=	str_replace( array( "\r\n", "\r", "\n", "\t", '  ', '    ', '    ' ), '', $head['script']['text/javascript'] );
			$inline['script']					=	$head['script'];
		}

		if ( count( $inline ) ) {
			$doc->setHeadData( $inline );
		}
	}

	// _parse
	protected function _parse( $ext, $body, $regex, $replace )
	{
		$files	=	array();
		preg_match_all( $regex, $body, $matches );
		
		if ( count( $matches[1] ) ) {
			foreach ( $matches[1] as $k=>$v ) {
				$files[$v]	=	array();
				if ( $k != 0 ) {
					$body	=	str_replace( $matches[0][$k]."\n", '', $body );
				}
			}
		}

		if ( count( $files ) ) {
			$file		=	$this->_combine( $ext, $files, array( 'alter'=>false, 'js_exclusions'=>$this->options->get( 'js_exclusions', '' ), 'minify'=>$this->options->get( 'css_minify', 0 ) ) );
			$replace	=	str_replace( '##', $file, $replace );
			// $body	=	str_replace( $matches[0][0], "\n", $body );
			// $body	=	str_replace( '</body>', $replace."\n".'</body>', $body );
			$body		=	str_replace( $matches[0][0], $replace."\n", $body );
		}

		return $body;
	}

	// _substrrpos
	protected function _substrrpos( $string, $needle, $i )
	{
		for ( $i; $i >= 0; $i-- ) {
			$pos	=	strrpos( $string, $needle );
			$string	=	substr( $string, 0, $pos );
		}

		return $string.$needle;
	}
}
?>