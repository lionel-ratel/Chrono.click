<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: tabs.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// JCckDevTabs
abstract class JCckDevUikitTabs
{
	protected static $positions	=	array(
										0 => 'top',
										1 => 'left',
										2 => 'bottom',
										3 => 'right'
									);
	// end
	public static function end( $params, $navs )
	{
		$html	=	'</li>'
				.	'</ul>';

		if ( $params['position'] ) {
			$html	.=	'</div></div>';
		}

		if ( $params['wrapper'] ) {
			$html		=	'</div>';
		}

		$js		=	'';

		foreach ( $navs[$params['selector']] as $key => $nav ) {
			if ( $params['type'] ) {
				$elem		=	'<li><a href="#">'.$nav.'</a></li>';
			} else {
				$elem		=	'<li><a href="#">'.$nav.'</a></li>';
			}
			
			$js		.=	'$('.json_encode( '#'.$params['selector'].'Tabs' ).').append($('.json_encode( $elem ).'));';
		}

		$js		=	'jQuery(function($){'.$js.'});';

		return $html.'<script type="text/javascript">/*console.log("Before!!");*/'.$js.'/*console.log("After!!");*/</script>';
	}
	
	// open
	public static function open( $params, $css )
	{
		if ( !(int)$params['position'] ) {
			if ( $css !== '' ) {
				$css	=	' '.$css;
			}
		}

		$html	=	'</li>'
				.	'<li class="'.$css.'">';

		return $html;
	}
	
	// start
	public static function start( $params, $start, $css = '' )
	{
		if ( $params['wrapper'] ) {
			$html		=	'<div>';
		} else {
			$html		=	'';
		}
		
		$pos		=	$params['position'];
		$position	=	' class="uk-tab-'.self::$positions[$pos].'" uk-tab="connect: #'.$params['selector'].'; active: '.$start.'"';

		if ( $pos ) {
			$right	=	( $pos == 2 || $pos == 3 ) ? ' uk-flex-last@m' : '';

			if ( $pos == 2 ) {
				$full	=	'1-1';
			} else {
				$full	=	'auto';
				$right	.=	' uk-padding-top-38';
			}
			
			$html	.=	'<div uk-grid><div class="uk-width-'.$full.'@m'.$right.'">';
		} else{
			if ( $css !== '' ) {
				$css	=	' '.$css;
			}
		}

		if ( $params['type'] ) {
			$html	.=	'<ul id="'.$params['selector'].'Tabs"'.$position.'></ul>';
		} else {
			$html	.=	'<ul id="'.$params['selector'].'Tabs"'.$position.'></ul>';	
		}

		if ( $pos ) {
			$html	.=	'</div><div class="uk-width-expand@m">';
		}

		$class	=	'';

		if ( $position === 'top' ) {
			$class	=	' uk-padding';
		}
		
		$html	.=	'<ul id="'.$params['selector'].'" class="uk-switcher'.$class.'">'
    			.	'<li class="'.$css.'">';
        
        return $html;
	}
}
?>