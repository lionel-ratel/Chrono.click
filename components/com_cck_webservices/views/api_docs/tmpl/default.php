<?php
/**
* @version 			SEBLOD WebServices 1.x
* @package			SEBLOD WebServices Add-on for SEBLOD 3.x
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\Registry\Registry;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Uri\Uri;

if ( $this->params->get( 'show_page_heading' ) ) {
	echo '<h1>' . ( ( $this->escape( $this->params->get( 'page_heading' ) ) ) ? $this->escape( $this->params->get( 'page_heading' ) ) : $this->escape( $this->params->get( 'page_title' ) ) ) . '</h1>';
}
if ( $this->show_page_title ) {
	$tag		=	$this->tag_page_title;
	$class		=	trim( $this->class_page_title );
	$class		=	$class ? ' class="'.$class.'"' : '';
	echo '<'.$tag.$class.'><span>' . $this->title . '</span></'.$tag.'>';
}

echo '<div class="o-container o-grid o-rowgap-24">'.HTMLHelper::_( 'content.prepare', '{loadposition api_web_console}' );

HTMLHelper::_( 'bootstrap.framework' );
HTMLHelper::_( 'bootstrap.tooltip' );

JCck::loadjQuery();

$doc	=	Factory::getDocument();

$doc->addStyleSheet( Uri::root( true ).'/media/cck_webservices/css/cck.api.css?v6' );
$doc->addScript( Uri::root( true ).'/media/cck_webservices/js/cck.api.js?v6' );

$js 	=	'
			(function ($){
				$(document).ready(function() {
					var data = {
						"api_url":"'.$this->api_endpoint.'/v'.$this->api_version.'/"
					};
					JCck.WebService.setInstance(data);					
				});
			})(jQuery);
			';
$doc->addScriptDeclaration( $js );

$this->_setAuth();

if ( count( $this->items ) ) {
	PluginHelper::importPlugin( 'cck_field', 'text' );
	PluginHelper::importPlugin( 'cck_field', 'select_simple' );

	$accordion_id	=	'api';
	$inc			=	array();
	$key_params		=	array(
							'DELETE'=>array(
										'default'=>array( '_'=>'COM_CCK_PARAMETERS' )
									  ),
							'GET'=>array(
										'default'=>array( '_'=>'COM_CCK_CUSTOM_FILTERS' ),
										'filtering'=>array(
													   'after'=>array( 'columns'=>'created', 'mode'=>'>', 'type'=>'DATE, DATETIME' ),
													   'before'=>array( 'columns'=>'created', 'mode'=>'<', 'type'=>'DATE, DATETIME' ),
													   'since'=>array( 'columns'=>'created OR modified', 'mode'=>'>=', 'type'=>'DATE, DATETIME' ),
													   'until'=>array( 'columns'=>'created OR modified', 'mode'=>'<=', 'type'=>'DATE, DATETIME' )
													 ),
										'sorting'=>array( 'sort'=>array( 'mode'=>'alpha, newest, oldest' ) ),
										'pagination'=>array( 'limit'=>array( 'mode'=>'=', 'type'=>'INT (max='.JCckWebservice::getConfig_Param( 'resources_limit_max', '10' ).')' ) )
								   ),
							'GET_ID'=>array(
										'default'=>array( '_'=>'COM_CCK_PARAMETERS' )
									  ),
							'PATCH'=>array(
										'default'=>array( '_'=>'COM_CCK_PARAMETERS' )
									),
							'POST'=>array(
										'default'=>array( '_'=>'COM_CCK_PARAMETERS' )
									),
							'PUT'=>array(
										'default'=>array( '_'=>'COM_CCK_PARAMETERS' )
								   )
						);
	echo HTMLHelper::_( 'bootstrap.startAccordion', $accordion_id.'-resources', array( 'active'=>'collapse0', 'parent'=>true ) );

	foreach ( $this->items as $i=>$item ) {
		$idx		=	$item->methods;
		$isGet		=	$item->methods == 'GET';
		$resource	=	'api-'.strtolower( $item->methods ).'-'.$item->name;
		$suffix		=	'';

		if ( !isset( $inc[$item->name][$item->methods] ) ) {
			$inc[$item->name][$item->methods]	=	0;
		}
		if ( $isGet && $inc[$item->name][$item->methods] ) {
			$idx	=	'GET_ID';
			$suffix	.=	'/:id';
			continue;
		}
		if ( $i ) {
			echo HTMLHelper::_( 'bootstrap.endSlide' );
		}

		$body		=	'';
		$heading	=	'<span class="method">'.$item->methods.'</span>'
					.	'<span class="title">'.$item->title.'</span>'
					.	'<span class="uri">/'.$item->name.$suffix.'</span>';
		$heading	=	HTMLHelper::_( 'bootstrap.addSlide', $accordion_id.'-resources', $heading, 'collapse'.$i++ );
		$heading	=	str_replace( 'accordion-group', 'accordion-group api-resource '.$resource.' method-'.strtolower( $item->methods ), $heading );
		
		$actions	=	'<div class="actions o-grid o-col-auto-2 o-align-ic o-align-jic o-align-jse">'					
					.	'<button type="button" class="btn btn-link api-reset o-btn-outlined o-btn-auto o-mr-8" style="display:none;" data-resource="'.$resource.'">'.Text::_( 'COM_CCK_RESET' ).'</a>'
					.	'<button type="button" class="btn api-call o-btn o-btn-solid o-btn-auto" data-resource="'.$resource.'">'.Text::_( 'COM_CCK_TRY_IT' ).'</button>'
					.	'</div>';

		$html		=	'<h2>'.Text::_( 'COM_CCK_REQUEST' ).'</h2>'
					.	'<div class="o-tabs">'
					.	HTMLHelper::_( 'bootstrap.startTabSet', 'request_tabs'.$i, array( 'active'=>'request_t'.$i.'-0' ) )
					.	HTMLHelper::_( 'bootstrap.addTab', 'request_tabs'.$i, 'request_t'.$i.'-0', Text::_( $key_params[$idx]['default']['_'] ) );
		$html		=	str_replace( '</ul>', '</ul>'.$actions, $html );


		$options			=	new Registry( $item->options );
		$input_fields		=	array();
		$input_properties	=	(array)$options->get( 'input' );

		if ( count( $input_properties ) ) {
			$names			=	implode( '","', array_keys( $input_properties ) );
			$input_fields	=	JCckDatabase::loadObjectList( 'SELECT title, name, type, options, options2, storage, storage_table, storage_field, storage_field2, storage_mode'
															. ' FROM #__cck_core_fields'
															. ' WHERE name IN ("'.$names.'")'
															, 'name' );
		}

		$legacy_filters	=	array();

		foreach( $input_properties as $k=>$v ) {
			$value	=	$input_fields[$k]->storage_field;

			if ( $v->property ) {
				$value	.=	'='.$v->property;
			}

			$legacy_filters[]	=	$value;
		}

		$options	=	array();
		$pages		=	array( 'first', 'previous', 'next', 'last' );

		if ( isset( $legacy_filters ) && count( $legacy_filters ) ) {
			$filters		=	array();
			$filters_html	=	'';

			if ( $idx == 'GET' ) {
				$legacy_filters	=	array_diff( $legacy_filters, array( '' ) );
				$legacy_filters	=	array_flip( $legacy_filters );

				if ( count( $legacy_filters ) ) {
					foreach ( $legacy_filters as $k=>$v ) {
						$v	=	$k;

						if ( strpos( $k,  '=' ) !== false ) {
							$parts	=	explode( '=', $k );

							if ( $parts[0] != '' && $parts[1] != '' ) {
								$k	=	$parts[1];
								$v	=	$parts[0];
							} else {
								continue;
							}
						}

						if ( !isset( $filters[$k] ) ) {
							$filters[$k]	=	array();
						}
						$filters[$k][]		=	$v;
					}

					foreach ( $filters as $filter=>$columns ) {
						if ( $filter != '' ) {
							if ( count( $columns ) > 1 ) {
								$title	=	' title="'.implode( ', ', $columns ).'" data-placement="right"';
							} else {
								$title	=	' title="'.$columns[0].'" data-placement="right"';
							}
							$filters_html	.=	'<tr>'
											.	'<td><span class="hasTooltip"'.$title.'>'.$filter.'</span></td>'
											.	'<td width="40%"><div class="o-input">'.JCckDevField::getForm( 'core_dev_text', '', $config, array( 'id'=>$item->name.'-'.$filter, 'name'=>$filter, 'attributes'=>'placeholder="=" '.$resource.'="filter"' ) ).'</div></td>'
											.	'<td width="30%">STRING <span class="icon-info hasTooltip" title="'.Text::_( 'COM_CCK_COMMA_SEPARATED_VALUES' ).'"></span></td>'
											.	'</tr>';
						}
					}
				}

				if ( count( $filters ) ) {
					if ( JCckWebservice::getConfig_Param( 'resources_filtering', '0' ) ) {
						$html	.=	'<table class="'.( JCck::is( '4.0' ) ? 'o-table table' : 'table table-striped' ).'">'
								.	'<thead>'
								.	'<tr>'
								.	'<th>'.Text::_( 'COM_CCK_PARAMETER' ).'</th>'
								.	'<th width="40%">'.Text::_( 'COM_CCK_VALUE' ).'</th>'
								.	'<th width="30%">'.Text::_( 'COM_CCK_TYPE' ).'</th>'
								.	'</tr>'
								.	'</thead>'
								.	'<tbody>'
								.	$filters_html
								.	'</tbody></table>'
								;
					} else {
						$html	.=	'<em>'.Text::_( 'COM_CCK_CUSTOM_FILTERS_DISABLED' ).'</em>';
					}
				} else {
					$html	.=	'<em>'.Text::_( 'COM_CCK_NO_CUSTOM_FILTER_FOUND' ).'</em>';
				}
			}

			if ( $idx == 'GET' ) {
				$html	.=	HTMLHelper::_( 'bootstrap.endTab' )
						.	HTMLHelper::_( 'bootstrap.addTab', 'request_tabs'.$i, 'request_t'.$i.'-1', Text::_( 'COM_CCK_KEY_FILTERS' ) );

				$html	.=	$this->_getHtmlTableSection( 'table' )
						.	$this->_getHtmlTableSection( 'head' )
						.	$this->_getHtmlTableSection( 'body' );
				
				foreach ( $key_params[$idx]['filtering'] as $filter=>$info ) {
					if ( $filter != '' ) {
						$title	=	'';

						if ( isset( $info['columns'] ) && $info['columns'] ) {
							$title	=	' title="'.$info['columns'].'" data-placement="right"';
						}
						
						$html	.=	'<tr>'
								.	'<td><span class="hasTooltip"'.$title.'>'.$filter.'</span></td>'
								.	'<td width="40%"><div class="o-input">'.JCckDevField::getForm( 'core_dev_text', '', $config, array( 'id'=>$item->name.'-'.$filter, 'name'=>$filter, 'attributes'=>'placeholder="'.$info['mode'].'" '.$resource.'="'.$filter.'"' ) ).'</div></td>'
								.	'<td width="30%">'.$info['type'].'</td>'
								.	'</tr>';
					}
				}
				
				$html	.=	$this->_getHtmlTableSection( 'end' );

				$html	.=	HTMLHelper::_( 'bootstrap.endTab' )
						.	HTMLHelper::_( 'bootstrap.addTab', 'request_tabs'.$i, 'request_t'.$i.'-2', Text::_( 'COM_CCK_PAGINATION' ) );

				$html	.=	$this->_getHtmlTableSection( 'table' )
						.	$this->_getHtmlTableSection( 'head' )
						.	$this->_getHtmlTableSection( 'body' );

				foreach ( $key_params[$idx]['pagination'] as $filter=>$info ) {
					if ( $filter != '' ) {
						$title	=	'';

						if ( isset( $info['columns'] ) && $info['columns'] ) {
							$title	=	' title="'.$info['columns'].'" data-placement="right"';
						}
						
						$html	.=	'<tr>'
								.	'<td><span class="hasTooltip"'.$title.'>'.$filter.'</span></td>'
								.	'<td width="40%"><div class="o-input">'.JCckDevField::getForm( 'core_dev_text', '', $config, array( 'id'=>$item->name.'-'.$filter, 'name'=>$filter, 'attributes'=>'placeholder="'.$info['mode'].'" '.$resource.'="'.$filter.'"' ) ).'</div></td>'
								.	'<td width="30%">'.$info['type'].'</td>'
								.	'</tr>';
					}
				}

				$html	.=	$this->_getHtmlTableSection( 'end' );
				$html	.=	'<br>';
				$html	.=	$this->_getHtmlTableSection( 'table' )
						.	'<thead>'
						.	'<tr>'
						.	'<th>'.Text::_( 'COM_CCK_PAGE' ).'</th>'
						.	'<th width="70%"></th>'
						.	'</tr>'
						.	'</thead>'
						.	$this->_getHtmlTableSection( 'body' );

				foreach ( $pages as $page )	{
					$html	.=	'<tr>'
							.	'<td><span class="hasTooltip">'.$page.'</span></td>'
							.	'<td width="70%"><div class="actions"><button class="btn api-go o-btn-0 o-btn-auto" data-page="'.$page.'" data-resource="'.$resource.'" data-url="" disabled="disabled">Go</button></div></td>'
							.	'</tr>';
				}

				$html	.=	$this->_getHtmlTableSection( 'end' );						

				$html	.=	HTMLHelper::_( 'bootstrap.endTab' )
						.	HTMLHelper::_( 'bootstrap.addTab', 'request_tabs'.$i, 'request_t'.$i.'-3', Text::_( 'COM_CCK_SORTING' ) );

				$html	.=	$this->_getHtmlTableSection( 'table' )
						.	$this->_getHtmlTableSection( 'head' )
						.	$this->_getHtmlTableSection( 'body' );

				$html	.=	'<tr>'
						.	'<td><span class="hasTooltip"'.$title.'>sort</span></td>'
						.	'<td width="40%"><div class="o-input">'.JCckDevField::getForm( 'core_dev_select', '', $config, array( 'id'=>$item->name.'-'.'sort', 'name'=>'sort', 'selectlabel'=>'Inherited', 'options'=>'Alphabetical=alpha||Newest=newest||Oldest=oldest', 'attributes'=>$resource.'="sort"' ) ).'</div></td>'
						.	'<td width="30%">STRING</td>'
						.	'</tr>';

				$html	.=	$this->_getHtmlTableSection( 'end' );
			}

			$html	.=	HTMLHelper::_( 'bootstrap.endTab' )
					.	HTMLHelper::_( 'bootstrap.endTabSet' )
					.	'</div>'
					;

			$html	=	str_replace( 'class="nav nav-tabs"', 'class="nav nav-tabs cck-tabs o-colspan-5"', $html );
			$html	=	str_replace( 'class="tab-content"', 'class="tab-content o-colspan-6"', $html );

			$body	.=	$html
					.	'<div class="call o-mt-18" style="display:none;">'
					.	'<div class="request">'
					.	'<h3>'.Text::_( 'COM_CCK_REQUEST_URL' ).'</h3>'
					.	'<pre></pre>'
					.	'</div>'
					.	'<div class="response">'
					.	'<h2>'.Text::_( 'COM_CCK_RESPONSE' ).'</h2>'
					.	'<pre></pre>'
					.	'</div>'
					.	'</div>'
					;
		}

		$inc[$item->name][$item->methods]++;

		echo $heading.$body;
	}

	echo HTMLHelper::_( 'bootstrap.endSlide' );
    echo HTMLHelper::_( 'bootstrap.endAccordion' );
}

echo '</div>';
?>