<?php
defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;

$mixin	=	new class() {
	use JCckContentTraitMixin;

	// _getBody
	protected function _getBody()
	{
		return function( $data ) {
			return $this->_replaceData( $this->getProperty( 'body' ), $data );
		};
	}

	// _getCopies
	protected function _getCopies()
	{
		return function( $property = 'cc' ) {
			$dest	=	$this->getProperty( $property );

			if ( $dest == '' ) {
				return array();
			}

			return $this->_split( $dest );
		};
	}

	// _getHeader
	protected function _getHeader()
	{
		return function( $property, $data = array() ) {
			$app	=	Factory::getApplication();
			$result	=	$this->getProperty( $property );

			if ( $result == '' ) {
				return $app->get( $property, null );
			} elseif ( count( $data ) && strpos( $result, '{' ) !== false ) {
				$result	=	$this->_replaceData( $result, $data );
			}

			return $result;
		};
	}

	// _getSubject
	protected function _getSubject()
	{
		return function( $data ) {
			return $this->_replaceData( $this->getProperty( 'subject' ), $data );
		};
	}

	// _replaceData
	protected function _replaceData()
	{
		return function( $text, $data ) {
			preg_match_all( '/{(.*?)}/', $text, $matches );

			if ( !( isset( $matches[1] ) && count( $matches[1] ) ) ) {
				return $text;
			}

			$replace	=	array();
			$search		=	array();

			foreach ( $matches[1] as $value ) {
				if ( isset( $data[$value] ) ) {
					$search[]	=	'{'.$value.'}';
					$replace[]	=	$data[$value];
				}
			}

			return str_replace( $search, $replace, $text );
		};
	}

	// _updateBody
	protected function _updateBody()
	{
		return function() {
			$list	=	new JCckList;

			$list->load( 'o_mail_template_content_view' );

			$buffer	=	$list->with( 'o_mail_template_name', '=', $this->getProperty( 'name' ) )
							 ->with( 'o_mail_template_language', '=', $this->getProperty( 'language' ) )
							 ->output();

			$this->setProperty( 'body', $buffer )->store();
		};
	}

	// _split
	protected function _split()
	{
		return function( $string ) {
			$string		=	str_replace( array( ' ', "\r" ), '', $string );

			if ( $string == '' ) {
				return array();
			}

			if ( strpos( $string, ',' ) !== false ) {
				$tab	=	explode( ',', $string );
			} else if ( strpos( $string, ';' ) !== false ) {
				$tab	=	explode( ';', $string );
			} else {
				$tab	=	array( 0=>$string );
			}
			
			return $tab;
		};
	}
};
?>