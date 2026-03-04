<?php
defined( '_JEXEC' ) or die;

if ( !isset( $options ) ) {
	return;
}

$html	=	file_get_contents( 'https://getuikit.com/docs/icon' );
$dom	=	new DOMDocument();

libxml_use_internal_errors( true );
$dom->loadHTML( $html );
libxml_clear_errors();

$xpath		=	new DOMXPath( $dom );
$resultat	=	[];
$h4_nodes	=	$xpath->query( '//h4[@class="uk-heading-line"]' );

foreach ( $h4_nodes as $h4 ) {
    $titre_node	=	$xpath->query( './/span', $h4 )->item( 0 );
    $titre		=	$titre_node ? trim( $titre_node->textContent ) : 'inconnu';
    $next_div	=	$h4->nextSibling;

    while ( $next_div && ( $next_div->nodeType !== XML_ELEMENT_NODE || $next_div->nodeName !== 'div' ) ) {
        $next_div	=	$next_div->nextSibling;
    }

    $icons	=	[];

    if ( $next_div ) {
        $span_nodes	=	$xpath->query('.//ul/li/span[@uk-icon]', $next_div);

        foreach ( $span_nodes as $span ) {
            $icons[]	=	$span->getAttribute( 'uk-icon' );
        }
    }

    $resultat[$titre]	=	$icons;
}

if ( empty( $resultat ) ) {
	return;
}

$query	=	'SELECT a.id, b.icon_alias'
		.	' FROM #__categories AS a'
		.	' LEFT JOIN #__cck_store_item_categories AS b ON b.id = a.id'
		.	' WHERE a.parent_id=40';

$items	=	JCckDatabase::loadObjectList( $query );
$cats	=	[];

foreach ( $items as $item ) {
	$cats[$item->icon_alias]	=	$item->id;	
}

$content	=	new JCckContentFree;
$content->setTable( '#__cck_store_form_o_icon' );

foreach ( $resultat as $key => $values ) {
	if ( !isset( $cats[$key] ) ) {
			echo '<pre>';
			echo 'New Category : '.$key;
			echo '</pre>';
		continue;
	}

	foreach ( $values as $value ) {
		$exists	=	(int)JCckDatabase::loadResult( 'SELECT id FROM #__cck_store_form_o_icon WHERE title="'.$value.'"' );

		if ( !$exists ) {
			$data	=	array(
							'title'=>$value,
							'published'=>1,
							'access'=>1,
							'language'=>'*',
							'catid'=>$cats[$key]
						);

			$content->create( 'o_icon', $data );

			echo '<pre>';
			print_r( $value );
			echo '</pre>';

		}
	}
}
?>

