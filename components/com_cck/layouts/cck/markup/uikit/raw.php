<?php
use YooSeblod\Integration;

defined( '_JEXEC' ) or die;

YooSeblod\Integration\YooUikit::markupField( $displayData['field'] );
?>
<?php echo $displayData['field']->form; ?>