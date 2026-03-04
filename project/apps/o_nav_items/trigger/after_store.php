<?php
defined( '_JEXEC' ) or die;

if ( !( isset( $this ) && $this->isSecure() ) ) {
	return;
}

$query	=	'SELECT a.description, a.menutype'
			.	' FROM #__menu_types AS a'
			.	' LEFT JOIN #__menu AS b ON b.menutype = a.menutype'
			.	' WHERE b.id='.(int)$this->getPk();

$menu	=	JCckDatabase::loadObject( $query );

preg_match( '/{(.*?)}/s', $menu->description, $matches );

if ( isset( $matches[0] ) ) {
	$param	=	json_decode( $matches[0] );

	if ( isset( $param->template ) && (int)$param->template ) {
		JCckDatabase::execute( 'UPDATE #__menu SET template_style_id='.(int)$param->template.' WHERE menutype="'.$menu->menutype.'"' );
	}
}
?>