<?php
use YooSeblod\Integration;
use Joomla\CMS\Factory;

defined( '_JEXEC' ) or die;

static $isSuperUser	=	0;
static $viewlevels	=	array(
							'3'=>'special',
							'7'=>'admin',
							'6'=>'super-user'
						);

if ( $isSuperUser === 0 ) {
	$isSuperUser	=	Factory::getUser()->authorise( 'core.admin' );
}

// Base
$attr	=	'';
$class	=	trim( $displayData['field']->markup_class );
$desc	=	'';
$hasId	=	false;
$label	=	'';

// Init
YooSeblod\Integration\YooUikit::markupField( $displayData['field'] );

// Computation
if ( isset( $displayData['field']->computation ) && $displayData['field']->computation ) {
	$displayData['cck']->setComputationRules( $displayData['field'] );

	$hasId	=	true;
}

// Conditional
if ( isset( $displayData['field']->conditional ) && $displayData['field']->conditional ) {
	$displayData['cck']->setConditionalStates( $displayData['field'] );

	$hasId	=	true;
}

if ( $hasId ) {
	$attr	=	' id="'.$displayData['cck']->id.'_'.$displayData['field']->name.'"';
}

if ( $isSuperUser && isset( $viewlevels[(string)$displayData['field']->access] ) ) {
	$attr	.=	' data-access="'.$viewlevels[(string)$displayData['field']->access].'"';
}

// Description
if ( $displayData['options']->get( 'field_description', $displayData['cck']->getStyleParam( 'field_description', 0 ) ) ) {
	if ( $displayData['field']->description != '' ) {
		if ( $displayData['options']->get( 'field_description', $displayData['cck']->getStyleParam( 'field_description', 0 ) ) == 5 ) {
			$desc_class	=	'';
			if ( isset( $displayData['field']->description_class ) ) {
				$desc_class	=	' '.$displayData['field']->description_class;
			}

			$desc	=	''
					.	'<div class="cck-description">'
    				.	'<a class="'.$displayData['field']->type.' uk-form-icon'.$desc_class.'" href="#" uk-icon="icon: info"></a>'
	    			.	'<div class="uk-card uk-card-body uk-card-default" uk-drop="pos: right-center; mode: click">'.$displayData['field']->description.'</div>'
					.	$displayData['field']->form
					.	'</div>';

			$displayData['field']->form	=	$desc;
		} else {
			$desc	=	$displayData['field']->description;
			$desc	=	 '<span><span class="o-help" uk-icon="icon: info; ratio: 0.8"></span> <em>'.$desc.'</em></span>';

			$displayData['field']->form	.=	$desc;
		}
	}
}

// Label
if ( $displayData['options']->get( 'field_label', $displayData['cck']->getStyleParam( 'field_label', 1 ) ) ) {
	$label	=	$displayData['cck']->getLabel( $displayData['field']->name, true, ( $displayData['field']->required ? '*' : '' ) );

	if ( $label != '' ) {
		$label	=	str_replace( '<label', '<label class="uk-form-label"', $label );
	}
}

if ( $class != '' ) {
	$class	=	' '.$class;
}

// Display
if ( is_array( $displayData['field']->form ) ) {
	$form	=	'';

	foreach ( $displayData['field']->form as $key => $f ) {
		$form	.=	$f->form;
	}
} else {
	$form	=	$displayData['field']->form;
}

?>
<?php if ( $displayData['cck']->client !== 'search' ) { ?>
	<div class="uk-margin<?php echo $class; ?>"<?php echo $attr; ?>>
		<?php echo $label; ?>
		<div class="uk-form-controls">
			<div class="uk-inline uk-width-1-1">
				<?php echo $form; ?>
			</div>
		</div>
	</div>
<?php } else { ?>
	<?php echo $displayData['field']->form; ?>
<?php } ?>
