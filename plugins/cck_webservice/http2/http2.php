<?php
/**
* @version 			SEBLOD WebServices 1.x
* @package			SEBLOD WebServices Add-on for SEBLOD 3.x
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\Filesystem\File;
use Joomla\CMS\Factory;
use Joomla\Registry\Registry;

// Plugin
class plgCCK_WebserviceHttp2 extends JCckPluginWebservice
{
	protected static $type		=	'http2';
	protected static $path;
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Construct
	
	// onCCK_FieldConstruct
	public function onCCK_WebserviceConstruct( $type, &$data = array() )
	{
		if ( self::$type != $type ) {
			return;
		}
		parent::g_onCCK_WebserviceConstruct( $data );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Call
	
	// onCCK_WebserviceCall
	public function onCCK_WebserviceCall( &$webservice, $fields, $config = array() )
	{
		if ( self::$type != $webservice->type ) {
			return;
		}

		$options	=	new Registry( $webservice->options );
		$options2	=	new Registry( $webservice->options2 );

		if ( isset( $config['task'] ) && $config['task'] == 'stack' ) {
			$webservice->ws_settings	=	self::_prepare( $webservice, $options, $options2, $fields );
		} else {
			if ( !isset( $webservice->ws_settings ) ) {
				$webservice->ws_settings	=	self::_prepare( $webservice, $options, $options2, $fields );
			}
			
			$settings	=	is_object( $webservice->ws_settings ) ? (array)$webservice->ws_settings : $webservice->ws_settings;
			
			self::_remote( $webservice, $options, $options2, $settings );
		}
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Protected

	// _prepare
	protected function _prepare( &$webservice, $options, $options2, $fields )
	{
		try {
			$app	=	Factory::getApplication();
		} catch ( Exception $e ) {
			// OK
		}
		
		$debug			=	(int)JCckWebservice::getConfig_Param( 'debug', 0 );
		$debug_group	=	(int)JCckWebservice::getConfig_Param( 'debug_group', 0 );
		$request		=	$webservice->request;
		$user			=	Factory::getUser();
		$url			=	'';

		if ( $debug_group ) {
			if ( isset( $user->groups[$debug_group] ) ) {
				$debug_group	=	1;
			} else {
				$debug_group	=	0;
			}
		}
		if ( $debug_group || $debug == 1 || ( $debug == 2 && $user->authorise( 'core.admin' ) ) ) {
			$url		=	$options->get( 'url_dev' );
		} else {
			$url		=	$options->get( 'url' );
		}

		if ( $request != '' ) {
			if ( strpos( $request, '$cck->get' ) !== false ) {
				$matches	=	'';
				$search		=	'#\$cck\->get([a-zA-Z0-9_]*)\( ?\'([a-zA-Z0-9_,]*)\' ?\)(;)?#';
				preg_match_all( $search, $request, $matches );
				
				if ( count( $matches[1] ) ) {
					foreach ( $matches[1] as $k=>$v ) {
						$fieldname		=	$matches[2][$k];
						$target			=	strtolower( $v );
						$value			=	'';
						$pos			=	strpos( $target, 'safe' );
						
						if ( $pos !== false && $pos == 0 ) {
							$target		=	substr( $target, 4 );
							$value		=	$fields[$fieldname]->$target;
							$value		=	JCckDev::toSafeID( $value );
						} else {
							$value		=	$fields[$fieldname]->$target;
						}
						
						$request		=	str_replace( $matches[0][$k], $value, $request );
					}
				}
			}

			if ( isset( $app ) && strpos( $request, '$uri->get' ) !== false ) {
				$matches	=	'';
				$search		=	'#\$uri\->get([a-zA-Z]*)\( ?\'?([a-zA-Z0-9_]*)\'? ?\)(;)?#';
				preg_match_all( $search, $request, $matches );

				if ( count( $matches[1] ) ) {
					foreach ( $matches[1] as $k=>$v ) {
						$target		=	'get'.$v;
						$variable	=	$matches[2][$k];
						
						if ( $v == 'Int' ) {
							$request	=	str_replace( $matches[0][$k], (int)$app->input->$target( $variable, '' ), $request );
						} else {
							$request	=	str_replace( $matches[0][$k], $app->input->$target( $variable, '' ), $request );
						}
					}
				}
			}
			if ( $request[0] == '/' ) {
				$request	=	substr( $request, 1 );
			}
			if ( $request != '' ) {
				$length		=	strlen( $url );
				
				if ( $url[$length - 1] == '/' ) {
					$url	=	substr( $url, 0, -1 );
				}
				$url		.=	'/'.$request;
			}
		}

		$webservice->request	=	$request;

		// Prepare
		$agent		=	'Mozilla/5.0';
		$format		=	$webservice->request_format ? $webservice->request_format : 'nvp';
		$headers	=	array();
		$method		=	$webservice->request_method;
		$vars		=	self::_getVars( (array)$options2->get( 'input' ), $format, $fields );

		if ( $method == 'post' || $method == 'put' ) {
			$settings	=	array(
								CURLOPT_POST=>1,
								CURLOPT_POSTFIELDS=>$vars,
								CURLOPT_RETURNTRANSFER=>1,
								CURLOPT_URL=>$url,
								CURLOPT_USERAGENT=>$agent
							);
			if ( $method == 'put' ) {
				$settings[CURLOPT_CUSTOMREQUEST]	=	$method;
			}
		} elseif ( $method == 'delete' ) {
			$settings	=	array(
								CURLOPT_CUSTOMREQUEST=>$method,
								CURLOPT_RETURNTRANSFER=>1,
								CURLOPT_URL=>$url,
								CURLOPT_USERAGENT=>$agent
							);
		} else {
			if ( $format != 'json' ) {
				if ( $vars ) {
					$vars	=	( strpos( $url, '?' ) !== false ) ? '&'.$vars : '?'.$vars;
				}
				$url		.=	$vars;	
			}

			$settings	=	array(
								CURLOPT_RETURNTRANSFER=>1,
								CURLOPT_URL=>$url,
								CURLOPT_USERAGENT=>$agent
							);

			if ( $format == 'json' ) {
				// TODO
			}
		}
		if ( $format == 'json' ) {
			$headers[]	=	'Content-Type: application/json';
		} else {
			$headers[]	=	'Content-Type: application/x-www-form-urlencoded';
		}

		$webservice->query	=	$url;

		if ( $options->get( 'auth', '' ) ) {
			$auth	=	JCckDatabase::loadObject( 'SELECT type, options FROM #__cck_more_webservices_auths WHERE id = '.(int)$options->get( 'auth', '' ) );

			if ( is_object( $auth ) ) {
				$auth_options	=	json_decode( $auth->options, true );

				switch ( $auth->type ) {
					case 'api_key':
						if ( $auth_options['key'] != '' && $auth_options['value'] != '' ) {
							$headers[]	=	$auth_options['key'].': '.$auth_options['value'];
						}

						break;
					case 'basic_auth':
						if ( $auth_options['username'] != '' && $auth_options['password'] != '' ) {
							$headers[]	=	'Authorization: Basic '.base64_encode( $auth_options['username'].':'.$auth_options['password'] );
						}

						break;
					case 'token_auth':
						if ( $auth_options['token'] != '' ) {
							$headers[]	=	'Authorization: Bearer '.$auth_options['token'];
						}

						break;
					default:
						break;
				}
			}
		}

		if ( count( $headers ) ) {
			$settings[CURLOPT_HTTPHEADER]	=	$headers;
		}

		if ( $webservice->response_format == 'file' || $webservice->response_format == 'image' ) {
			$settings[CURLOPT_TIMEOUT]	=	1000;
		}

		$ssl	=	(bool)JCckWebservice::getConfig_Param( 'calls_http', 1 );

		$settings[CURLOPT_SSL_VERIFYPEER] 	=	$ssl;
		$settings[CURLOPT_SSL_VERIFYHOST] 	=	$ssl ? 2 : 0;

		return $settings;
	}

	// _remote
	protected function _remote( &$webservice, $options, $options2, $settings )
	{
		$isBackend	=	false;
		$response	=	'';
		$ws			=	curl_init();
		
		curl_setopt_array( $ws, $settings );

		$response	=	curl_exec( $ws );
		$root_key	=	$webservice->response;

		// TODO: needed, but to be reviewed
		if ( !empty( $root_key ) && !is_string( $root_key ) ) {
			$root_key	=	json_encode( $root_key );
		}
		if ( $root_key != '' && $root_key[0] == '{' ) {
			$root_key	=	'';
		}

		try {
			$app	=	Factory::getApplication();
			if ( $app->isClient( 'administrator' ) && $app->input->get( 'option' ) == 'com_cck_webservices' && $app->input->get( 'view' ) == 'call' ) {
				$isBackend	=	true;
			}
		} catch ( Exception $e ) {
			// OK
		}

		// Set
		if ( $isBackend ) {
			$root_key						=	'';
			$webservice->response_format	=	'none';
		} elseif ( !$webservice->response_format ) {
			$webservice->response_format	=	'json';
		}
		if ( $webservice->response_format == 'json' ) {
			$response			=	json_decode( $response );
		} elseif ( $webservice->response_format == 'xml' ) {
			$response			=	JCckDev::fromXML( $response, false );
		} elseif ( $webservice->response_format == 'file' || $webservice->response_format == 'image' ) {
			$allowed		=	array();
			$media			=	$options2->get( 'media_extensions', 'common' );

			if ( $media != '' ) {
				$media		=	JCck::getConfig_Param( 'media_'.$media.'_extensions' );
				$allowed	=	explode( ',', $media );
				$allowed	=	array_flip( $allowed );
			}

			$content_type	=	curl_getinfo( $ws, CURLINFO_CONTENT_TYPE );
			$name			=	str_replace( $options->get( 'url' ), '', $webservice->query );
			$response		=	self::_createFile( $response, $name, $webservice->response_format, $content_type, $allowed, (int)$options2->get( 'media_filename', '0' ) );
		}

		if ( $root_key ) {
			$root_key2				=	'';
			$webservice->response	=	'';

			if ( strpos( $root_key, '[' ) !== false ) {
				$parts		=	explode( '[', $root_key );
				$root_key	=	$parts[0];
				$root_key2	=	substr( $parts[1], 0, -1 );
			}
			if ( $root_key && isset( $response->$root_key ) ) {
				if ( $root_key2 ) {
					if ( isset( $response->$root_key->$root_key2 ) ) {
						$webservice->response	=	$response->$root_key->$root_key2;
					}
				} else {
					$webservice->response	=	$response->$root_key;
				}
			}
		} else {
			$webservice->response	=	$response;
		}

		unset( $ws );
	}

	// _createFile
	protected static function _createFile( $response, $name, $type, $content_type, $allowed, $format )
	{
		if ( $response == '' ) {
			return '';
		}
		if ( $type == 'image' ) {
			switch( $content_type ) {
				case 'image/gif':
					$extension	=	'gif';
					break;
				case 'image/png':
					$extension	=	'png';
					break;
				case 'image/jpeg':
					$extension	=	'jpg';
					break;
				default:
					$extension	=	'';
					break;
			}
		} else {
			switch( $content_type ) {
				case 'application/pdf':
					$extension	=	'pdf';
					break;
				case 'application/octet-stream':
					$extension	=	'zip';
				default:
					$extension	=	'';
					break;
			}	
		}
		if ( !$extension ) {
			return '';
		}
		if ( !isset( $allowed[$extension] ) ) {
			return '';
		}
		$pos		=	strrpos( $name, '/' );

		if ( $pos !== false && $pos > 0 ) {
			$name	=	substr( $name, $pos + 1 );
		}
		if ( !$format ) {
			$extension	=	'';
		} else {
			$extension	=	'.'.$extension;

			if ( base64_decode( $name ) !== false ) {
				$name	=	base64_decode( $name );
			}
			$ext		=	'';
			$pos		=	strrpos( $name, '.' );

			if ( $pos !== false && $pos > 0 ) {
				$ext	=	substr( $name, $pos + 1 );
				$name	=	substr( $name, 0, $pos );
				$pos	=	strrpos( $name, '/' );

				if ( $pos !== false && $pos > 0 ) {
					$name	=	substr( $name, $pos + 1 );
				}
			} else {
				$name	=	'';
			}
			if ( !( $ext && isset( $allowed[$ext] ) && $name != '' ) ) {
				return '';
			}
		}
		$path		=	Factory::getConfig()->get( 'tmp_path' ).'/'.$name.$extension;

		if ( is_file( $path ) ) {
			File::delete( $path );
		}
		if ( File::write( $path, $response ) ) {
			$response	=	$path;
		} else {
			$response	=	'';
		}

		return $response;
	}

	// _setVars
	protected static function _getVars( $params, $format, $fields )
	{
		$vars	=	array();

		foreach ( $params as $param ) {
			$k		=	$param->property;
			$k2	=	'';

			if ( strpos( $k, '.' ) !== false ) {
				$parts	=	explode( '.', $k );
				$k		=	$parts[0];
				$k2		=	$parts[1];
			}
			
			$v	=	'';

			if ( $param->mode == 'field' ) {
				if ( isset( $fields[$param->value] ) ) {
					$v	=	$fields[$param->value]->value;
				}
			} else {
				$v	=	$param->value;
			}

			if ( $format == 'array' || $format == 'json' || $format == 'object' ) {
				if ( $v === 'true' ) {
					$v	=	true;
				} elseif ( $v === 'false' ) {
					$v	=	false;
				}
				if ( $k2 ) {
					if ( !isset( $vars[$k] ) ) {
						$vars[$k]	=	array();
					}

					$vars[$k][$k2]	=	$v;
				} else {
					$vars[$k]	=	$v;
				}
			} elseif ( $format == 'nvp' ) {
				$vars[]		=	$k.'='.urlencode( $v );
			}
		}

		// Set
		if ( $format == 'nvp' ) {
			$vars	=	implode( '&', $vars );
		} elseif ( $format == 'json' ) {
			$vars	=	json_encode( $vars );
		} elseif ( $format == 'object' ) {
			$vars	=	(object)$vars;
		}

		return $vars;
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Stuff

	// getInfoURL
	public static function getInfoURL( $webservice )
	{
		$options	=	new Registry( $webservice->options );
		
		return '';
	}
}
?>