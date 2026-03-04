<?php
/**
* @version 			SEBLOD Toolbox 1.x
* @package			SEBLOD Toolbox Add-on for SEBLOD 3.x
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;
use Joomla\Registry\Registry;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

// Model
class CCK_ToolboxModelCCK_Toolbox extends BaseDatabaseModel
{
	// prepareProcess
	public function prepareProcess( &$params, $task_id, $ids, &$config = array() )
	{
		$app		=	Factory::getApplication();
		$ids		=	implode( ',', $ids );
		$ids		=	( $ids != '' ) ? $ids : '';
		$options	=	array(
							'content_type'=>'',
							'filename'=>'',
							'ids'=>$ids,
							'output'=>0,
							'scriptfile'=>'',
							'task_id'=>(int)$task_id
						);
		
		if ( $options['task_id'] ) {
			$processing				=	JCckDatabase::loadObject( 'SELECT name, options, scriptfile FROM #__cck_more_processings WHERE published = 1 AND id = '.(int)$options['task_id'] );
			$options['filename']	=	$processing->name;
			$options['scriptfile']	=	$processing->scriptfile;

			if ( $processing->options != '' ) {
				$processing->options	=	new Registry( $processing->options );
				$output					=	$processing->options->get( 'output', '' );

				if ( $output == '' ) {
					$output	=	-1;
				}

				$options['output']	=	$output;

				$params->set( 'output_extension', $processing->options->get( 'output_extension', '' ) );
				$params->set( 'output_filename_date', $processing->options->get( 'output_filename_date', '' ) );
				$params->set( 'output', $output );
				$params->set( 'output_path', $processing->options->get( 'output_path', '' ) );
			}
		}
		
		return $this->prepareOutput( $params, $options, $config, @$processing->options );
	}

	// prepareOutput
	protected function prepareOutput( $params, $options = null, &$config = array(), $processing_options )
	{
		if ( $options['output'] == 0 ) {
			$app			=	Factory::getApplication();

			if ( !$options ) {
				$options	=	$app->input->get( 'options', array(), 'array' );
				$options['task_id']	=	-1;
			}
			$extension		=	$params->get( 'output_extension', 'txt' );
			$name			=	( isset( $options['filename'] ) && $options['filename'] != '' ) ? $options['filename'] : 'processing';
			$name_date		=	$params->get( 'output_filename_date', '' );
			$suffix			=	( $name_date != '' ) ? '_'.Factory::getDate()->format( $name_date ) : '';
			
			$tmp_path		=	Factory::getConfig()->get( 'tmp_path' );
			$tmp_dir 		=	uniqid( 'cck_' );
			$path 			= 	$tmp_path.'/'.$tmp_dir;
			$root			=	$path.'/'.$extension;
			//
			$output			=	$params->get( 'output', 0 );
			$output_path	=	$params->get( 'output_path', '' );

			if ( $output == 2 && $output_path != '' && is_dir( $output_path ) ) {
				$output_path	=	$output_path;
			} elseif ( $output_path != '' && $output_path != 'tmp/' ) {
				$output_path	=	JPATH_SITE.'/'.$output_path;

				if ( !is_dir( $output_path ) ) {
					CCK_Export::createDir( $output_path );
				}
			} else {
				$output_path	=	$tmp_path;
			}
		}

		$buffer		=	array();
		$return		=	$this->processItems( $options, $config, $buffer, $processing_options );

		if ( $options['output'] != 0 || $return === false ) {
			return $return;
		}

		if ( $count = count( $buffer ) ) {
			foreach ( $buffer as $k=>$v ) {
				$tmp	=	$root.'/'.$name.'-'.($k + 1).'.'.$extension;

				File::write( $tmp, $buffer[$k] );
			}
		}
		
		if ( $count > 1 ) {
			require_once JPATH_ADMINISTRATOR.'/components/com_cck/helpers/pclzip/pclzip.lib.php';
			$tmp		=	$path.'/'.$name.'.zip';
			$archive	=	new PclZip( $tmp );

			if ( $archive->create( $root, PCLZIP_OPT_REMOVE_PATH, $root ) == 0 ) {
				return false;
			}
			$ext	=	'.zip';
		} else {
			$ext	=	'.'.$extension;
		}
		
		if ( is_file( $tmp ) ) {
			$file	=	$output_path.'/'.$name.$suffix.$ext;
			File::move( $tmp, $file );
			
			if ( is_dir( $path ) ) {
				Folder::delete( $path );
			}
			
			return ( $output > 0 ) ? true : $file;
		}
		
		return false;
	}

	// process
	public function process( &$params, $task_id, $ids, &$config = array() )
	{
		$app		=	Factory::getApplication();
		$ids		=	implode( ',', $ids );
		$ids		=	( $ids != '' ) ? $ids : '';
		$options	=	array(
							'content_type'=>'',
							'filename'=>'',
							'ids'=>$ids,
							'output'=>0,
							'scriptfile'=>'',
							'task_id'=>(int)$task_id
						);
		
		if ( $options['task_id'] ) {
			$processing				=	JCckDatabase::loadObject( 'SELECT name, options, scriptfile FROM #__cck_more_processings WHERE id = '.(int)$options['task_id'] );
			$options['filename']	=	$processing->name;
			$options['scriptfile']	=	$processing->scriptfile;

			if ( $processing->options != '' ) {
				$processing->options	=	new Registry( $processing->options );
				$output					=	$processing->options->get( 'output', '' );

				if ( $output == '' ) {
					$output	=	-1;
				}

				$options['output']	=	$output;

				$params->set( 'output_extension', $processing->options->get( 'output_extension', '' ) );
				$params->set( 'output_filename_date', $processing->options->get( 'output_filename_date', '' ) );
				$params->set( 'output', $output );
				$params->set( 'output_path', $processing->options->get( 'output_path', '' ) );
			}
		}
		$buffer	=	array();
		
		return $this->processItems( $options, $config, $buffer, @$processing->options );
	}

	// processItem
	protected function processItem( $scriptfile, &$config, &$buffer, $options )
	{
		include JPATH_SITE.$scriptfile;
	}

	// processItems
	protected function processItems( $options, &$config, &$buffer, $processing_options )
	{
		if ( !is_file( JPATH_SITE.$options['scriptfile'] ) ) {
			return false;
		}

		if ( is_null( $processing_options ) ) {
			$processing_options	=	new Registry;
		} elseif ( is_array( $processing_options ) ) {
			$processing_options	=	new Registry( $processing_options );
		}

		if ( (int)$processing_options->get( 'input', '0' ) ) {
			$ids	=	explode( ',', $options['ids'] );
			$count	=	0;
			$length	=	count( $ids );
			$uniqid	=	( isset( $config['uniqid'] ) ) ? $config['uniqid'] : '';

			// Process
			if ( $length ) {
				foreach ( $ids as $i=>$id ) {
					$config	=	array(
									'buffer'=>'',
									'count'=>$length,
									'doTranslation'=>JCck::getConfig_Param( 'language_jtext', ( JCck::is( '4' ) ? 1 : 0 ) ),
									'i'=>$i++,
									'id'=>$id,
									'location'=>'free',
									'message_style'=>'message',
									'path'=>'',
									'pk'=>0,
									'type'=>'',
									'type_id'=>0,
									'uniqid'=>$uniqid
								);
					
					$this->processItem( $options['scriptfile'], $config, $buffer, $processing_options );

					if ( $config['buffer'] != '' ) {
						$buffer[]	=	$config['buffer'];
					}
					$count++;
				}
			}
			if ( !$count ) {
				return false;
			}
		} else {
			// Init
			$query	=	'SELECT a.id, a.pk, a.cck, a.storage_location, b.id as cck_id'
					.	' FROM #__cck_core AS a'
					.	' LEFT JOIN #__cck_core_types AS b ON b.name = a.cck'
					;

			if ( $options['ids'] != '' ) {
				$query	.=	' WHERE a.id IN ('.$options['ids'].')';
				$query	.=	' ORDER BY FIELD(a.id,'.$options['ids'].')';
			} else {
				$ids	=	Factory::getApplication()->input->getString( 'ids', '0' );
				$query	.=	' WHERE a.id IN ('.$ids.')';
				$query	.=	' ORDER BY FIELD(a.id,'.$ids.')';
			}
			
			$count	=	0;
			$items	=	JCckDatabase::loadObjectList( $query );
			$length	=	count( $items );
			$uniqid	=	( isset( $config['uniqid'] ) ) ? $config['uniqid'] : '';
			$user	=	Factory::getUser();

			// Process
			if ( $length ) {
				foreach ( $items as $i=>$item ) {
					$config	=	array(
									'buffer'=>'',
									'count'=>$length,
									'doTranslation'=>JCck::getConfig_Param( 'language_jtext', ( JCck::is( '4' ) ? 1 : 0 ) ),
									'error'=>false,
									'i'=>$i++,
									'id'=>$item->id,
									'location'=>$item->storage_location,
									'message_style'=>'message',
									'path'=>'',
									'pk'=>$item->pk,
									'type'=>$item->cck,
									'type_id'=>$item->cck_id,
									'uniqid'=>$uniqid
								);
					
					if ( !$user->authorise( 'core.process', 'com_cck.form.'.$config['type_id'] ) ) {
						continue;
					}
					$this->processItem( $options['scriptfile'], $config, $buffer, $processing_options );

					if ( $config['buffer'] != '' ) {
						$buffer[]	=	$config['buffer'];
					}
					if ( $config['error'] ) {
						continue;
					}

					$count++;
				}
			}
			if ( !$count ) {
				return false;
			}
		}

		return true;
	}
}
?>