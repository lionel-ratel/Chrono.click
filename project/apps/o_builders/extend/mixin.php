<?php
defined( '_JEXEC' ) or die;

$mixin	=	new class() {
	use JCckContentTraitMixin;

	// _getJSON
	protected function _getJSON()
	{
		return function() {
			$query			=	'SELECT custom_data FROM #__extensions'
							.	' WHERE type="plugin" AND element="yootheme" AND folder="system"';

			$custom_data	=	json_decode( JCckDatabase::loadResult( $query ) );
			$templates		=	$custom_data->templates;

			if ( !is_null( $json ) ) {
				return $json;
			}

			return false;
		};
	}

	// _storeJSON
	protected function _storeJSON()
	{
		return function( $json ) {
			$query	=	'UPDATE #__extensions'
					.	' SET custom_data="'.JCckDatabase::escape( $json ).'"'
					.	' WHERE type="plugin" AND element="yootheme" AND folder="system"';

			return JCckDatabase::execute( $query );
		};
	}
};
?>