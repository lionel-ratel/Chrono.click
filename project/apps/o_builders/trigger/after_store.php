<?php
defined( '_JEXEC' ) or die;

if ( !( isset( $this ) && $this->isSecure() ) ) {
	return;
}

/*
$json	=	$this->getValue( 'o_builder_json' );

if ( $this->isNew() || $json === '' ) {
	$path	=	JPATH_SITE.'/templates/yootheme/templateDetails.xml';

	if ( file_exists( $path ) ) {
	    $xml		=	simplexml_load_file( $path );
	    $version	=	(string)$xml->version;
	} else {
		$version	=	'4.5.14';
	}

	$data = [
	    'type' => 'layout',
	    'children' => [
	        [
	            'type' => 'section',
	            'props' => [
	                'image_position'   => 'center-center',
	                'style'            => 'default',
	                'title_breakpoint' => 'xl',
	                'title_position'   => 'top-left',
	                'title_rotation'   => 'left',
	                'vertical_align'   => 'middle',
	                'width'            => 'default',
	            ],
	            'children' => [
	            	[
	                    "type" => "column",
	                    "props" => [
	                        "image_position" => "center-center",
	                        "position_sticky_breakpoint" => "m"
	                    ]
	            	]
	            ]
	        ]
	    ],
	    'version' => $version
	];
	
	$content    =   new JCckContentArticle;
	$content->setOptions( array( 'trigger_events'=>0 ) );

	if ( $content->load( $this->getPk() )->isSuccessful() ) {
		$content->setProperty( 'fulltext', '<!-- '.json_encode( $data ).' -->' )
				->store();
	}
}
*/

//
if ( $this->getValue( 'o_builder_type' ) === 'section' ) {
	$title	=	JCckDatabase::loadResult( 'SELECT title FROM #__cck_core_types WHERE name="'.$this->getValue( 'o_builder_section' ).'";' );

	$this->setValue( 'o_builder_title', $title );
}
?>