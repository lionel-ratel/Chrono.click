<?php
defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\Filesystem\File;

// ProjectHelper
abstract class ProjectHelper
{
	const REGISTERED			=	2;
	const SUPER_USER			=	8;

	// ---

	protected static $nav_items		=	array(
											'fr-FR'	=>	array(
															'login'=>406,

															// Customizer
															'customizer-open'=>236,
															'customizer-store'=>237,
															'customizer-edit'=>234
														)
										);

	// ---

	public static function renderBuilder( string $json ): string
	{
		$layout	=	json_decode( $json, true );

		//
		$html	=	'<div class="yo-builder uk-flex uk-flex-column uk-flex-center">'.PHP_EOL;

		foreach ( $layout['children'] as $s => $section ) {
			$html	.=	'    <div class="yo-builder-section">'.PHP_EOL
					.	'        <div class="uk-flex uk-flex-column uk-flex-center">'.PHP_EOL
					.	'            <div class="yo-builder-grid uk-margin-auto">'.PHP_EOL;

			foreach ( ( $section['children'] ?? [] ) as $r => $row ) {
				$html	.=	'                <div class="">'.PHP_EOL
						.	'                    <div class="uk-grid uk-grid-match">'.PHP_EOL;

				$numCols	=	count( $row['children'] ?? [1] );   // pour le width

				foreach ( ( $row['children'] ?? [] ) as $c => $col ) {
					$width	=	self::_widthClass( $numCols );        // uk-width-1-1 | uk-width-1-2 | …
					$html 	.= 	"                        <div class=\"$width\"> <!---->".PHP_EOL
							.	'                            <div class="uk-flex uk-flex-column">'.PHP_EOL;

					foreach ( ( $col['children'] ?? []) as $e => $el ) {
						$type	=	$el['type'] ?? 'element';
						$dataId	=	"page#{$s}-{$r}-{$c}-{$e}";
						$icon	=	self::_iconPath( $type );
						$label	=	ucfirst( $type );

						$html	.= "                                <div data-id=\"$dataId\" class=\"yo-builder-element uk-flex-1 uk-width-1-1 uk-flex uk-flex-center uk-flex-middle\">".PHP_EOL
								. '                                    <div class="uk-grid uk-grid-column-small uk-grid-row-collapse uk-flex-center uk-flex-middle uk-width-1-1 uk-text-center">'.PHP_EOL
								. '                                        <div class="uk-width-auto">'.PHP_EOL
								. "                                            <img alt=\"$label\" src=\"$icon\" width=\"20\" height=\"20\" uk-svg hidden>".PHP_EOL
								. '                                        </div>'.PHP_EOL
								. "                                        <div class=\"uk-width-auto uk-text-truncate\">$label</div>".PHP_EOL
								. '                                    </div>'.PHP_EOL
								. '                                </div>'.PHP_EOL;
					}

					$html	.=	'                            </div>'.PHP_EOL
							.	'                        </div>'.PHP_EOL;
				}

				$html	.=	'                    </div>'.PHP_EOL
						.	'                </div>'.PHP_EOL;
			}

			$html	.=	'            </div>'.PHP_EOL
					.	'        </div>'.PHP_EOL
					.	'    </div>'.PHP_EOL;
		}

		return $html.'</div>';
	}

	protected static function _widthClass( int $cols ): string
	{
		static $map = [1=>'1-1', 2=>'1-2', 3=>'1-3', 4=>'1-4', 5=>'1-5', 6=>'1-6'];

		return 'uk-width-'.( $map[$cols] ?? '1-1' );
	}

	protected static function _iconPath( string $type ): string
	{
		return "/templates/yootheme/packages/builder/elements/{$type}/images/iconSmall.svg";
	}

	// Common -----------------------

	// getItemID
	public static function getItemID( $pk )
	{
		$link	=	'index.php?option=com_content&view=article&id='.$pk;

		return (int)JCckDatabase::loadResult( 'SELECT id FROM #__menu WHERE link="'.$link.'"' );
	}

	// getUrl
	public static function getUrl( $property, $target = '' )
	{
		if ( $property ) {
			$lang_tag	=	Factory::getLanguage()->getTag();

			if ( $target !== '' ) {	
				if ( isset( self::${$property}[$lang_tag][$target] ) ) {
					return Route::_( 'index.php?Itemid='.self::${$property}[$lang_tag][$target] );
				}
			} else {
				return Route::_( 'index.php?Itemid='.(int)$property );
			}
		}

		return '';
	}

	// getConstantInLanguage
	public static function getConstantInLanguage( $constant, $lang_tag )
	{
		$current_tag	=	Factory::getLanguage()->getTag();

		if ( $lang_tag != $current_tag ) {
			JCckDevHelper::setLanguage( $lang_tag );
		}

		$text	=	JText::_( $constant );

		if ( $lang_tag != $current_tag ) {
			JCckDevHelper::setLanguage( $current_tag );
		}

		return $text;
	}

	// getFieldOptions2
	public static function getFieldOptions2( $field_name, $property = '' )
	{
		$options2	=	JCckDatabse::loadResult( 'SELECT options2 FROM #__cck_core_fields WHERE name="'.$field_name.'";' );
		$options2	=	json_decode( $options2, true );

		if ( $property != '' ) {
			return $options2[$property];
		}

		return $options2;
	}

	// isGuest
	public static function isGuest()
	{
		$user	=	Factory::getUser();

		return $user->id && !$user->guest ? false : true;
	}

	// isOffline
	public static function isOffline()
	{
		return false;
	}

	// redirect
	public static function redirect( $dest_url, $code = 301 )
	{
		$app	=	Factory::getApplication();
		$len	=	strlen( $dest_url );

		if ( $dest_url[($len - 1)] == '/' ) {
			$dest_url	=	substr( $dest_url, 0, -1 );
		}

		$app->redirect( $dest_url, $code );
	}

	// ucword
	public static function ucwords( $text )
	{
		foreach ( array( ' ', '-' ) as $separator ) {
			$text 	=	ucwords( $text, $separator );				
		}
		return $text;
	}

	// updateConstantFile
	public static function updateConstantFile( $lang_tag, $filename )
	{
		$content_free 	=	new JCckContentFree;
		$data			=	array( 'filename'=>$filename );
		$languages		=	array( $lang_tag=>array() );

		$content_free->setTable( '#__cck_store_form_o_constant' );

		foreach ( $content_free->search( 'o_constant', $data )->by( 'constant', 'asc' )->findPks() as $pk ) {
			if ( $content_free->load( $pk )->isSuccessful() ) {
				foreach ( $languages as $tag => $langue ) {
					$row 		=	$content_free->getProperty( 'constant' );
					$translate 	=	$content_free->getProperty( str_replace( '-', '_', strtolower( $tag ) ) );

					if ( $translate != '' ) {
						$clean 				=	str_replace( array( '\"', '\""' ), '"', $translate );
						$languages[$tag][] 	=	$row.'="'.str_replace( '"', '\"', $clean ).'"';	
					}
				}
			}
		}

		if ( $filename == 'override' ) {
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

	}

	// _bn2br
	public static function _bn2br( $text )
	{
		return preg_replace( '/\\n/i', "<br />", $text );
	}

	// MailTemplate -----------------------

	// getMailTemplate
	public static function getMailTemplate( $name )
	{
		static $loaded	=	array();

		$loaded	=	array();
		$idx	=	$name;

		if ( !isset( $loaded[$idx] ) ) {
			$content_free 	=	new JCckContentFree;

			$name			=	explode( '@', $name );
			$lang			=	Factory::getLanguage();
			$lang_tag		=	( isset( $name[1] ) && $name[1] ) ? $name[1] : $lang->getTag();

			$data			=	array(
									'name'=>$name[0],
									'language'=>$lang_tag
								);

			$content_free->setTable( '#__cck_store_form_o_mail_template' );
			$content_free->extend( JPATH_SITE.'/project/apps/o_mail_templates/extend/mixin.php' );

			if ( !$content_free->findOne( 'o_mail_template', $data )->isSuccessful() ) {
				$data['language']	=	$lang->getDefault();

				$content_free->findOne( 'o_mail_template', $data );
			}

			$loaded[$idx]	=	$content_free;
		}

		return $loaded[$idx];
	}

	// sendMailTemplate
	public static function sendMailTemplate( $name, $to = '', $variables = array(), $attachment = null, $cc = array() )
	{
		$content_free 	=	self::getMailTemplate( $name );

		$app 			=	Factory::getApplication();
		$body			=	$content_free->_getBody( $variables );
		$bcc			=	$content_free->_getCopies( 'bcc' );
		$cc				=	array_unique( array_merge( $cc, $content_free->_getCopies( 'cc' ) ) );
		$from_email		=	$content_free->_getHeader( 'mailfrom', $variables );
		$from_name		=	$content_free->_getHeader( 'fromname', $variables );
		$reply_email	=	$content_free->_getHeader( 'replyto', $variables );
		$reply_name		=	$content_free->_getHeader( 'replytoname', $variables );
		$subject		=	$content_free->getProperty( 'subject' );

		if ( $to == '' ) {
			$to	=	$content_free->_getCopies( 'to' );
		}

		return Factory::getMailer()->sendMail( $from_email, $from_name, $to, $subject, $body, true, $cc, $bcc, $attachment, $reply_email, $reply_name );
	}

	// Protected -----------------------

	public static function _uniq ( $length = 8 ) 
	{
		$characters		=	'0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    	$randomString	=	'';
    	$charLength		=	strlen( $characters );

    	for ( $i = 0; $i < $length; $i++ ) {
        	$randomString	.=	$characters[rand( 0, $charLength - 1 )];
    	}

    	return $randomString;		
	}
}
?>