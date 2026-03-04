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

use Joomla\CMS\Factory;

require_once JPATH_ADMINISTRATOR.'/components/'.CCK_COM.'/helpers/common/display.php';

// Helper
class Helper_Display extends CommonHelper_Display
{
	public static function renderChart( $items, $params )
	{
		if ( !count( $items ) ) {
			return;
		}

		$doc	=	Factory::getDocument();
		$datas	=	'';
		$js		=	'';
		$params['label']	=	isset( $params['label'] ) ? $params['label'] : 'text';
		$params['label2']	=	isset( $params['label2'] ) ? $params['label2'] : 'num';
		$params['label3']	=	isset( $params['label3'] ) ? $params['label3'] : '';
		$params['label4']	=	isset( $params['label4'] ) ? $params['label4'] : '';
		$params['options']	=	isset( $params['options'] ) ? $params['options'] : '';
		$params['title']	=	isset( $params['title'] ) ? $params['title'] : '';

		foreach( $items as $item ) {
			$datas	.=	'["'.htmlspecialchars( $item->text ).'", '.$item->num;
			if ( $params['label3'] ) {
				$datas	.=	', '.$item->num2;
			}
			if ( $params['label4'] ) {
				$datas	.=	', '.$item->num3;
			}
			$datas	.=	'],';
		}
		$columns	=	'data.addColumn("string", "'.$params['label'].'"); data.addColumn("number", "'.$params['label2'].'");';
		$colors		=	'';

		if ( $params['label3'] ) {
			$colors		=	',colors: ["#3366CC", "#DC3912", "#109618", "#FF9900", "#990099", "#3B3EAC", "#0099C6", "#DD4477"]';
			$columns	.=	' data.addColumn("number", "'.$params['label3'].'");';
		}
		if ( $params['label4'] ) {
			$columns	.=	' data.addColumn("number", "'.$params['label4'].'");';
		}

		$datas		=	'data.addRows(['.substr( $datas, 0, -1 ).']);';

		$js		.=	'
					function drawChart_'.$params['id'].'() {			
						var options = {
							title:"'.$params['title'].'",
							width:'.str_replace( 'px', '', $params['width'] ).',
							height:'.str_replace( 'px', '', $params['height'] ).',
							legend: {position: "'.$params['legend'].'"}'.$params['options'].',
							isStacked: true'.$colors.'
						};
			        	var data = new google.visualization.DataTable();
						'.$columns.$datas.'
						var chart = new google.visualization.'.$params['type'].'(document.getElementById("'.$params['id'].'"));
						chart.draw(data, options);
					}
					google.load("visualization", "1.0", {"packages":["corechart"]}); google.setOnLoadCallback(drawChart_'.$params['id'].');
					';

		
		$doc->addScript( 'https://www.google.com/jsapi' );
		$doc->addScriptDeclaration( $js );
		$doc->addStyleDeclaration( '#'.$params['id'].' > div, #'.$params['id'].' > div > div {margin:0 auto; margin-left:-21px;}' );
		
		return '<div id="'.$params['id'].'" style="width:'.$params['width'].'; height:'.$params['height'].';"></div>';
	}
}
?>