<?php
defined( '_JEXEC' ) or die;

use Joomla\Filesystem\File;

if ( !( isset( $this ) && $this->isSecure() ) ) {
	return;
}

$languages 			=	array();
$type 				=	$this->getValue( 'o_constant_filename' );

foreach ( JCckDatabase::loadColumn( 'SELECT lang_code FROM #__languages WHERE published=1' ) as $tag ) {
	$languages[$tag] 	=	array();
}

$content_free 	=	new JCckContentFree;
$content_free->setTable( '#__cck_store_form_o_constant' );
$data 			=	array(
						'filename'=>$type
					);

foreach ( $content_free->search( 'o_constant', $data )->by( 'constant', 'asc' )->findPks() as $pk ) {
	if ( $content_free->load( $pk )->isSuccessful() ) {
		foreach ( $languages as $tag => $langue ) {
			$row 		=	$content_free->getProperty( 'constant' );
			$translate 	=	$content_free->getProperty( str_replace( '-', '_', strtolower( $tag ) ) );
			
			if ( $translate != '' ) {
				$clean 				=	str_replace( array( '\"', '\""' ), '"', $translate );
				$clean				=	str_replace( "\r\n", '<br>', $clean );
				$clean				=	str_replace( "\n", '<br>', $clean );
				$languages[$tag][] 	=	$row.'="'.str_replace( '"', '\"', $clean ).'"';	
			}
		}
	}
}

if ( $type == 'override' ) {
	foreach ( $languages as $tag => $language ) {

		$file_admin 	=	JPATH_SITE.'/administrator/language/overrides/'.$tag.'.override.ini';
		$file_site 		=	JPATH_SITE.'/language/overrides/'.$tag.'.override.ini';
		$buffer 		=	implode( "\r", $language );

		File::write( $file_admin, $buffer );
		File::write( $file_site, $buffer );
	}
} else {
	foreach ( $languages as $tag => $language ) {
		$file_site 		=	JPATH_SITE.'/language/'.$tag.'/'.$tag.'.com_cck_default.ini';
		$buffer 		=	implode( "\r", $language );

		File::write( $file_site, $buffer );
	}
}
?>