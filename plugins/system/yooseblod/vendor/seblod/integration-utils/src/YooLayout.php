<?php
namespace YooSeblod\Integration;

defined( '_JEXEC' ) or die;

use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use YOOtheme\Application;
use YOOtheme\View;

require_once JPATH_SITE.'/project/helper.php';

class YooLayout {
	public		$cck 		= 	null;
	public		$layout 	= 	null;

	protected	$_id 		= 	null;
	protected	$_stubs		=	__DIR__ . '/stubs';

	// __construct
	function __construct( $layout, $cck = null ) 
	{
		if ( !is_null( $cck ) ) {
			if ( is_object( $cck ) ) {
				$this->cck	=	$cck;
				$this->_id 	=	'cck_yoo_'.$this->cck->id;
			} elseif ( is_string( $cck ) ) {
				$this->cck	=	null;
				$this->_id 	=	'cck_yoo_'.$cck;
			}
		}

		$this->layout 	=	$this->_clean( $layout );
	}

	// display
	public function display( $transformNode = false ) 
	{
		$layout	=	$this->layout;

		if ( $transformNode ) {
			$layout	=	$this->_transformNode( $layout );
		}

		// Use Yootheme View
		$app	=	\YOOtheme\Application::getInstance();
		$view 	=	$app(View::class);

		return $view->builder( $layout, ['prefix' => $this->_id ] );
	}

	// getElement
	public function getElement( $name ) 
	{
		if ( is_file( $this->_stubs.'/'.$name.'/element.php' ) ) {
			return include $this->_stubs.'/'.$name.'/element.php';
		}

		return false;
	}

	// getLayout
	public function getLayout( $json = false ) 
	{
		if ( $json ) {
			return json_encode( $this->layout );
		}

		return $this->layout;
	}

	// getModel
	public function getModel() 
	{
		$suffix		=	'_item';
		$matches	=	[];

		$walker	=	function ( mixed $n ) use ( &$walker, &$matches, $suffix ): void {
			if ( is_object( $n ) ) {
				if ( isset( $n->type ) && str_ends_with( $n->type, $suffix ) ) {
					$matches[]	=	$n;
				}

				if ( isset( $n->children ) && is_iterable( $n->children ) ) {
					foreach ( $n->children as $child ) {
						$walker( $child );
					}
				}
			} elseif ( is_array( $n ) ) {
				foreach ( $n as $value ) {
					$walker( $value );
				}
			}
		};

		$walker( $this->layout );

		if ( isset( $matches[0] ) && is_object( $matches[0] ) ) {
			return 	array(
						'type'=>str_replace( '_item', '', $matches[0]->type ),
						'item'=>$matches[0]
					);
		}

		return false;
	}

	// getSources
	public function getSources( &$model ) 
	{
		$sources	=	array();

		// Static
		foreach ( $model->props as $property => $value ) {
			$sources[$property]	=	$value;
		}

		// Dynamic
		foreach ( $model->source->props as $property => $data ) {
			$sources[$property]	=	$data->name;
		}

		unset( $model->source );

		return $sources;
	}

	// render
	public function render(): string
	{
		$layout	=	json_decode( $this->getLayout( true ), true );

		if ( $layout === false ) {
			return 'JSON Error...';
		}

		$html	=	'<div class="yo-builder uk-flex uk-flex-column uk-flex-center">'.PHP_EOL;

		foreach ( $layout['children'] as $s => $section ) {
			$html	.=	'    <div class="yo-builder-section">'.PHP_EOL
					.	'        <div class="uk-flex uk-flex-column uk-flex-center">'.PHP_EOL
					.	'            <div class="yo-builder-grid uk-margin-auto">'.PHP_EOL;

			foreach ( ( $section['children'] ?? [] ) as $r => $row ) {
				$html	.=	'                <div class="">'.PHP_EOL
						.	'                    <div class="uk-grid uk-grid-match">'.PHP_EOL;

				// Extraire les ratios de layout si présent
				$ratios	=	[];

				if ( !empty( $row['props']['layout'] ) ) {
					$parts	=	explode( ',', $row['props']['layout'] );
					
					foreach ( $parts as $part ) {
						$ratios[]	=	'uk-width-'.$part;
					}
				}

				$cols		=	$row['children'] ?? [];
				$numCols	=	count( $cols );

				foreach ( $cols as $c => $col ) {
					$width	=	$ratios[$c] ?? $this->_widthClass( $numCols );
					$html	.=	"                        <div class=\"$width\"> <!---->".PHP_EOL
							.	'                            <div class="uk-flex uk-flex-column">'.PHP_EOL;

					foreach ( ( $col['children'] ?? []) as $e => $el ) {
						$type	=	$el['type'] ?? 'element';
						$dataId	=	"page#{$s}-{$r}-{$c}-{$e}";
						$icon	=	$this->_iconPath( $type );
						$label	=	ucfirst( $type );

						$html	.=	"                                <div data-id=\"$dataId\" class=\"yo-builder-element uk-flex-1 uk-width-1-1 uk-flex uk-flex-center uk-flex-middle\">".PHP_EOL
								.	'                                    <div class="uk-grid uk-grid-column-small uk-grid-row-collapse uk-flex-center uk-flex-middle uk-width-1-1 uk-text-center">'.PHP_EOL
								.	'                                        <div class="uk-width-auto">'.PHP_EOL
								.	"                                            <img alt=\"$label\" src=\"$icon\" width=\"20\" height=\"20\" uk-svg hidden>".PHP_EOL
								.	'                                        </div>'.PHP_EOL
								.	"                                        <div class=\"uk-width-auto uk-text-truncate\">$label</div>".PHP_EOL
								.	'                                    </div>'.PHP_EOL
								.	'                                </div>'.PHP_EOL;
					}

					$html	.=	'                            </div>'.PHP_EOL
							.	'                        </div>'.PHP_EOL;
				}

				$html	.=	'                    </div>'.PHP_EOL
						.	'                </div>'.PHP_EOL;
			}

			$html	.=	'            </div>'.PHP_EOL
					.	'        </div>'.PHP_EOL
					.	'    </div>'.PHP_EOL;
		}

		return $html.'</div>';
	}

	// setSources
	public function setSources( $sources, $type ) 
	{
		$layout	=	$this->layout;

		while ( isset( $layout->type ) && $layout->type !== $type ) {
			$layout	=	$layout->children[0];
		}

		$layout->children	=	$sources;
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Protected

	// _addEditButtons
	public function _addEditButtons( string $type ): string
	{
		$layout				=	$this->layout;
		$link_edit			=	'#3';	// https://ratel6x.me/admin/yootheme/section-manager/form/o_builder_section?id=184&return=aHR0cHM6Ly9yYXRlbDZ4Lm1lL2FkbWluL3lvb3RoZW1lL3NlY3Rpb24tbWFuYWdlcg==
		$link_customizer	=	self::getCustomizerLink( $type );
		
		include $this->_stubs.'/edit-buttons/element.php';

		if ( empty( $layout->children ) || !is_array( $layout->children ) ) {
			return $layout;
		}

		foreach ( $layout->children as $key => &$child ) {
			if ( isset( $child->type ) && $child->type === 'section' ) {
				$child	=	$this->_addVisibleToggleClass( $child );

				if ( !isset( $child->children ) ) {
					$child->children	= 	[];
				}

				array_unshift( $child->children, $element );
				break; 
			}
		}

		return json_encode( $layout, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
	}

	// _addVisibleToggleClass
	protected function _addVisibleToggleClass( $element )
	{
		$class	=	'uk-visible-toggle';

		if ( !isset( $element->props ) ) {
			$element->props	=	new \stdClass();
		}

		if ( !isset( $element->props->class ) ) {
			$element->props->class	=	$class;
		} else {
			$classes	=	explode( ' ', $element->props->class );

			if ( !in_array( $class, $classes ) ) {
				$element->props->class	.=	' '.$class;
			}
		}

		return $element;
	}

	// _clean
	protected function _clean( $json, $array = false ) 
	{
		$json	=	trim( trim( $json ), "\xEF\xBB\xBF" );

		if ( substr( $json, 0, 4 ) == '<!--' ) {
			$json	=	trim( str_replace( array( '<!--', '-->' ), '', $json ) );
		}

		if ( $json === '' || $json[0] !== '{' ) {
			return false;
		}

		return json_decode( $json, $array );
	}

	// _iconPath
	protected static function _iconPath( string $type ): string
	{
		return "/templates/yootheme/packages/builder/elements/{$type}/images/iconSmall.svg";
	}

	// _transformNode
	protected function _transformNode( &$node, string $target_name = 'seblod' )
	{
		if ( is_null( $this->cck ) ) {
			return $node;
		}

		if ( is_object( $node ) ) {
			if ( isset( $node->source->query->name ) && $node->source->query->name === $target_name ) {
				if ( !isset( $node->props ) || !is_object( $node->props ) ) {
					$node->props	=	new \stdClass();
				}

				if ( isset( $node->source->props ) && is_object( $node->source->props ) ) {
					foreach ( $node->source->props as $prop => $data ) {
						$field_value			=	$this->cck->renderField( $data->name );
						$node->props->{$prop}	=	trim( $field_value );
					}
				}

				unset( $node->source );
			}

			foreach ( get_object_vars( $node ) as $key => $value ) {
				$this->_transformNode( $node->$key, $target_name );
			}
		} elseif ( is_array( $node ) ) {
			foreach ( $node as &$element ) {
				$this->_transformNode( $element, $target_name );
			}
		}

		return $node;
	}

	// _widthClass
	protected function _widthClass( int $cols ): string
	{
		$map	=	[1=>'1-1', 2=>'1-2', 3=>'1-3', 4=>'1-4', 5=>'1-5', 6=>'1-6'];

		return 'uk-width-'.( $map[$cols] ?? '1-1' );
	}

	// -------- -------- -------- -------- -------- -------- // Static Public

	// getCustomizerLink
	public static function getCustomizerLink( string $type ): string
	{
		// Get Refs
		list( $type, $pk )	=	self::_getReferences( $type );

		// Construct
		$uri		=	Uri::getInstance();
		$base		=	$uri->toString( array( 'scheme', 'user', 'pass', 'host', 'port' ) );
		$current	=	Uri::current();
		$query		=	$uri->getQuery();

		$query		=	array(
							'cck='.$type,
							'pk='.$pk,
							'back='.base64_encode( $current )
						);
		$url		=	$base
					.	str_replace( '?view=processing', '', \ProjectHelper::getUrl( 'nav_items', 'customizer-open' ) )
					.	'?'.implode( '&', $query );
		
		return $url;		
	}

	// getSeblodFields
	public static function getSeblodFields() 
	{	
		$exclude 	= 	'"'.implode( '","', array( 'code_beforerender', 'code_css', 'code_js', 'div', 'field_x', 'form_action', 'item_x', 'jform_associations', 'jform_componentlayout', 'jform_menuitem', 'jform_rules', 'jform_templatestyle', 'jform_timezone', 'jform_usergroups', 'message_redirection', 'password', 'search_breadcrumbs', 'search_generic', 'search_join', 'search_operator', 'search_ordering', 'search_query', 'search_total', 'storage', 'tabs' ) ).'"';
		$fields 	=	\JCckDatabase::loadObjectList( 'SELECT title, name FROM #__cck_core_fields WHERE type NOT IN ('.$exclude.') ORDER BY title ASC' );
		$results 	=	array();

		foreach ( $fields as $field ) {
			$results[$field->name] 	=	array(
											'type' => 'String',
											'metadata' => [
												'label' => $field->title
											]
										);
		}

		return $results;
	}

	// -------- -------- -------- -------- -------- -------- // Static Protected

	// _getReferences
	public static function _getReferences( string $type ): array
	{
		$query	=	'SELECT a.cck, b.id'
				.	' FROM #__cck_core AS a'
				.	' LEFT JOIN #__cck_store_item_content AS b ON b.id = a.pk'
				.	' WHERE b.section_type="'.$type.'"';

		$ref	=	\JCckDatabase::loadObject( $query );

		return array( $ref->cck, $ref->id );
	}	
}