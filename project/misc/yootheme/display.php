<?php
defined( '_JEXEC' ) or die;

use Joomla\CMS\HTML\HTMLHelper;

if ( !isset( $options ) ) {
    return false;
}

echo HTMLHelper::_( 'content.prepare', '::cck::'.$value.'::/cck::', null );
?>