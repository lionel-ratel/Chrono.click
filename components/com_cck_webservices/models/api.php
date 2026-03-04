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

use Joomla\Registry\Registry;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Uri\Uri;

// Model
class CCK_WebservicesModelApi extends BaseDatabaseModel
{
	protected $dev_mode			=	false;
	protected $error_outputs	=	array(
										'bad_request'=>array( 'code'=>400, 'datetime'=>'', 'message'=>'Bad Request', 'status'=>'error' ),
										'duplicate_entry'=>array( 'code'=>403, 'datetime'=>'', 'message'=>'Forbidden: Duplicate Entry %s', 'status'=>'error' ),
										'empty_data'=>array( 'code'=>400, 'datetime'=>'', 'message'=>'Bad Request: Missing Body', 'status'=>'error' ),
										'forbidden'=>array( 'code'=>403, 'datetime'=>'', 'message'=>'Forbidden: %s', 'status'=>'error' ),
										'internal_server_error'=>array( 'code'=>500, 'datetime'=>'', 'message'=>'Internal Server Error => %s', 'status'=>'error' ),
										'invalid_data'=>array( 'code'=>400, 'datetime'=>'', 'message'=>'Bad Request: Invalid Data => %s', 'status'=>'error' ),
										'invalid_format'=>array( 'code'=>400, 'datetime'=>'', 'message'=>'Bad Request: Invalid Format => %s', 'status'=>'error' ),
										'method_not_allowed'=>array( 'code'=>405, 'datetime'=>'', 'message'=>'Method Not Allowed', 'status'=>'error' ),
										'not_found'=>array( 'code'=>404, 'datetime'=>'', 'message'=>'Not Found', 'status'=>'error' ),
										'not_implemented'=>array( 'code'=>501, 'datetime'=>'', 'message'=>'Not Implemented', 'status'=>'error' ),
										'required_property'=>array( 'code'=>400, 'datetime'=>'', 'message'=>'Bad Request: Required Property => %s', 'status'=>'error' ),
										'resource_not_found'=>array( 'code'=>404, 'datetime'=>'', 'message'=>'Not Found: Unknown Resource', 'status'=>'error' ),
										'ssl_required'=>array( 'code'=>403, 'datetime'=>'', 'message'=>'Forbidden: SSL Required', 'status'=>'error' ),
										'unauthorized'=>array( 'code'=>401, 'datetime'=>'', 'message'=>'Unauthorized', 'status'=>'error' ),
										'version_not_found'=>array( 'code'=>404, 'datetime'=>'', 'message'=>'Not Found: Unknown Version', 'status'=>'error' )
									);
	protected $success_outputs	=	array(
										'delete'=>array( 'code'=>200, 'datetime'=>'', 'message'=>'Successfully Deleted', 'status'=>'success' ),
										'patch'=>array( 'code'=>200, 'datetime'=>'', 'message'=>'Successfully Updated', 'status'=>'success' ),
										'post'=>array( 'code'=>201, 'datetime'=>'', 'message'=>'Successfully Created', 'status'=>'success' ),
										'put'=>array( 'code'=>200, 'datetime'=>'', 'message'=>'Successfully Updated', 'status'=>'success' )
									);
	protected $unique_keys		=	array();
	protected $unset_keys		=	array();
	protected $wrapper_modes	=	array(
										'raw'=>0,
										'raw_grouped'=>-2,
										'wrapped'=>1,
										'wrapped_grouped'=>2
									);

	protected static $list_relations	=	null;

	// _getDate
	protected function _getDateFromUrl( $name, $default = '' )
	{
		$str	=	(string)Factory::getApplication()->input->getCmd( $name, $default );

		if ( strpos( $str, 'T' ) !== false && strpos( $str, 'Z' ) !== false ) {
			$parts	=	explode( 'T', $str );
			$str	=	$parts[0].'T'.$parts[1][0].$parts[1][1].':'.$parts[1][2].$parts[1][4].':'.$parts[1][2].$parts[1][5].'Z';
		}

		return $str;
	}

	// auth
	protected function auth( $run_as = 0 )
	{
		Factory::getSession()->set( 'user', Factory::getUser( $run_as ) );
		JCck::getUser( array( $run_as, true ) );
	}

	// authApp
	protected function authApp( $auth_id, $config )
	{
		$app_where	=	'';

		if ( $auth_id ) {
			$app_where	=	' AND a.auth_id = '.(int)$auth_id;
		}

		$my_app	=	JCckDatabase::loadObject( 'SELECT a.id, a.methods, a.name, a.run_as, b.id AS run_as_id'
											. ' FROM #__cck_more_webservices_apps AS a'
											. ' LEFT JOIN #__users AS b ON b.id = a.run_as'
											. ' WHERE a.published = 1'.$app_where
					);

		if ( !is_object( $my_app ) ) {
			return $this->outputError( 'internal_server_error' );
		}

		$my_app		=	$this->prepareResource( $my_app );
		$user_id	=	Factory::getUser()->id;

		if ( !isset( $my_app->methods[$config['method']] ) ) {
			return $this->outputError( 'method_not_allowed' );
		}

		if ( $my_app->run_as ) {
			if ( (int)$my_app->run_as !== (int)$my_app->run_as_id ) {
				return $this->outputError( 'unauthorized' );
			} else {
				if ( $my_app->run_as != $user_id ) {
					$this->auth( $my_app->run_as );
				}	
			}
		} elseif ( $user_id ) {
			$this->auth( 0 );
		}

		return true;
	}

	// doInput
	public function doInput( $name, $raw_data )
	{
		if ( is_string( $raw_data ) ) {
			$raw_data	=	json_decode( $raw_data, true );
		}

		$config	=	array(
						'error'=>false,
						'input_raw'=>$raw_data,
						'limit'=>300, /* TODO: completeConfig */
						'media_type'=>'application/json',
						'method'=>'POST',
						'output'=>JCckWebservice::getConfig_Param( 'resources_output', 'wrapped' ),
						'output_wrapper'=>array( 'properties'=>'attributes', 'response'=>'data' ),
						'resource'=>$name,
						'resource_id'=>'',
						'resource_identifier'=>'',
						'resource_mode'=>'',
						'resource_relation'=>'',
						'resource_relation_id'=>'',
						'sort'=>'',
						'start'=>0,
						'version'=>2
					);

		return $this->prepareData( $this->getResource( $name, $config['method'] ), $config );
	}

	// doInput
	public function doOutput( $name, $override_config = array(), $override_resource = array() )
	{
		$config	=	array(
						'error'=>false,
						'limit'=>300, /* TODO: completeConfig */
						'media_type'=>'',
						'method'=>'GET',
						'output'=>str_replace( 'wrapped', 'raw', JCckWebservice::getConfig_Param( 'resources_output', 'raw' ) ),
						'output_wrapper'=>array( 'properties'=>'attributes', 'response'=>'data' ),
						'resource'=>$name,
						'resource_id'=>'',
						'resource_identifier'=>'',
						'resource_mode'=>'',
						'resource_relation'=>'',
						'resource_relation_id'=>'',
						'sort'=>'',
						'start'=>0,
						'version'=>2
					);

		foreach ( $override_config as $k=>$v ) {
			if ( isset( $config[$k] ) ) {
				$config[$k]	=	$v;
			}
		}

		// "all"
		if ( isset( $override_config['resource_relation_ids'] ) ) {
			$config['resource_relation_ids']	=	$override_config['resource_relation_ids'];
		}

		$base			=	$config['resource_relation'] ? false : true;
		$output			=	$this->prepareData( $this->getResource( $name, $config['method'], $base ), $config );
		$output			=	array(
								'data'=>$output
							);

		return $output;
	}

	// download
	protected function download( $path )
	{
		if ( !( $path && is_file( $path ) ) ) {
			return false;
		}
		
		$ext			=	strtolower( substr ( strrchr( $path, '.' ) , 1 ) );
		$name			=	substr( $path, strrpos( $path, '/' ) + 1, strrpos( $path, '.' ) );
		$to_be_erased	=	false;

		if ( $ext == 'php' || $file == '.htaccess' ) {
			$output	=	$this->outputError( 'not_found' );

			return false;
		}

		set_time_limit( 0 );
		include JPATH_ROOT.'/components/com_cck/download.php';
		
		return true;	
	}

	// getApp
	protected function getApp( $resource_id = 0 )
	{
		$apps	=	(array)JCckDatabase::loadObjectList( 'SELECT a.id, a.auth_id'
													.	' FROM #__cck_more_webservices_apps AS a'
													.	' LEFT JOIN #__cck_more_webservices_app_resources AS b ON (b.id = a.id AND b.id2 = '.(int)$resource_id.')'
													.	' WHERE id2 = '.(int)$resource_id );

		if ( count( $apps ) === 1 ) {
			return $apps[0];
		} else {
			foreach ( $apps as $k=>$app ) {
				if ( $app->auth_id ) {
					unset( $apps[$k] );
				}
			}
			if ( count( $apps ) === 1 ) {
				return current( $apps );
			}
		}

		return false;
	}

	// getContentInstance
	protected function getContentInstance( $content_type )
	{
		$content_instance	=	null;
		$type				=	new JCckType;

		if ( $type->load( $content_type )->isSuccessful() ) {
			$content_object		=	$type->getContentObject();
			$content_instance	=	new $content_object;

			if ( $type->getObject() == 'free' ) {
				if ( ( $parent_type = $type->getProperty( 'parent' ) ) != '' ) {
					$content_instance->setTable( '#__cck_store_form_'.$parent_type );
				} else {
					$content_instance->setTable( '#__cck_store_form_'.$content_type );
				}
			}
/* TEMPORARY */
$content_instance->setOptions( array( 'check_permissions'=>0 ) );
/* TEMPORARY */
		}

		return $content_instance;
	}

	// getData
	public function getData()
	{
		$app	=	Factory::getApplication();
		$v		=	$app->input->getCmd( 'version' );

		// Allows only HTTPS requests
		if ( Uri::getInstance()->getScheme() != 'https' ) {
			if ( JCckWebservice::getConfig_Param( 'resources_http', 1 ) ) {
				return $this->outputError( 'ssl_required' );
			}
		}

		// Init
		if ( $v == 'v0' ) {
			$version		=	-1;
		} else {
			if ( is_file( JPATH_ADMINISTRATOR.'/components/com_cck_webservices/_VERSION.php' ) ) {
				require_once JPATH_ADMINISTRATOR.'/components/com_cck_webservices/_VERSION.php';
				
				$version	=	new JCckWebservicesVersion;
				$version	=	$version->getApiVersion( $v );

				if ( (int)$version ) {
					$version	=	(int)substr( $v, 1 );
				}
			} else {
				$version	=	0;
			}
		}
		
		$config		=	array(
							'error'=>false,
							'limit'=>$app->input->get( 'limit', '' ),
							'media_type'=>( isset( $app->client->headers['Content-Type'] ) ? $app->client->headers['Content-Type'] : '' ),
							'method'=>$app->input->getMethod(),
							'output'=>JCckWebservice::getConfig_Param( 'resources_output', 'wrapped' ),
							'output_wrapper'=>array( 'properties'=>'attributes', 'response'=>'data' ),
							'resource'=>(string)$app->input->getCmd( 'resource' ),
							'resource_id'=>$app->input->getString( 'id', '' ),
							'resource_identifier'=>'',
							'resource_mode'=>'',
							'resource_relation'=>(string)$app->input->getCmd( 'relation' ),
							'resource_relation_id'=>$app->input->getString( 'relation_id', '' ),
							'sort'=>(string)$app->input->getCmd( 'sort', '' ),
							'start'=>(int)$app->input->getInt( 'start', 0 ),
							'version'=>$version
						);
		$resource	=	new stdClass;

		if ( (int)$version > -1 ) {
			// Check if version exists
			if ( !$config['version'] ) {
				return $this->outputError( 'version_not_found' );
			}

			// Check if resource given
			if ( !$config['resource'] ) {
				return $this->outputError( 'resource_not_found' );
			}

			if ( $config['resource_relation']
			  && ( $config['resource_relation_id'] || ( !$config['resource_relation_id'] && $config['method'] != 'GET' ) ) ) {
				$resource	=	$this->getResource( $config['resource_relation'], $config['method'], false );
				
				if ( $resource->type == 'relationship' ) {
					$resource	=	$this->updateResource( $resource, $config['resource'], 'GET' );
				}
			} else {
				$resource	=	$this->getResource( $config['resource'], $config['method'] );
			}

			// Check if resource exists
			if ( !is_object( $resource ) ) {
				return $this->outputError( 'resource_not_found' );
			}
			
			// Check if method is allowed
			if ( !isset( $resource->methods[$config['method']] ) ) {
				return $this->outputError( 'method_not_allowed' );
			}
		} else {
			$resource->type	=	'';
		}

		// Check Auth
		if ( $app_auth = $this->getApp( $resource->id ) ) { // Do we have a single App Id that says which auth to use?
			if ( $app_auth->auth_id ) {
				if ( !( $this->isAuth( $app_auth->auth_id ) > 0 ) ) {
					return $this->outputError( 'unauthorized' );
				}
				if ( ( $res = $this->authApp( $app_auth->auth_id, $config ) ) !== true ) {
					return $res;
				}
			}
		} else {
			if ( !( $auth_id = $this->isAuth() ) ) {
				return $this->outputError( 'unauthorized' );
			} elseif ( $auth_id > -1 ) {
				if ( ( $res = $this->authApp( $auth_id, $config ) ) !== true ) {
					return $res;
				}
			}
		}

		// Clean
		$config['resource_id']			=	(string)preg_replace( '/[^A-Z0-9\:\-\_\@\.\+]/i', '', $config['resource_id'] );
		$config['resource_relation_id']	=	(string)preg_replace( '/[^A-Z0-9\:\-\_\@\.\+]/i', '', $config['resource_relation_id'] );

		// Set
		return $this->prepareData( $resource, $config );
	}

	// getHash
	protected function getHash( $str )
	{
		return $str;
	}

	// getInput
	protected function getInput( $config )
	{
		$input	=	file_get_contents( 'php://input' );

		switch ( $config['media_type'] ) {
			case 'application/json':
				$input	=	json_decode( $input, true );
				break;
			case 'application/x-www-form-urlencoded':
				$input	=	urldecode( $input );

				if ( $config['property'] != '' ) {
					$pos	=	strpos( $input, $config['property'].'=' );

					if ( $pos !== false && $pos == 0 ) {
						$input	=	substr( $input, strlen( $config['property'].'=' ) );
						$input	=	json_decode( $input, true );
					} else {
						return false;
					}
				} else {
					$input	=	JCckDevHelper::getUrlVars( $input, true, false );
				}
				break;
			default:
				break;
		}

		return $input;
	}

	// getInputFilters
	protected function getInputFilters( $config )
	{
		$filters	=	explode( '|', $config['filter'] );
		$input		=	array();

		foreach ( $filters as $filter ) {
			if ( strpos( $filter, '=' ) !== false ) {
				$parts	=	explode( '=', $filter );

				$k		=	$parts[0];
				$v		=	$parts[1];
				
				if ( $k == '' ) {
					return false;
				}

				$input[$k]	=	$v;
			}
		}

		return $input;
	}

	// getLinkToSelf
	protected function getLinkToSelf( $config, $id, $object = false )
	{
		if ( $object ) {
			$link		=	new stdClass;
			$link->href	=	$config['uri_base'].'/'.$id;
			$link->rel	=	'self';
		} else {
			$link		=	$config['uri_base'].'/'.$id;	
		}
		
		return $link;
	}

	// getStorageProperty
	public function getStorageProperty( $property, $name, $config )
	{
		if ( isset( $config['input_fields'][$name] ) ) {
			return $config['input_fields'][$name]->$property;
		}

		return '';
	}

	// getResource
	public function getResource( $name, $method = '', $base = true )
	{
		$query		=	'SELECT id, title, name, methods, options, type'
					.	' FROM #__cck_more_webservices_resources'
					.	' WHERE published = 1 AND name = "'.$name.'"';
	
		if ( $base ) {
			$query	.=	' AND type != "relationship"';
		} else {
			$query	.=	' AND type = "relationship"';
		}

		$resources	=	JCckDatabase::loadObjectList( $query );

		if ( is_array( $resources ) ) {
			if ( count( $resources ) == 1 ) {
				return $this->prepareResource( $resources[0] );
			} elseif ( isset( $resources[0] ) ) {
				foreach ( $resources as $resource ) {
					if ( strpos( $resource->methods, $method ) !== false ) {
						return $this->prepareResource( $resource );
					}
				}
				
				return $this->prepareResource( $resources[0] );
			}
			
		}

		return null;
	}

	// isAuth
	protected function isAuth( $auth_id = 0 )
	{
		$auth_where			=	$auth_id ? 'id = '.(int)$auth_id : 'featured = 1';
		$authentications	=	JCckDatabase::loadObjectList( 'SELECT id, type, options FROM #__cck_more_webservices_auths WHERE '.$auth_where.' AND published = 1' );

		if ( !count( $authentications ) ) {
			return -1;
		}

		$app		=	Factory::getApplication();

		foreach ( $authentications as $auth ) {
			$options	=	json_decode( $auth->options, true );

			switch ( $auth->type ) {
				case 'api_key':
					if ( $options['mode'] ) {
						if ( $options['key'] != '' && $options['value'] != ''
						  && $app->input->getString( $options['key'] ) == $options['value'] ) {
						  	$this->unset_keys[]	=	$options['key'];
							return (int)$auth->id;
						}
					} else {
						if ( $options['key'] != '' && $options['value'] != '' ) {
							if ( !isset( $app->client->headers[$options['key']] ) ) {
								$options['key']	=	ucwords( $options['key'], '-' );
							}
							if ( isset( $app->client->headers[$options['key']] ) && $app->client->headers[$options['key']] == $options['value'] ) {
								return (int)$auth->id;
							}
						}
					}

					break;
				case 'basic_auth':
					$http_auth	=	$app->input->server->get( 'HTTP_AUTHORIZATION', '', 'string' );

					if ( $options['username'] != '' && $options['password'] != ''
					  && 'Basic '.base64_encode( $options['username'].':'.$options['password'] ) == $http_auth ) {
						return (int)$auth->id;
					}

					break;
				case 'token_auth':
					$http_auth	=	$app->input->server->get( 'HTTP_AUTHORIZATION', '', 'string' );

					if ( $options['token'] != ''
					  && 'Bearer '.$options['token'] == $http_auth ) {
						return (int)$auth->id;
					}

					break;
				default:
					break;
			}
		}

		return 0;
	}

	// loadItem
	public function loadItem( &$content_instance, $config, $property = 'content' )
	{
		if ( $config['resource_identifier'] ) {
			if ( !$content_instance->findOne( $config[$property.'_type'], array( $config['resource_identifier']=>$config['resource_id'] ) )->isSuccessful() ) {
				if ( $config[$property.'_types'] ) {
					if ( strpos( $config[$property.'_types'], ',' ) !== false ) { /* May be better to load/retrieve from #__cck_core */
						$c_types	=	explode( ',', $config[$property.'_types'] );

						foreach ( $c_types as $c_type ) {
							if ( $content_instance->findOne( $config[$property.'_types'], array( $config['resource_identifier']=>$config['resource_id'] ) )->isSuccessful() ) {
								break;
							}
						}
					} else {
						$content_instance->findOne( $config[$property.'_types'], array( $config['resource_identifier']=>$config['resource_id'] ) );
					}
				}
			}
		} else {
			$content_instance->load( (int)$config['resource_id'] );
		}
	}

	// outputError
	protected function outputError( $type = '', $string = '' )
	{
		if ( !( $type && isset( $this->error_outputs[$type] ) ) ) {
			$type	=	'not_found';
		}

		$output				=	$this->error_outputs[$type];
		$output['datetime']	=	Factory::getDate()->format( 'Y-m-d\TH:i:s\Z' );

		if ( strpos( $output['message'], '%s' ) !== false ) {
			$output['message']	=	trim( str_replace( '%s', $string, $output['message'] ) );
			$output['message']	=	trim( $output['message'], ':' );
		}

		return $output;
	}

	// outputSuccess
	protected function outputSuccess( $task = '' )
	{
		$output	=	array(
					  'code'=>200,
					  'datetime'=>Factory::getDate()->format( 'Y-m-d\TH:i:s\Z' ),
					  'message'=>'Successfully Processed',
					  'status'=>'success'
					);


		return $output;
	}

	// prepareData
	protected function prepareData( $resource, &$config )
	{
		if ( $resource === null ) {
			return $this->outputError();
		}
		if ( (int)$config['version'] == -1 ) {
			return json_decode( file_get_contents( 'php://input' ), true );
		}

		$app	=	Factory::getApplication();
		$output	=	array();
		$user	=	Factory::getUser();

		// Pre-Init
		$config['output']			=	$this->wrapper_modes[$config['output']];
		$config['resource_mode']	=	$resource->type; /* TODO: resource_type, after resource_type is changed to uri_type */
		$config['standalone']		=	false;

		// Init
		switch ( $resource->type ) {
			case 'content_type':
			case 'content_type_standalone':
			case 'relationship':
				if ( $resource->type === 'content_type_standalone' ) {
					$config['content_table']	=	$resource->options->get( 'content_table' );
					$config['standalone']		=	true;
					$resource->type				= 'content_type';
				}

				$config['content_type']			=	$resource->options->get( 'content_type' );
				$config['content_types']		=	$resource->options->get( 'content_types', '' );

				$this->prepareDataInput( 'input', $resource, $config );

				$config['input_required']		=	true;
				$config['links']				=	$resource->options->get( 'hateoas', JCckWebservice::getConfig_Param( 'resources_links', 0 ) );
				$config['links_pagination']		=	$resource->options->get( 'hateoas_pagination', JCckWebservice::getConfig_Param( 'resources_pagination', 0 ) );
				$config['location']				=	$resource->options->get( 'storage_location' );
				$config['output_fields']		=	array();
				$config['output_keys']			=	$resource->options->get( 'output_keys', '' );
				$config['output_keys']			=	$config['output_keys'] !== '' ? explode( ',', $config['output_keys'] ) : array();
				$config['output_keys_attr']		=	true;
				$config['output_keys_count']	=	count( $config['output_keys'] );

				if ( count( $config['output_keys'] ) ) {
					if ( in_array( 'attributes', $config['output_keys'] ) ) {
						unset( $config['output_keys'][0] );
					} else {
						$config['output_keys_attr']	=	false;
					}
				}

				$config['output_properties']	=	(array)$resource->options->get( 'output' );

				if ( $resource->type == 'relationship' && !( $config['method'] == 'GET' || $config['method'] == 'POST' ) ) {
					$this->prepareDataInput( 'parent', $resource, $config );
				}

				$config['prepare']		=	JCckWebservice::getConfig_Param( 'resources_prepare_data', 0 );
				break;
			case 'download':
				if ( $config['method'] != 'GET' ) {
					return $this->outputError( 'bad_request' );
				} else {
					$config['resource_id']	=	$app->input->getBase64( 'id', '' );

					$this->processGET_download( $config, $output );

					return $output;
				}
				break;
			case 'field':
				$config['referrer']		=	$resource->options->get( 'field_name' );
				break;
			case 'processing':
				$config['processing']	=	$resource->options->get( 'processing' );
				break;
			default:
				return $this->outputError();
				break;
		}

		if ( $config['method'] != 'POST' ) {
			if ( !$this->updateIdentifier( 'resource_id', $config ) ) {
				return $this->outputError( 'bad_request' );
			}
		}

		// Init (2)
		$this->setUri( $config );

		if ( (int)$resource->options->get( 'debug', '0' ) ) {
			$this->dev_mode	=	true;
		}
		if ( $this->dev_mode !== true ) {
			ob_start();
		}
		switch ( $config['method'] ) {
			case 'DELETE':
				// Check
				if ( !$config['resource_id'] ) {
					return $this->outputError( 'bad_request' );
				}

				// Process
				if ( $config['resource_relation'] ) {
					if ( $config['resource_relation_id'] ) {
						$config['method']			=	'UNLINK';
						$config['set_message']		=	'Successfully Withdrawn';
					} else {
						$config['set_message']		=	'Successfully Emptied';
					}

					$this->processRELATE( $resource->type, $config, $output );
				} else {
					$this->processDELETE( $resource->type, $config, $output );
				}
				break;
			case 'GET':
				// Check
				if ( $resource->type == 'relationship' && $config['resource_id'] ) {
					return $this->outputError( 'not_implemented' );
				}

				// Init (3)
				if ( isset( $resource->options ) ) {
					$is_null	=	false;

					if ( $config['limit'] == '' ) {
						$config['limit']	=	(int)$resource->options->get( 'limit', JCckWebservice::getConfig_Param( 'resources_limit', 10 ) );
					} elseif ( !$config['limit'] ) {
						$is_null	=	true;
					}
					$limit_max		=	(int)$resource->options->get( 'limit_max', JCckWebservice::getConfig_Param( 'resources_limit_max', 10 ) );
					
					if ( $config['limit'] > $limit_max && ( $is_null || !$is_null && $limit_max ) ) {
						$config['limit']	=	$limit_max;
					} elseif ( (int)$config['limit'] === 0 ) {
						$config['limit']	=	$limit_max;
					}

					$config['group_by']	=	(int)$resource->options->get( 'group_by', '' );
				} else {
					$config['group_by']	=	0;
					$config['limit']	=	(int)$config['limit'];
				}
				if ( JCckWebservice::getConfig_Param( 'resources_filtering', '0' ) == '1' ) {
					$config['filter']	=	(string)$app->input->getString( 'filter', '' );
					$config['filter']	=	substr( $config['filter'], 1, -1 );
				} elseif ( JCckWebservice::getConfig_Param( 'resources_filtering', '0' ) == '2' ) {
					$this->setUri( $config, 'query' );

					$config['filter']	=	str_replace( '&', '|', $config['uri_query'] );
				} else {
					unset( $config['filter'] );
				}
				if ( JCckWebservice::getConfig_Param( 'resources_filters', '' ) != '' ) {
					$config['filter']	.=	'|'.JCckWebservice::getConfig_Param( 'resources_filters', '' );
				}
				if ( isset( $resource->options ) && !$config['sort'] ) {
					$config['sort']		=	$resource->options->get( 'ordering', JCckWebservice::getConfig_Param( 'resources_ordering', 'alpha' ) );
					$config['sort_by']	=	$resource->options->get( 'order_by', '' );
				}

				// Process
				$this->processGET( $resource->type, $config, $output, $user );
				break;
			case 'LINK':
				// Check
				if ( !( $config['resource_id'] && $config['resource_relation'] && $config['resource_relation_id'] ) ) {
					return $this->outputError( 'bad_request' );
				}

				// Init (3)
				$config['property']			=	$resource->options->get( 'property', '' );
				$config['set_message']		=	'Successfully Assigned';

				// Process
				$this->processRELATE( $resource->type, $config, $output );
				break;
			case 'POST':
				// Check
				$config['resource_id']	=	(int)$config['resource_id'];

				if ( $config['resource_id'] ) {
					return $this->outputError( 'bad_request' );
				}

				// Init (3)
				$config['property']			=	$resource->options->get( 'property', '' );
				$config['set_code']			=	201;
				$config['set_message']		=	'Successfully Created';

				// Process
				$this->processSET( $resource->type, $config, $output );
				break;
			case 'PATCH':
				// Check
				if ( !$config['resource_id'] ) {
					return $this->outputError( 'bad_request' );
				}

				// Init (3)
				$config['input_required']	=	false;
				$config['property']			=	$resource->options->get( 'property', '' );
				$config['set_code']			=	200;
				$config['set_message']		=	'Successfully Updated';

				// Process
				$this->processSET( $resource->type, $config, $output );
				break;
			case 'PUT':
				// Check
				if ( !$config['resource_id'] ) {
					return $this->outputError( 'bad_request' );
				}

				// Init (3)
				$config['property']			=	$resource->options->get( 'property', '' );
				$config['set_code']			=	200;
				$config['set_message']		=	'Successfully Updated';

				// Process
				if ( $config['resource_relation'] ) {
					if ( !$config['resource_relation_id'] ) {
						return $this->outputError( 'bad_request' );
					}

					$config['method']			=	'LINK';
					$config['set_message']		=	'Successfully Assigned';

					$this->processRELATE( $resource->type, $config, $output );
				} else {
					$this->processSET( $resource->type, $config, $output );
				}
				break;
			case 'UNLINK':
				// Check
				if ( !( $config['resource_id'] && $config['resource_relation'] && $config['resource_relation_id'] ) ) {
					return $this->outputError( 'bad_request' );
				}

				// Init (3)
				$config['set_message']		=	'Successfully Withdrawn';

				// Process
				$this->processRELATE( $resource->type, $config, $output );
				break;
			default:
				return;
				break;
		}
		if ( $this->dev_mode !== true ) {
			ob_get_clean();
		}

		if ( is_array( $output ) && !empty( $output ) ) {
			return $output;
		} else {
			return $this->outputError();
		}
	}

	// prepareDataInput
	protected function prepareDataInput( $property, $resource, &$config )
	{
		$config[$property.'_fields']		=	array();
		$config[$property.'_properties']	=	(array)$resource->options->get( $property );

		if ( count( $config[$property.'_properties'] ) ) {
			$names		=	implode( '","', array_keys( $config[$property.'_properties'] ) );
			$select		=	'title, name, type, options, options2, storage, storage_table, storage_field, storage_field2';

			if ( JCck::is( '4.0' ) ) {
				$select	.=	', storage_key , storage_mode';
			}

			$config[$property.'_fields']	=	JCckDatabaseCache::loadObjectList( 'SELECT '.$select
																				 . ' FROM #__cck_core_fields'
																				 . ' WHERE name IN ("'.$names.'")'
																				 , 'name' );
		}

		if ( $property == 'parent' ) {
			$config['parent_type']	=	$resource->options->get( 'parent_type' );
			$config['parent_types']	=	$resource->options->get( 'parent_types', '' );
		}
	}

	// prepareInput
	protected function prepareInput( $input, $config, &$output, $final = true )
	{
		static $processed	=	0;

		$data	=	array();

		if ( count( $config['input_properties'] ) ) {
			foreach ( $config['input_properties'] as $k_name=>$params ) {
				if ( !( $k = $this->getStorageProperty( 'storage_field', $k_name, $config ) ) ) {
					$output	=	$this->outputError( 'internal_server_error', $k_name );

					return false;
				}

				// Input
				$k2			=	isset( $params->property ) && $params->property ? $params->property : $k;
				$input_type	=	'';

				if ( isset( $params->type ) ) {
					if ( $params->type == 'unset' ) {
						continue;
					} else {
						$input_type	=	$params->type;

						if ( isset( $input[$k2] ) ) {
							if ( !$final ) {
								$k			=	$k2;
							}

							switch( $input_type ) {
								case 'raw':
									$data[$k]	=	$input[$k2];
									break;
								case 'string':
								default:
									$data[$k]	=	(string)$input[$k2];
									break;
							}
						}
					}
				}
				
				// Value
				if ( !isset( $params->value_mode ) ) {
					$params->value_mode	=	0;
				} else {
					$params->value_mode	=	(int)$params->value_mode;
				}
				if ( !$params->value_mode ) {
					if ( isset( $params->value ) && (string)$params->value != '' ) {
						$data[$k]	=	(string)$params->value;
					}
				} elseif ( !isset( $data[$k] ) ) {
					if ( isset( $params->value ) && (string)$params->value != '' ) {
						$data[$k]	=	(string)$params->value;
					}
				}

				// Required
				if ( isset( $params->required ) && $params->required ) {
					$error	=	false;

					if ( isset( $data[$k] ) ) {
						if ( (string)$data[$k] != '' ) {
							// OK
						} else {
							$error	=	true;
						}
					} elseif ( $config['input_required'] ) {
						$error	=	true;
					}

					if ( $error && $k2 != $config['resource_identifier'] ) {
						$output	=	$this->outputError( 'required_property', $k2 );

						return false;
					} elseif ( (int)$params->required == -1 ) {
						$this->unique_keys[]	=	$k2;
					}
				}
			}
		}

		return $data;	
	}

	// prepareInputFilters
	protected function prepareInputFilters( $input, &$config, &$output, $final = true )
	{
		$data	=	array();

		foreach ( $input as $k=>$v ) {
			if ( !isset( $config['filters'][$k] ) ) {
				$output	=	$this->outputError( 'bad_request' );

				return false;
			}
			if ( (string)$v == '' ) {
				if ( isset( $config['filters_required'][$k] ) ) {
					$output	=	$this->outputError( 'required_property', $k );

					return false;
				}
				continue;
			} else {
				unset( $config['query_parts'][$k] );
			}

			$data[$k]	=	$v;
		}

		return $data;
	}

	// prepareResource
	protected function prepareResource( $resource )
	{
		$resource->methods	=	explode( ',', $resource->methods );
		$resource->methods	=	array_flip( $resource->methods );

		if ( isset( $resource->options ) ) {
			$resource->options	=	new Registry( $resource->options );
		} else {
			$resource->options	=	new Registry;
		}

		return $resource;
	}

	// preProcess
	protected function preProcess( $config, &$output, &$data, $method )
	{
		$processing	=	array();

		if ( JCckToolbox::getConfig()->get( 'processing', 0 ) ) {
			$processing =	JCckDatabaseCache::loadObjectListArray( 'SELECT type, scriptfile, options FROM #__cck_more_processings WHERE published = 1 AND type IN ("onCckResourceInput") ORDER BY ordering', 'type' );
		}

		$event	=	'onCckResourceInput';

		if ( isset( $processing[$event] ) ) {
			if ( $method != '' ) {
				$data	=	$this->$method( $data, $config, $output, false );
			}

			if ( $data === false ) {
				return false;
			}

			foreach ( $processing[$event] as $p ) {
				$process	=	new JCckProcessing( $event, JPATH_SITE.$p->scriptfile, $p->options, true );
				$result		=	call_user_func_array( array( $process, 'execute' ), array( &$config, &$data ) );

				if ( !$result ) {
					if ( isset( $config['error_output'] ) ) {
						$output	=	$config['error_output'];
					} elseif ( is_array( $config['error'] ) ) {
						$k		=	key( $config['error'] );
						$output	=	$this->outputError( $k, $config['error'][$k] );
					}

					return false;
				}
			}
		}

		return true;
	}
	
	// postProcess
	protected function postProcess( $config, &$item )
	{
		$processing	=	array();

		if ( JCckToolbox::getConfig()->get( 'processing', 0 ) ) {
			$processing =	JCckDatabaseCache::loadObjectListArray( 'SELECT type, scriptfile, options FROM #__cck_more_processings WHERE published = 1 AND type IN ("onCckResourceOutput") ORDER BY ordering', 'type' );
		}

		$event	=	'onCckResourceOutput';

		if ( isset( $processing[$event] ) ) {
			foreach ( $processing[$event] as $p ) {
				$process	=	new JCckProcessing( $event, JPATH_SITE.$p->scriptfile, $p->options, true );
				$result		=	call_user_func_array( array( $process, 'execute' ), array( &$config, &$item ) );

				if ( !$result ) {
					if ( isset( $config['error_output'] ) ) {
						$output	=	$config['error_output'];
					} elseif ( is_array( $config['error'] ) ) {
						$k		=	key( $config['error'] );
						$output	=	$this->outputError( $k, $config['error'][$k] );
					}

					return false;
				}
			}
		}

		return true;
	}

	// process
	protected function process( &$config, &$output, $input = array() )
	{
		if ( $config['version'] == 1 ) {
			$data	=	$input;
		}

		$error			=	false;
		$processing		=	JCckDatabase::loadObject( 'SELECT id, scriptfile, options FROM #__cck_more_processings WHERE published = 1 AND id ='.(int)$config['processing'] );
		
		if ( is_object( $processing ) && $processing->scriptfile != '' && is_file( JPATH_SITE.$processing->scriptfile ) ) {
			$options	=	new Registry( $processing->options );

			include JPATH_SITE.$processing->scriptfile;
		}

		return $error;
	}
	
	// processDELETE
	protected function processDELETE( $type, $config, &$output )
	{
		if ( $type == 'processing' ) {
			return $this->processDELETE_process( $config, $output );
		} elseif ( !$config['content_type'] ) {
			return;
		}

		// Process
		$content_type	=	new JCckType;

		if ( $content_type->load( $config['content_type'] )->isSuccessful() ) {
			$content_object		=	$content_type->getContentObject();
			$content_instance	=	new $content_object;

			if ( $content_type->getObject() == 'free' ) {
				if ( ( $parent_type = $content_type->getProperty( 'parent' ) ) != '' ) {
					$content_instance->setTable( '#__cck_store_form_'.$parent_type );
				} else {
					$content_instance->setTable( '#__cck_store_form_'.$config['content_type'] );
				}
			}
/* TEMPORARY */
$content_instance->setOptions( array( 'check_permissions'=>0 ) );
/* TEMPORARY */
			$this->loadItem( $content_instance, $config );

			if ( $content_instance->isSuccessful() ) {
				if ( !$content_instance->can( 'delete' ) ) {
					$output	=	$this->outputError( 'unauthorized' );

					return;
				}

				$data	=	array();

				if ( $this->preProcess( $config, $output, $data, '' ) ) {
					if ( $content_instance->delete() ) {
						$output	=	array(
									  'code'=>200,
									  'datetime'=>Factory::getDate()->format( 'Y-m-d\TH:i:s\Z' ),
									  'message'=>'Successfully Deleted',
									  'status'=>'success'
									);
					} else {
						$output	=	$this->outputError( 'forbidden' );
					}
				} else {
					return -1;
				}
			}
		}
	}

	// processDELETE_process
	protected function processDELETE_process( $config, &$output )
	{
		$error	=	$this->process( $config, $output );

		if ( $error ) {
			if ( is_string( $error ) ) {
				$output	=	$this->outputError( $error );
			}
		} else {
			$output	=	$this->outputSuccess( 'delete' );
		}
	}

	// processGET
	protected function processGET( $type, &$config, &$output, $user )
	{
		$app	=	Factory::getApplication();

		$config['after']	=	$this->_getDateFromUrl( 'after', '' );
		$config['before']	=	$this->_getDateFromUrl( 'before', '' );
		$config['since']	=	$this->_getDateFromUrl( 'since', '' );
		$config['until']	=	$this->_getDateFromUrl( 'until', '' );

		if ( $this->dev_mode === true ) {
			JCckDev::aa( $config, 'GET /query '.Factory::getDate()->format( 'H:i:s' ) );
		}

		if ( $type == 'processing' ) {
			return $this->processGET_process( $config, $output );
		} elseif ( $type == 'download' ) {
			return $this->processGET_download( $config, $output );
		} elseif ( $type == 'field' ) {
			return $this->processGET_field( $config, $output );
		} else {
			$config['db_group']			=	false;
			$config['db_prefix']		=	Factory::getConfig()->get( 'dbprefix' );
			$config['filters_allowed']	=	array();
			$config['filters_required']	=	array();

			// Set 'resource_type'
			if ( $config['resource_id'] != '' && $config['resource_id'] != 'all' ) {
				$config['resource_type']	=	$config['resource_relation'] ? 'relationship' : 'document';
			} else {
				$config['resource_type']	=	'collection';
			}

			$db		=	Factory::getDbo();
			$query	=	$db->getQuery( true );

			if ( count( $config['output_properties'] ) ) {
				$attributes		=	(string)$app->input->getString( 'fields', '' );
				$names			=	implode( '","', array_keys( $config['output_properties'] ) );
				$prepare_output	=	false;
				$select			=	'title, name, type, options, options2, storage, storage_crypt, storage_table, storage_field, storage_field2';

				if ( JCck::is( '4.0' ) ) {
					$select	.=	', storage_mode';
				}

				$config['output_fields']	=	JCckDatabaseCache::loadObjectList( 'SELECT '.$select
																				 . ' FROM #__cck_core_fields'
																				 . ' WHERE name IN ("'.$names.'")'
																				 . ' ORDER BY FIELD(name,"'.$names.'")'
																				 , 'storage_field' );

				if ( $attributes ) {
					if ( $attributes[0] == '-' ) {
						$attributes		=	substr( $attributes, 2, -1 );
						$attributes_do	=	-1;
					} else {
						$attributes		=	substr( $attributes, 1, -1 );
						$attributes_do	=	1;
					}

					$attributes	=	explode( ',', $attributes );
					$attributes	=	array_flip( $attributes );

					if ( count( $attributes ) ) {
						$config['output_attributes']		=	$attributes;
						$config['output_attributes']['_']	=	$attributes_do;
					} else {
						$config['output_attributes']	=	null;
					}
				} else {
					$config['output_attributes']	=	null;
				}

				foreach ( $config['output_fields'] as $property_name=>$field ) {
					if ( isset( $config['output_properties'][$field->name] ) ) {
						if ( $config['output_properties'][$field->name]->view && $config['resource_type'] == 'collection' ) {
							if ( (int)$config['output_properties'][$field->name]->view == 2 && $config['resource_id'] == 'all' ) {
								// Bypass OK
							} else {
								unset( $config['output_fields'][$property_name] );

								continue;
							}
						} elseif ( (int)$config['output_properties'][$field->name]->view == 2 && $config['resource_type'] == 'document' ) {
							unset( $config['output_fields'][$property_name] );

							continue;
						}

						$field->resource_property	=	$config['output_properties'][$field->name]->property ? $config['output_properties'][$field->name]->property : $property_name;

						if ( $config['output_attributes'] !== null ) {
							if ( $config['output_attributes']['_'] === 1 ) {
								if ( !isset( $attributes[$field->resource_property] ) ) {
									unset( $config['output_fields'][$property_name] );
								}
							} elseif ( $config['output_attributes']['_'] === -1 ) {
								if ( isset( $attributes[$field->resource_property] ) ) {
									unset( $config['output_fields'][$property_name] );
								}
							}
						}

						$field->resource_output		=	isset( $config['output_properties'][$field->name]->output ) && $config['output_properties'][$field->name]->output != '' ? $config['output_properties'][$field->name]->output : $config['prepare'];

						if ( $field->resource_output ) {
							$prepare_output	=	true;
						}
					} else {
						unset( $config['output_fields'][$property_name] );
					}
				}
				if ( $prepare_output ) {
					PluginHelper::importPlugin( 'cck_field' );
				}
			}

			if ( $this->processGET_queryList( $query, $db, $config, false, $output ) === false ) {
				if ( !$this->processGET_query( $config, $output, $db, $query, $user ) ) {
					return;
				}
			}

			$trim	=	false;
			$total	=	count( $output );

			if ( !$total ) {
				return;
			}

			// Memorize this one
			$resource_rel	=	$config['resource_relation'];

			// Prepare
			if ( count( $config['output_fields'] ) ) {
				PluginHelper::importPlugin( 'cck_storage' );

				$config['app']	=	new JCckApp;
				$config['app']->loadDefault();

				$config['ids']	=	array();
				$config['pks']	=	array();

				foreach ( $output as $k=>$v ) {
					$config['ids'][]	=	$v->cck_id;
					$config['pks'][]	=	$v->id;
				}

				$config['ids']	=	implode( ',', $config['ids'] );
				$config['pks']	=	implode( ',', $config['pks'] );

				foreach ( $output as $k=>$v ) {
					$config['id']	=	$v->cck_id;
					$config['pk']	=	$v->id;
					$config['type']	=	$v->cck;

					$data	=	array();

					if ( abs( $config['output'] ) == 2 ) {
						if ( $config['output_keys_attr'] ) {
							$v->attributes	=	new stdClass;
						}
						foreach ( $config['output_keys'] as $output_key ) {
							if ( isset( $v->$output_key ) ) {
								$data[$output_key]	=	$v->$output_key;
							}

							unset( $v->$output_key );

							$v->$output_key	=	'';
						}

						if ( !$config['output_keys_attr'] ) {
							$v->attributes	=	new stdClass;
						}

						if ( !$config['output_keys_count'] ) {
							unset( $v->id );
						}
					}
					if ( $config['links'] == -1 ) {
						if ( !$config['output_keys_count'] ) {
							unset( $v->id );
						}

						$v->href	=	$this->getLinkToSelf( $config, $config['pk'], false );
					}

					$v->id		=	$config['pk'];

					unset( $v->cck_id );

					foreach ( $config['output_fields'] as $name=>$field ) {
						$property		=	$field->resource_property;
						$value			=	'';

						if ( !( $field->storage == 'none' && $field->storage == '' ) ) {
							if ( isset( $data[$name] ) ) {
								$value	=	$data[$name];
							} elseif ( isset( $v->$name ) ) {
								$value	=	$v->$name;
							}

							if ( JCck::is( '4.0' ) ) {
								if ( $field->storage == 'standard' && $field->storage_mode ) {
									$field->storage	=	'json';
								}	
							}

							$app->triggerEvent( 'onCCK_StoragePrepareResource', array( &$field, &$value, &$config ) );

							if ( !isset( $data[$name] )  ) {
								unset( $v->$name );
							}
						}

						if ( $field->resource_output ) {
							$config['resource_relation']	=	$resource_rel;

							$app->triggerEvent( 'onCCK_FieldPrepareResource', array( &$field, $value, &$config ) );

							$value	=	$field->data;
						} 

						if ( abs( $config['output'] ) == 2 ) {
							if ( isset( $data[$name] ) ) {
								$v->$property				=	$value;
							} else {
								$v->attributes->$property	=	$value;
							}
						} else {
							$v->$property				=	$value;
						}
					}

					unset( $v->cck );

					if ( !$this->postProcess( $config, $output[$k] ) ) {
						$trim	=	true;

						unset( $output[$k] );
						
						continue;
					}

					if ( $config['links'] == 1 ) {
						$v->links	=	array();
						$v->links[]	=	$this->getLinkToSelf( $config, $v->id, true );
					}
				}

				unset( $config['pk'] );
			} elseif ( $config['links'] ) {
				foreach ( $output as $k=>$v ) {
					if ( $config['links'] == 1 ) {
						$v->links	=	array();
						$v->links[]	=	$this->getLinkToSelf( $config, $v->id, true );
					}
				}
			}

			// Set
			if ( $trim ) {
				$output	=	array_values( $output );
			}
			if ( $config['output'] > 0 ) {
				$output	=	array(
							  'code'=>200,
							  'datetime'=>Factory::getDate()->format( 'Y-m-d\TH:i:s\Z' ),
							  'status'=>'success',
							  'data'=>$output
							);

				if ( $config['resource_type'] == 'collection' ) {
					if ( $config['links_pagination'] ) {
						$query->clear( 'select' )
							  ->clear( 'order' )
							  ->clear( 'limit' );

						if ( $config['db_group'] ) {
							$query->clear( 'group' )
								  ->select( 'COUNT(DISTINCT('.( $config['standalone'] ? 't1' : 't0' ).'.id))' );
						} else {
							$query->select( 'COUNT('.( $config['standalone'] ? 't1' : 't0' ).'.id)' );
						}
						
						$db->setQuery( $query );
						
						$output['links']	=	$this->processGET_paginate( $config, (int)$db->loadResult() );
					}

					$output['total']	=	$total;
				} else {
					if ( $config['version'] > 1 ) {
						$output['data']	=	current( $output['data'] );
					}
				}
			}
		}
	}

	// processGET_paginate
	protected function processGET_paginate( $config, $count )
	{
		$this->setUri( $config, 'query' );

		$links 			=	array();

		if ( $config['uri_query'] != '' ) {
			$d1			=	'?';
			$d2			=	'&';
		} else {
			$d1			=	'';
			$d2			=	'?';
		}

		$link			=	new stdClass;
		$link->href		=	$config['uri_base'].$d1.$config['uri_query'];
		$link->rel		=	'first';

		$this->setLink( $link, $links, $config );
		
		$link			=	new stdClass;
		$start			=	$config['start'] - $config['limit'];
		$start			=	$start > 0 ? $d2.'start='.$start : '';
		$link->href		=	( $config['start'] ) ? $config['uri_base'].$d1.$config['uri_query'].$start : '';
		$link->rel		=	'previous';
		
		$this->setLink( $link, $links, $config );
		
		$link			=	new stdClass;		
		$start			=	$config['start'] + $config['limit'];
		$start			=	( ( $start > 0 ) && ( $start < $count ) ) ? $d2.'start='.$start : '';
		$link->href		=	$start != '' ? $config['uri_base'].$d1.$config['uri_query'].$start : '';

		$query_diff		=	$config['uri_query'] ? '?'.$config['uri_query'] : '';

		if ( $link->href == $config['uri_base'].$query_diff ) {
			$link->href	=	'';
		}
		$link->rel		=	'next';
		
		$this->setLink( $link, $links, $config );
		
		$link			=	new stdClass;
		$start			=	0;

		if ( $config['limit'] ) {
			$start		=	( $count % $config['limit'] ) ? $count - ( $count % $config['limit'] ) : $count - ( $count % $config['limit'] ) - $config['limit'];
		}

		$start			=	$start > 0 ? $d2.'start='.$start : '';
		$link->href		=	$config['uri_base'].$d1.$config['uri_query'].$start;
		$link->rel		=	'last';
		
		$this->setLink( $link, $links, $config );
		
		return $links;
	}

	// processGET_download
	protected function processGET_download( $config, &$output )
	{
		$output	=	array(
					  'code'=>200,
					  'datetime'=>Factory::getDate()->format( 'Y-m-d\TH:i:s\Z' ),
					  'status'=>'success'
					);
		$path	=	'';

		// Prepare
		ob_start();

		if ( $config['resource_id'] ) {
			$identifier	=	base64_decode( $config['resource_id'] );
			$identifier	=	JCckDevHelper::getUrlVars( $identifier, false, false );

			if ( $identifier['file'] && $identifier['id'] ) {
				$return	=	JCckDevHelper::getDownloadInfo( (int)$identifier['id'], $identifier['file'] );

				if ( $return === false || isset( $return['error'] ) && $return['error'] ) {
					$output	=	$this->outputError( 'not_found' );
				}

				if ( isset( $return['file'] ) && $return['file'] ) {
					$path	=	JPATH_ROOT.'/'.$return['file'];
				}
			}

			// $path	=	JPATH_ROOT.'/tmp/plop.pdf';
		}

		ob_end_clean();

		if ( !$this->download( $path ) ) {
			$output	=	$this->outputError( 'not_found' );
		}
	}

	// processGET_field
	protected function processGET_field( $config, &$output )
	{
		$error	=	true;
		
		if ( $config['referrer'] ) {
			$content_field	=	new JCckField2;

			if ( $content_field->load( $config['referrer'] )->isSuccessful() ) {
				$content_field->prepare( 'form', array( 'selectlabel'=>'', 'variation'=>'default' ) );

				$filter_value	=	'';

				if ( $config['filter'] ) {
					$filter_parts	=	explode( '=', $config['filter'] );

					if ( $filter_parts[0] == 'value' ) {
						$filter_value	=	$filter_parts[1];
					}
				}

				$output		=	array();
				$optionsList	=	$content_field->getProperty( 'optionsList' );

				if ( $optionsList ) {
					$error	=	false;
					$opts	=	explode( '||', $optionsList );

					foreach ( $opts as $opt ) {
						$parts		=	explode( '=', $opt );
						$value		=	isset( $parts[1] ) ? $parts[1] : $parts[0] ;

						if ( $filter_value == '' || ( $filter_value != '' && $filter_value == $value ) ) {
							$output[]	=	array(
												'text'=>$parts[0],
												'value'=>$value
											);
						}
					}
				}

				if ( $filter_value != '' && count( $output ) === 0 ) {
					$error	=	'not_found';
				}
			}
		}
		
		if ( $error ) {
			if ( is_string( $error ) ) {
				$output	=	$this->outputError( $error );
			}
		} else {
			if ( !empty( $output ) && $config['output'] > 0 ) {
				$output	=	array(
							  'code'=>200,
							  'datetime'=>Factory::getDate()->format( 'Y-m-d\TH:i:s\Z' ),
							  'status'=>'success',
							  'data'=>$output
							);
			} elseif ( empty( $output ) ) {
				$output	=	array(
							  'code'=>200,
							  'datetime'=>Factory::getDate()->format( 'Y-m-d\TH:i:s\Z' ),
							  'status'=>'success'
							);
			}
		}
	}

	// processGET_process
	protected function processGET_process( $config, &$output )
	{
		$error	=	$this->process( $config, $output );

		if ( $error ) {
			if ( is_string( $error ) ) {
				$output	=	$this->outputError( $error );
			}
		} else {
			if ( !empty( $output ) && $config['output'] > 0 ) {
				$output	=	array(
							  'code'=>200,
							  'datetime'=>Factory::getDate()->format( 'Y-m-d\TH:i:s\Z' ),
							  'status'=>'success',
							  'data'=>$output
							);
			} elseif ( empty( $output ) ) {
				$output	=	array(
							  'code'=>200,
							  'datetime'=>Factory::getDate()->format( 'Y-m-d\TH:i:s\Z' ),
							  'status'=>'success'
							);
			}
		}
	}

	// processGET_query
	protected function processGET_query( &$config, &$output, $db, $query, $user )
	{
		PluginHelper::importPlugin( 'cck_storage_location' );

		$error			=	false;
		$inherit		=	array();
		$properties		=	array( 'created_at', 'key', 'modified_at', 'ordering', 'table' );
		$properties		=	JCck::callFunc( 'plgCCK_Storage_Location'.$config['location'], 'getStaticProperties', $properties );
		$t				=	1;
		
		if ( $config['standalone'] )  {
			$properties['table']	=	$config['content_table'];
			$tables					=	array(
											$properties['table']=>array( '_'=>'t1', 'id'=>true )
										);

			// Prepare
			$query->select( 't1.'.$properties['key'] )
				  ->select( '"'.$config['content_type'].'" AS cck' )
				  ->select( 't1.'.$properties['key'].' AS cck_id' )
				  ->from( $properties['table'].' AS t1' );
		} else {
			if ( !$properties['table'] ) {
				$properties['table']	=	'#__cck_store_form_'.$config['content_type'];
			}
			$tables		=	array(
								'#__cck_core'=>array( '_'=>'t0', 'cck'=>true )
							);

			// Prepare
			$query->select( 't1.'.$properties['key'] )
				  ->select( 't0.cck' )
				  ->select( 't0.id AS cck_id' )
				  ->from( '#__cck_core AS t0' )
				  ->join( 'LEFT', $properties['table'].' AS t1 ON ( t1.'.$properties['key'].' = t0.pk AND storage_location = "'.$config['location'].'" )' );
		}

		// Set relathionship
		if ( $config['resource_mode'] == 'relationship' && $config['resource_relation'] && $config['resource_relation_id'] ) {
			$this->processGET_queryWhereRelation( $query, $config, $t, $tables, true );

			if ( $config['group_by'] ) {
				$query->group( 't1.'.$properties['key'] );
			}
		} elseif ( $config['resource_type'] == 'collection' && $config['resource_id'] == 'all' ) {
			if ( $this->processGET_queryWhereRelation( $query, $config, $t, $tables ) ) {
				$config['db_group']	=	true;

				$query->group( 't1.'.$properties['key'] );
			}
		}

		if ( count( $config['output_fields'] ) ) {
			$tables[$properties['table']]	=	array( '_'=>'t1', $properties['key']=>true );

			foreach ( $config['output_fields'] as $field ) {
				if ( !( $field->storage != 'none' && $field->storage_table ) ) {
					continue;
				}
				if ( !isset( $tables[$field->storage_table] ) ) {
					$t++;
					
					$query->join( 'LEFT', $field->storage_table.' AS t'.$t.' ON t'.$t.'.'.( isset( $field->storage_key ) && $field->storage_key ? $field->storage_key : 'id' ).' = t1.'.$properties['key'] );

					$tables[$field->storage_table]			=	array( '_'=>'t'.$t );
				}
				if ( !isset( $tables[$field->storage_table][$field->storage_field] ) ) {
					$query->select( $tables[$field->storage_table]['_'].'.'.$field->storage_field.'' );

					$tables[$field->storage_table][$field->storage_field]	=	true;
				}
			}
		}

		// Prepare (Filtering)
		if ( count( $tables ) ) {
			foreach ( $tables as $k=>$table ) {
				$tables[$k]['columns']	=	JCckDatabaseCache::getTableColumns( $k, true );
			}
		}

		if ( !$config['standalone'] && $config['content_type'] != '' ) {
			if ( $config['content_types'] != '' ) {
				$query->where( 't0.cck IN ("'.str_replace( ',', '","', $config['content_type'].','.$config['content_types'] ).'")' );
			} else {
				$query->where( 't0.cck = "'.$config['content_type'].'"' );
			}
		}

		if ( $config['resource_id'] != '' && $config['resource_id'] != 'all' ) {
			if ( $config['resource_identifier'] ) {
				$filters	=	array(
									$config['resource_identifier']=>$config['resource_id']
								);
				$where		=	array();

				$this->preProcess( $config, $output, $filters, '' );

				if ( $filters[$config['resource_identifier']] != '' ) {
					$config['resource_id']	=	$filters[$config['resource_identifier']];
				}

				$this->processGET_queryWhere( $query, $where, $config['resource_identifier'], $config['resource_id'], $config, $t, $tables );

				if ( isset( $where[0] ) ) {
					$query->where( $where[0] );
				} else {
					$output	=	$this->outputError( 'bad_request' );

					return false;
				}
			} else {
				$query->where( 't1.'.$properties['key'].' = "'.(int)$config['resource_id'].'"' );
			}			
		} else {
			// Filtering
			$config['query_parts']	=	array();
			$range_parts			=	array(
											'after'=>'>',
											'before'=>'<',
											'since'=>'>=',
											'until'=>'<='
										);

			if ( count( $config['input_properties'] ) ) {
				foreach ( $config['input_properties'] as $k_name=>$params ) {
					if ( !( $k = $this->getStorageProperty( 'storage_field', $k_name, $config ) ) ) {
						$output	=	$this->outputError( 'internal_server_error', $k_name );

						return false;
					}

					$has_value	=	false;
					$k2			=	$k;
					$t2			=	$this->getStorageProperty( 'storage_table', $k_name, $config );
					$t2_key		=	$this->getStorageProperty( 'storage_key', $k_name, $config );

					if ( !isset( $params->value_mode ) ) {
						$params->value_mode	=	0;
					} else {
						$params->value_mode	=	(int)$params->value_mode;
					}

					if ( isset( $params->value ) && (string)$params->value != '' ) {
						$has_value			=	true;
						$k_value			=	(string)$params->value;

						if ( !$params->value_mode ) {
							$this->processGET_queryWhereDo( $query, $k, $k_value, $config, $t, $tables, $t2, $t2_key );
						} else {
							$config['query_parts'][$k]	=	array(
																'table_key'=>$t2_key,
																'table_name'=>$t2,
																'value'=>$k_value
															);
						}
					}
					if ( !$has_value || ( $has_value && $params->value_mode ) ) {
						if ( isset( $params->property ) && (string)$params->property != '' ) {
							$k2	=	$params->property;

							if ( isset( $range_parts[$k2] ) ) {
								if ( (string)$config[$k2] != '' ) {
									$this->processGET_queryWhereDo( $query, $k, $config[$k2], $config, $t, $tables, '', '', $range_parts[$k2] );

									$config[$k2]	=	'';
								}
							} else {
								if ( !isset( $config['filters'][$k2] ) ) {
									$config['filters'][$k2]	=	array();
								}

								$config['filters'][$k2][]		=	$k;
							}
						} else {
							$config['filters'][$k]	=	array( 0=>$k );
						}
					}

					if ( isset( $params->required ) ) {
						if ( $params->required == -1 ) {
							continue;
						} elseif ( $params->required ) {
							$config['filters_required'][$k2]	=	'';
						}
					}
				}
			}
			if ( $config['filter'] ) {
				$filters	=	$this->getInputFilters( $config );

				if ( $filters === false || $filters === null ) {
					$output	=	$this->outputError( 'bad_request' );

					return false;
				}

				if ( count( $filters ) ) {
					if ( $this->preProcess( $config, $output, $filters, 'prepareInputFilters' ) ) {
						$filters	=	$this->prepareInputFilters( $filters, $config, $output );
						
						if ( $filters === false ) {
							return false;
						}

						foreach ( $filters as $key=>$val ) {
							$where	=	array();

							// Prepare Where
							foreach ( $config['filters'][$key] as $column ) {
								$this->processGET_queryWhere( $query, $where, $column, $val, $config, $t, $tables );
							}

							// Set Where
							if ( count( $where ) > 1 ) {
								$query->where( '((' . implode( ') OR (', $where ) . '))' );
							} elseif ( isset( $where[0] ) ) {
								$query->where( $where[0] );
							}
						}
					} else {
						return false;
					}
				}
			} elseif ( count( $config['filters_required'] ) ) {
				$output	=	$this->outputError( 'required_property', key( $config['filters_required'] ) );
				
				return false;
			}
			if ( count( $config['query_parts'] ) ) {
				foreach ( $config['query_parts'] as $k=>$k_parts ) {
					$this->processGET_queryWhereDo( $query, $k, $k_parts['value'], $config, $t, $tables, $k_parts['table_name'], $k_parts['table_key'] );
				}
			}

			$this->processGET_queryWhereRange( $query, $config, $properties );
		}

		if ( $error ) {
			$output	=	$this->outputError( 'bad_request' );

			return false;
		}

		Factory::getApplication()->triggerEvent( 'onCCK_Storage_LocationPrepareSearch', array( $config['location'], &$query, &$tables, &$t, &$config, &$inherit, $user ) );

		// Prepare (Ordering)
		if ( $config['sort'] != '' ) {
			if ( isset( $properties['ordering'][$config['sort']] ) ) {
				$query->order( 't1.'.$properties['ordering'][$config['sort']] );
			} elseif ( $config['sort'] === 'pk_asc' || $config['sort'] === 'pk_desc' ) {
				$query->order( 't1.'.$properties['key'].' '.str_replace( 'pk_', '', $config['sort'] ) );
			} elseif ( $config['sort'] == '-1' ) {
				$query->order( $config['sort_by'] );
			} elseif ( $config['sort'] == 'random' ) {
				$query->order( $query->Rand() ); /* OK for 1000- records */
			} elseif ( $config['sort'] != 'none' ) {
				if ( $config['sort'][0] == '-' ) {
					$dir			=	'DESC';
					$config['sort']	=	substr( $config['sort'], 1 );
				} else {
					$dir			=	'ASC';
				}
				$query->order( 't1.'.$config['sort'].' '.$dir );
			}
		}

		$output	=	 $this->processGET_queryList( $query, $db, $config, true, $output );

		if ( $this->dev_mode === true ) {
			JCckDev::aa( (string)$query, 'GET /query '.Factory::getDate()->format( 'H:i:s' ) );
		}

		return true;
	}

	// processGET_queryList
	protected function processGET_queryList( &$query, &$db, &$config, $do_query, &$output )
	{
		if ( isset( $config['resource_relation_ids'] ) ) {
			static $list_relations	=	null;

			if ( $do_query ) {
				if ( !isset( $list_relations[$config['resource']] ) ) {
					$query->select( 't2.id AS pk' );

					$db->setQuery( $query );

					$list_relations[$config['resource']]	=	array();

					foreach ( (array)$db->loadObjectList() as $item ) {
						$pk	=	$item->pk;

						if ( !isset( $list_relations[$config['resource']][$pk] ) ) {
							$list_relations[$config['resource']][$pk]	=	array();
						}

						unset( $item->pk );

						$list_relations[$config['resource']][$pk][]	=	$item;
					}
				}

				return isset( $list_relations[$config['resource']][$config['resource_relation_id']] ) ? $list_relations[$config['resource']][$config['resource_relation_id']] : array();
			} else {
				$output	=	isset( $list_relations[$config['resource']][$config['resource_relation_id']] ) ? $list_relations[$config['resource']][$config['resource_relation_id']] : array();

				return false;
			}
		} elseif ( !$do_query ) {
			return false;
		} else {
			$db->setQuery( $query, $config['start'], $config['limit'] );

			return $db->loadObjectList();
		}
	}

	// processGET_queryWhere
	protected function processGET_queryWhere( &$query, &$where, $column, $value, $config, &$t_idx, &$tables, $table_name = '', $table_key = '', $match = '' )
	{
		if ( $table_name ) {
			if ( !isset( $tables[$table_name] ) ) {
				$t_idx++;

				$query->join( 'LEFT', $table_name.' AS t'.$t_idx.' ON t'.$t_idx.'.'.( $table_key ? $table_key : 'id' ).' = t1.id' );

				$tables[$table_name]	=	array( '_'=>'t'.$t_idx );
			}

			$tables[$table_name]['fields'][$column]	=	true;

			$this->processGET_queryWhereColumn( $tables[$table_name], $where, $column, $value, $match );
		} else {
			foreach ( $tables as $k=>$table ) {
				$t	=	str_replace( $config['db_prefix'], '#__', $k );

				if ( isset( $table['columns'][$column] )/* && !isset( $tables[$t]['fields'][$column] ) */) {
					$this->processGET_queryWhereColumn( $table, $where, $column, $value, $match );

					$tables[$t]['fields'][$column]	=	true;

					break;
				}
			}
		}
	}

	// processGET_queryWhereDo
	protected function processGET_queryWhereDo( &$query, $column, $value, $config, &$t_idx, &$tables, $table_name = '', $table_key = '', $match = '' )
	{
		$w	=	array();

		$this->processGET_queryWhere( $query, $w, $column, $value, $config, $t_idx, $tables, $table_name, $table_key, $match );

		// Set Where
		if ( isset( $w[0] ) ) {
			$query->where( $w[0] );
		}
	}

	// processGET_queryWhereColumn
	protected function processGET_queryWhereColumn( $table, &$where, $column, $value, $match = '' )
	{
		if ( strpos( $value, ',' ) !== false ) {
			$parts	=	explode( ',', $value );
			$values	=	array();

			if ( count( $parts ) ) {
				foreach ( $parts as $p ) {
					if ( $p != '' ) {
						if ( !is_numeric( $p ) ) {
							$p	=	JCckDatabase::quote( $p );
						}
						$values[]	=	$p;
					}
				}
				if ( count( $values ) ) {
					$where[]	=	$table['_'].'.'.$column.' IN ('.implode( ',', $values ).')';
				}
			}
		} else {
			$sql	=	$this->processGET_queryWhereMatch( $value, $match );

			if ( $sql != '' ) {
				$where[]	=	$table['_'].'.'.$column.$sql;
			}
		}
	}

	// processGET_queryWhereMatch
	protected function processGET_queryWhereMatch( $string, $match = '' )
	{
		$end	=	strlen( $string ) - 1;
		$sql	=	'';

		if ( $match != '' ) {
			if ( $match == '>' || $match == '<=' ) {
				if ( preg_match( '/^\d{4}[-](0?[1-9]|1[012])[-](0?[1-9]|[12][0-9]|3[01])$/', $string ) ) {
					$string	.=	'T23:59:59Z';
				}
			}
			$value	=	$string;

			if ( !is_numeric( $value ) ) {
				$value	=	JCckDatabase::quote( $value );
			}
			$sql	=	' '.$match.' '.$value;
		} else {
			if ( $string[0] == '*' && $string[$end] == '*' ) {
				$value	=	substr( $string, 1, -1 );
				$sql	=	' LIKE '.JCckDatabase::quote( '%'.JCckDatabase::escape( $value, true ).'%', false );
			} elseif ( $string[0] == '*' ) {
				$value	=	substr( $string, 1 );
				
				if ( $value !== '' ) {
					$sql	=	' LIKE '.JCckDatabase::quote( '%'.JCckDatabase::escape( $value, true ), false );
				}
			} elseif ( $string[$end] == '*' ) {
				$value	=	substr( $string, 0, -1 );
				$sql	=	' LIKE '.JCckDatabase::quote( JCckDatabase::escape( $value, true ).'%', false );
			} else {
				$value	=	$string;

				if ( !is_numeric( $value ) ) {
					$value	=	JCckDatabase::quote( $value );
				}
				$sql	=	' = '.$value;
			}
		}

		return $sql;
	}

	// processGET_queryWhereRange
	protected function processGET_queryWhereRange( &$query, $config, $properties )
	{
		$after_before	=	array();
		$since_until	=	array();
		$where_ranges	=	array();

		// After Before
		if ( $config['after'] != '' ) {
			$config['after']	=	Factory::getDate( $config['after'] );

			// --
			if ( 1 == 1 ) { /* > */
				$config['after']->modify( '+1 day' )->modify( '-1 second' );
			} 
			$config['after']	=	$config['after']->toSql();
			// --

			$after_before[]		=	't1.'.$properties['created_at'].' > "'.$config['after'].'"';
		}
		if ( $config['before'] != '' ) {
			$config['before']	=	Factory::getDate( $config['before'] )->toSql();
			$after_before[]		=	't1.'.$properties['created_at'].' < "'.$config['before'].'"';
		}

		// Since Until
		if ( $config['since'] != '' ) {
			$config['since']	=	Factory::getDate( $config['since'] )->toSql();
			$after_before[]		=	't1.'.$properties['created_at'].' >= "'.$config['since'].'"';
			$since_until[]		=	't1.'.$properties['modified_at'].' >= "'.$config['since'].'"';
			
		}
		if ( $config['until'] != '' ) {
			$config['until']	=	Factory::getDate( $config['until'] );

			// --
			if ( 1 == 1 ) { /* <= */
				$config['until']->modify( '+1 day' )->modify( '-1 second' );
			} 
			$config['until']	=	$config['until']->toSql();
			// --

			$after_before[]		=	't1.'.$properties['created_at'].' <= "'.$config['until'].'"';
			$since_until[]		=	'( t1.'.$properties['modified_at'].' <= "'.$config['until'].'" AND t1.'.$properties['modified_at'].' > "0000-00-00:00:00:00" )';
		}

		if ( count( $after_before ) ) {
			$where_ranges[]	=	implode( ' AND ', $after_before );
		}
		if ( count( $since_until ) ) {
			$where_ranges[]	=	implode( ' AND ', $since_until );
			$query->where( '((' . implode( ') OR (', $where_ranges ) . '))' );	
		} else {
			foreach ( $where_ranges as $where_range ) {
				$query->where( $where_range );
			}	
		}
	}

	// processGET_queryWhereRelation
	protected function processGET_queryWhereRelation( &$query, $config, &$t_idx, &$tables, $mode = false )
	{
		if ( $mode ) {
			$parent_resource	=	$this->getResource( $config['resource_relation'], 'GET' ); /* TODO: we may want to disable additional preparation from prepareResource */
			$type				=	'';

			if ( is_object( $parent_resource ) ) {
				$query_key		=	'id2';

				if ( isset( $config['resource_relation_ids'] ) ) {
					$query_where	=	' IN ('.$config['resource_relation_ids'].')';
				} else {
					if ( (int)$config['resource_relation_id'] ) {
						$query_on_and	=	' AND t'.( $t_idx + 1 ).'.id = '.(int)$config['resource_relation_id'];
					}

					$query_where	=	' = '.(int)$config['resource_relation_id'];
				}

				$resource		=	$config['resource'];
				$type			=	$parent_resource->options->get( 'content_type', '' );
			}
		} else {
			$query_key		=	'id';
			$query_on_and	=	'';
			$query_where	=	' IS NOT NULL';
			$resource		=	$config['resource_relation'];
			$type			=	$config['content_type'];
		}
		
		$content_type	=	new JCckType;

		if ( $type && $content_type->load( $type )->isSuccessful() ) {
			$ref_relation	=	$content_type->getRelationship( $resource );

			if ( is_object( $ref_relation ) && $ref_relation->params->table ) {
				$t_idx++;

				$query->join( 'LEFT', $ref_relation->params->table.' AS t'.$t_idx.' ON (t'.$t_idx.'.'.$query_key.' = t1.id'.$query_on_and.')' )
					  ->where( 't'.$t_idx.'.id'.$query_where );

				$tables[$ref_relation->params->table]	=	array( '_'=>'t'.$t_idx );

				return true;
			}
		}

		return false;
	}

	// processRELATE
	protected function processRELATE( $type, $config, &$output )
	{
		if ( $type == 'processing' ) {
			return false; /* TODO */
		} elseif ( !$config['content_type'] ) {
			return;
		}

		// Process
		$content_instance	=	$this->getContentInstance( $config['parent_type'] );

		if ( is_null( $content_instance ) ) {
			return;
		}

		$this->loadItem( $content_instance, $config, 'parent' );

		if ( $content_instance->isSuccessful() ) {
			if ( !$content_instance->can( 'save' ) ) {
				$output	=	$this->outputError( 'unauthorized' );

				return;
			}

			$res	=	false;

			$content_instance->relate( $config['resource_relation'] );

			if ( $config['method'] == 'DELETE' ) {
				$res	=	$content_instance->untieAll();
			} elseif ( $config['method'] == 'LINK' ) {
				$data	=	$this->getInput( $config );

				// -- Temporary (TBD)
				if ( strpos( $config['resource_relation_id'], ':' ) !== false ) {
					$data_parts	=	explode( ':', $config['resource_relation_id'] );

					if ( $data_parts[0] && $data_parts[1] ) {
						$data_k	=	$data_parts[0];

						if ( !isset( $data[$data_k] ) ) {
							if ( !is_array( $data ) ) {
								$data	=	array();
							}

							$data[$data_k]	=	$data_parts[1];
						}
					}
				}
				// -- Temporary (TBD)

				if ( $this->preProcess( $config, $output, $data, 'prepareInput' ) ) {
					$data	=	$this->prepareInput( $data, $config, $output );

					if ( $data === false ) {
						if ( empty( $output ) ) {
							$output	=	$this->outputError( 'bad_request' );
						}

						return;
					} elseif ( empty( $data ) ) {
						$data	=	array();
					}

					if ( !$this->updateIdentifier( 'resource_relation_id', $config, 'input', true ) ) {
						$output	=	$this->outputError( 'bad_request' );

						return;
					}

					$res	=	$content_instance->tie( $config['resource_relation_id'], $data );
				}				
			} elseif ( $config['method'] == 'UNLINK' ) {
				if ( !$this->updateIdentifier( 'resource_relation_id', $config, 'input', true ) ) {
					$output	=	$this->outputError( 'bad_request' );

					return;
				}

				$res	=	$content_instance->untie( $config['resource_relation_id'] );
			}

			if ( $res ) {
				$output	=	array(
							  'code'=>200,
							  'datetime'=>Factory::getDate()->format( 'Y-m-d\TH:i:s\Z' ),
							  'message'=>$config['set_message'],
							  'status'=>'success'
							);
			} else {
				if ( empty( $output ) ) {
					$output	=	$this->outputError( 'bad_request' );
				}
			}
		}
	}

	// processSET
	protected function processSET( $type, &$config, &$output )
	{
		if ( isset( $config['input_raw'] ) ) {
			$input	=	$config['input_raw'];
		} else {
			$input	=	$this->getInput( $config );
		}
		if ( $input === false || $input === null ) {
			$output	=	$this->outputError( 'bad_request' );

			return;
		}

		if ( $type == 'processing' ) {
			return $this->processSET_process( $config, $output, $input );
		} elseif ( !$config['content_type'] ) {
			return;
		}

		// Process
		$content_type	=	new JCckType;
		$error			=	false;
		$pk				=	0;

		if ( $content_type->load( $config['content_type'] )->isSuccessful() ) {
			$content_object		=	$content_type->getContentObject();
			$content_instance	=	new $content_object;

			if ( $content_type->getObject() == 'free' ) {
				if ( ( $parent_type = $content_type->getProperty( 'parent' ) ) != '' ) {
					$content_instance->setTable( '#__cck_store_form_'.$parent_type );
				} else {
					$content_instance->setTable( '#__cck_store_form_'.$config['content_type'] );
				}
			}
/* TEMPORARY */
$content_instance->setOptions( array( 'check_permissions'=>0/*, 'trigger_events'=>0*/ ) );
/* TEMPORARY */

			if ( $config['method'] == 'POST' ) {
				$pk		=	$this->processSET_create( $content_instance, $config, $output, $input );
			} else {
				$pk		=	$this->processSET_update( $content_instance, $config, $output, $input );
			}

			if ( !$pk ) {
				$error	=	true;
			} elseif ( $pk == -1 ) {
				if ( !empty( $output ) ) {
					return;
				} else {
					$error	=	true;
				}
			}
		}

		// Return
		if ( $error	!== false ) {
			$output	=	$this->outputError( 'bad_request' );

			return;
		}

		// Output
		if ( $config['links'] == -1 ) {
			$data	=	array(
							'href'=>$this->getLinkToSelf( $config, $pk ),
							'id'=>(string)$pk
						);
		} elseif ( $config['links'] ) {
			$data	=	array(
							'id'=>(string)$pk,
							'links'=>array( $this->getLinkToSelf( $config, $pk, true ) )
						);
		} else {
			$data	=	array( 'id'=>(string)$pk );
		}

		$output	=	array(
					  'code'=>$config['set_code'],
					  'datetime'=>Factory::getDate()->format( 'Y-m-d\TH:i:s\Z' ),
					  'message'=>$config['set_message'],
					  'status'=>'success',
					  'data'=>$data
					);
	}

	// processSET_create
	protected function processSET_create( $content_instance, &$config, &$output, $data )
	{
		if ( !$content_instance->can( 'create' ) ) {
			$output	=	$this->outputError( 'unauthorized' );

			return -1;
		}

		if ( $this->preProcess( $config, $output, $data, 'prepareInput' ) ) {
			$data	=	$this->prepareInput( $data, $config, $output );
			
			if ( $data === false ) {
				return -1;
			} elseif ( empty( $data ) ) {
				$output	=	$this->outputError( 'empty_data' );

				return -1;
			}
			try {
				$app	=	Factory::getApplication();
				
				$app->input->set( 'cck_is_api', 1 );

				if ( $content_instance->create( $config['content_type'], $data )->isSuccessful() ) {
					return $content_instance->getPk();
				} else {
					$logs	=	$content_instance->getLog();

					if ( isset( $logs['error' ] ) ) {
						$log	=	current( $logs['error' ] );
						$k		=	key( $log );

						if ( is_array( $log ) ) {
							$output	=	$this->outputError( $k , $log[$k] );

							return -1;
						}
					}
				}

				$app->input->set( 'cck_is_api', 0 );
			} catch ( Exception $e ) {
				if ( $e->getCode() == 1062 ) {
					$keys	=	count( $this->unique_keys ) ? '=> '.implode( '|', $this->unique_keys ) : '';
					$output	=	$this->outputError( 'duplicate_entry', $keys );

					return -1;
				}
			}
		} else {
			return -1;
		}

		return 0;
	}

	// processSET_process
	protected function processSET_process( &$config, &$output, $input )
	{
		$config_app	=	$config;

		unset( $config['output'] );

		$error		=	$this->process( $config, $output, $input );

		if ( isset( $config['output'] ) ) {
			$config_app['output']	=	$config['output'];
		}

		$config		=	$config_app;
		
		if ( $error ) {
			if ( is_string( $error ) ) {
				$output	=	$this->outputError( $error );
			}
		} else {
			if ( !empty( $output ) ) {
				if ( $config['output'] > 0 ) {
					$output	=	array(
								  'code'=>200,
								  'datetime'=>Factory::getDate()->format( 'Y-m-d\TH:i:s\Z' ),
								  'message'=>'Successfully Processed',
								  'status'=>'success',
								  'data'=>$output
								);	
				}
			} elseif ( $config['method'] == 'POST' ) {
				$output	=	$this->outputSuccess( 'create' );
			} else {
				$output	=	$this->outputSuccess( 'update' );
			}
		}
	}

	// processSET_update
	protected function processSET_update( $content_instance, &$config, &$output, $data )
	{
		$this->loadItem( $content_instance, $config );

		if ( !$content_instance->isSuccessful() ) {
			$output	=	$this->outputError( 'not_found' );

			return -1;
		}

		if ( !$content_instance->can( 'save' ) ) {
			$output	=	$this->outputError( 'unauthorized' );

			return -1;
		}

		if ( $this->preProcess( $config, $output, $data, 'prepareInput' ) ) {
			$data	=	$this->prepareInput( $data, $config, $output );

			if ( $data === false ) {
				return -1;
			} elseif ( empty( $data ) ) {
				$output	=	$this->outputError( 'empty_data' );

				return -1;
			}
			try {
				if ( $config['method'] == 'PUT' ) {
					$app	=	Factory::getApplication();
					
					$app->input->set( 'cck_is_api', 1 );

					if ( $content_instance->update( $data ) ) {
						return $content_instance->getPk();
					} else {
						$logs	=	$content_instance->getLog();

						if ( isset( $logs['error' ] ) ) {
							$log	=	current( $logs['error' ] );
							$k		=	key( $log );

							if ( is_array( $log ) ) {
								$output	=	$this->outputError( $k , $log[$k] );

								return -1;
							}
						}
					}

					$app->input->set( 'cck_is_api', 0 );
				} else {
					foreach ( $data as $k=>$v ) {
						$content_instance->setProperty( $k, $v );
					}
					if ( $content_instance->store() ) {
						return $content_instance->getPk();
					}
				}
			} catch ( Exception $e ) {
				if ( $e->getCode() == 1062 ) {
					$keys	=	count( $this->unique_keys ) ? '=> '.implode( '|', $this->unique_keys ) : '';
					$output	=	$this->outputError( 'duplicate_entry' );

					return -1;
				}
			}
		} else {
			return -1;
		}

		return 0;
	}

	// setLink
	protected function setLink( $link, &$links, $config )
	{
		if ( $config['links_pagination'] == -1 ) {
			$links[$link->rel]	=	$link->href;
		} else {
			$links[]	=	$link;
		}
	}

	// setUri
	protected function setUri( &$config, $property = '' )
	{
		$uri	=	Uri::getInstance();

		if ( $property == 'query' ) {
			if ( isset( $config['uri_query'] ) ) {
				return;
			}

			$vars	=	$uri->getQuery( true );

			unset( $vars['_'] );

			foreach ( $this->unset_keys as $unset_key ) {
				unset( $unset_key );
			}
			
			$config['uri_query']	=	'?'.urldecode( http_build_query( $vars, '', '&' ) );
			$config['uri_query']	=	str_replace( ( ( strpos( $config['uri_query'], '?start=' ) !== false ) ? '?start=' : '&start=' ).$config['start'], '', $config['uri_query'] );
			$config['uri_query']	=	( $config['uri_query'] != '' ) ? substr( $config['uri_query'], 1 ) : '';
		} else {
			$config['uri_base']		=	$uri->toString( array( 'scheme', 'host', 'path' ) );
			$config['uri_root']		=	substr( $config['uri_base'], 0, strpos( $config['uri_base'], '/v'.$config['version'] ) + 2 + strlen( $config['version'] ) );

			if ( $config['resource_relation'] ) {
				$config['uri_base']	=	substr( $config['uri_base'], 0, ( strlen( $config['resource_relation'] ) + 1 ) * -1 );
			}
			if ( $config['resource_id'] ) {
				$config['uri_base']	=	substr( $config['uri_base'], 0, ( strlen( $config['resource_id'] ) + 1 ) * -1 );
			}

			$config['uri_path']		=	$uri->getPath();
		}
	}

	// updateIdentifier
	protected function updateIdentifier( $property, &$config, $property2 = '', $update = false )
	{
		if ( strpos( $config[$property], ':' ) !== false ) {
			$error	=	true;
			$parts	=	explode( ':', $config[$property] );

			if ( $parts[0] && $parts[1] ) {
				$k			=	$parts[0];

				if ( !$property2 ) {
					$property2	=	isset( $config['parent_fields'] ) ? 'parent' : 'input';
				}

				foreach ( $config[$property2.'_fields'] as $name=>$field ) {
					if ( !( isset( $config[$property2.'_properties'][$name]->required ) && (int)$config[$property2.'_properties'][$name]->required == -1 ) ) {
						continue;
					}
					if ( isset( $config[$property2.'_properties'][$name]->property ) && $config[$property2.'_properties'][$name]->property != '' ) {
						if ( $config[$property2.'_properties'][$name]->property == $parts[0] ) {
							$error	=	false;
							$k		=	$field->storage_field;
						}
					} elseif ( $field->storage_field == $k ) {
						$error	=	false;
					}
				}

				$config['resource_identifier']	=	$k;
				$config[$property]				=	$parts[1];
			}

			if ( $error ) {
				return false;
			} elseif ( $update ) {
				$content_instance	=	$this->getContentInstance( $config['content_type'] );

				if ( !( $pk = $content_instance->findPk( $config['content_type'], array( $config['resource_identifier']=>$config[$property] ) ) ) ) {
					if ( $config['content_types'] ) {
						if ( strpos( $config['content_types'], ',' ) !== false ) { /* May be better to load/retrieve from #__cck_core */
							$c_types	=	explode( ',', $config['content_types'] );

							foreach ( $c_types as $c_type ) {
								if ( ( $pk = $content_instance->findPk( $c_type, array( $config['resource_identifier']=>$config[$property] ) ) ) ) {
									break;
								}
							}
						} else {
							$pk	=	$content_instance->findPk( $config['content_types'], array( $config['resource_identifier']=>$config[$property] ) );
						}
					}
				}

				if ( $pk ) {
					$config['resource_identifier']	=	'';
					$config[$property]				=	(string)$pk;
				} else {
					return false;
				}
			}
		}

		return true;
	}

	// updateResource
	protected function updateResource( $resource, $parent_name, $method = '' )
	{
 		if ( $parent_name ) {
 			$parent_resource	=	$this->getResource( $parent_name, $method );

 			if ( isset( $parent_resource->type ) && $parent_resource->type == 'content_type' ) {
				$resource->options->set( 'parent_type', $parent_resource->options->get( 'content_type', '' ) );
				$resource->options->set( 'parent_types', $parent_resource->options->get( 'content_types', '' ) );
				$resource->options->set( 'parent', $parent_resource->options->get( 'input' ) );
 			}
 		}

		return $resource;
	}
}
?>