<?php
defined( '_JEXEC' ) or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;
use YooSeblod\Integration;

if ( !isset( $options ) ) {
	return false;
}

// Add placeholder if needed
$query	=	'SELECT b.name, b.type'
		.	' FROM #__cck_core_type_field AS a'
		.	' LEFT JOIN #__cck_core_fields AS b ON b.id = a.fieldid'
		.	' WHERE a.client="admin" AND a.typeid='.(int)$config['type_id']
		.	' AND b.type IN ("text", "textarea", "upload_image2") AND b.name NOT LIKE "%_link_%";';

$items	=	JCckDatabase::loadObjectList( $query );

$uri	=	Uri::getInstance();
$base	=	$uri->toString( array( 'scheme', 'user', 'pass', 'host', 'port' ) );
$image	=	$base.'/templates/yootheme/assets/images/element-image-placeholder.png';

foreach ( $items as $item ) {
	if ( $fields[$item->name]->value === '' || $fields[$item->name]->value === 'cck-empty' ) {
		switch ( $item->type ) {
			case 'text':
				$fields[$item->name]->value	=	'Lorem...';
				break;
			case 'textarea':
				$fields[$item->name]->value	=	'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.';
				break;
			case 'upload_image2':
				$fields[$item->name]->value			=	$image;
				$fields[$item->name]->html			=	$image;
				break;
			default:
				// code...s
				break;
		}
	}
}

// Get Layout
$json		=	JCckDatabase::loadResult( 'SELECT json FROM #__cck_store_item_content WHERE section_type="'.$config['type'].'";' );
$section	=	new \YooSeblod\Integration\YooLayout( $json );

// Add Edit Buttons
$section->_addEditButtons( $config['type'] );

// Display
$fields[$name]->value	=	$section->getLayout( json: true );