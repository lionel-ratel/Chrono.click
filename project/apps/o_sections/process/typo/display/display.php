<?php
defined( '_JEXEC' ) or die;

use Joomla\CMS\HTML\HTMLHelper;

if ( !isset( $options ) ) {
    return false;
}

$typo   =  HTMLHelper::_( 'content.prepare', '::cck::'.$config['id'].'::/cck::' );