<?php
use YooSeblod\Integration;

defined( '_JEXEC' ) or die;

// -- Initialize
require_once __DIR__.'/config.php';
$cck	=	CCK_Rendering::getInstance( $this->template );
if ( $cck->initialize() === false ) { return; }

// -- Prepare
$attributes	=	$cck->id_attributes ? ' '.$cck->id_attributes : '';
$attributes	=	$cck->replaceLive( $attributes );

// -- Render
if ( $cck->id_class != '' ) {
	echo '<div class="'.trim( $cck->id_class ).'"'.$attributes.'>';
}

// Toolbar
$toolbar1	=	$cck->renderPosition( 'toolbar1', 'none' );

if ( $toolbar1 != '' ) {
	echo '<div class="uk-margin uk-grid-small" uk-grid>';
		echo $toolbar1;
	echo '</div>';
}

// Filters
$filter1	=	trim( $cck->renderPosition( 'filter1' ) );
$filter1_2	=	trim( $cck->renderPosition( 'filter1_2' ) );

if ( $filter1 || $filter1_2 ) {
	echo '<div class="uk-margin uk-grid-small" uk-grid>';

	if ( $filter1 != '' ) {
		echo	'<div class="uk-width-5-6 uk-grid-small" uk-grid>'.$filter1.'</div>';
	}

	if ( $filter1_2 != '' ) {
		echo '<div class="uk-width-1-6">'.$filter1_2.'</div>';
	}

	echo '</div>';
}

// Filters: Advanced
$filter2	=	$cck->renderPosition( 'filter2' );

if ( $filter2 != '' ) {
	echo '<div class="uk-margin uk-grid-small uk-child-width-1-6" uk-grid>';
	echo $filter2;
	echo '</div>';
}

echo '<hr class="hr-manager uk-divider-icon">';

// Info
$info1		=	$cck->renderPosition( 'info1', 'uikit_search' );
$info1_2	=	$cck->renderPosition( 'info1_2', 'uikit_search' );

if ( $info1 || $info1_2 ) {
	echo '<div class="uk-margin uk-grid-small" uk-grid>';

	if ( $info1 != '' ) {
		echo	'<div class="uk-width-5-6 uk-grid-small" uk-grid>'.$info1.'</div>';
	}

	if ( $info1_2 != '' ) {
		echo '<div class="uk-width-1-6">'.$info1_2.'</div>';
	}

	echo '</div>';
}

if ( $cck->countFields( 'mainbody' ) ) {
	echo $cck->renderPosition( 'mainbody' );
}

if ( $cck->id_class != '' ) {
	echo '</div>';
}

for ( $i = 1; $i <= 5; $i++ ) {
	$suffix	=	( $i == 1 ) ? '' : $i;

	if ( $cck->countFields( 'modal'.$suffix ) ) {
		$variation	=	$cck->getPosition( 'modal'.$suffix )->variation;
		$opts		=	array();

		if ( $variation === 'uikit_modal' ) {
			$opts 	= 	json_decode( $cck->getPosition( 'modal'.$suffix )->variation_options, true );
		}
		
		echo YooSeblod\Integration\YooUikit::getModalHtml( 'collapseModal'.$suffix, $cck->renderPosition( 'modal'.$suffix ), $opts );

		$cck->addJS( 'const $modal = $("#collapseModal'.$suffix.'");$modal.detach().appendTo("body");' );
	}
}

$buffer	=	$cck->renderPosition( 'hidden' );

if ( $cck->countFields( 'hidden' ) && $buffer != '' ) { ?>
	<div style="display: none;">
		<?php echo $buffer; ?>
	</div>
<?php }

// -- Finalize
$cck->finalize();
?>
