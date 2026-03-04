<?php
use YooSeblod\Integration;

defined( '_JEXEC' ) or die;

YooSeblod\Integration\YooUikit::markupField( $displayData['field'] );

$class	=	trim( $displayData['field']->markup_class );
?>
<div class="uk-inline uk-width-expand">
    <span class="uk-form-icon search-input" uk-icon="icon: <?php echo $class; ?>"></span>
    <?php echo $displayData['field']->form; ?>
</div>
