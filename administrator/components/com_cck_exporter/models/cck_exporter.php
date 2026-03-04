<?php
/**
* @version 			SEBLOD Exporter 1.x
* @package			SEBLOD Exporter Add-on for SEBLOD 3.x
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

require_once dirname( __DIR__, 1 ).'/helpers/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

jimport( 'cck.base.install.export' );

// Model
class CCK_ExporterModelCCK_Exporter extends BaseDatabaseModel
{
	// export
	public function export( &$params, $task_id, $ids, &$config = array() )
	{
		$columns	=	array();
		$options	=	array();
		
		$this->_prepare( $options, $columns, $task_id, $ids );
		
		return $this->exportToCsv( $params, $options, $columns, $config );
	}

	// exportToCsv
	public function exportToCsv( $params, $options = null, $columns = null, &$config = array() )
	{
		ini_set( 'memory_limit', '512M' );
		set_time_limit( 0 );

		$app			=	Factory::getApplication();
		if ( !$columns ) {
			$columns	=	$app->input->get( 'columns', array(), 'array' );
		}

		if ( !$options ) {
			$options	=	$app->input->get( 'options', array(), 'array' );
			$options['task_id']	=	-1;
		}
		$compression	=	0;
		$name			=	( isset( $options['filename'] ) && $options['filename'] != '' ) ? $options['filename'] : $options['storage_location'];
		$name_date		=	isset( $options['filename_date'] ) ? $options['filename_date'] : $params->get( 'filename_date', '' );
		$suffix			=	( $name_date != '' ) ? '_'.Factory::getDate()->format( $name_date ) : '';
		$tmp_path		=	Factory::getConfig()->get( 'tmp_path' );
		if ( isset( $config['uniqid'] ) && $config['uniqid'] != '' ) {
			$isAjax		=	true;
			$isAjaxEnd	=	(int)$app->input->getInt( 'end', '0' );
			$tmp_dir 	=	$config['uniqid'];
			$end		=	0;
		} else {
			$isAjax		=	false;
			$isAjaxEnd	=	0;
			$tmp_dir 	=	uniqid( 'cck_' );
		}
		$path 			= 	$tmp_path.'/'.$tmp_dir;
		$root			=	$path.'/csv';
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
		
		$buffer	=	'';
		$tmp	=	$root.'/'.$name.'.csv';

		if ( $isAjax && file_exists( $tmp ) ) {
			$isNew	=	false;
		} else {
			$isNew	=	true;
			File::write( $tmp, $buffer );
		}
		if ( !isset( $options['prepare_output'] ) ) {
			$options['prepare_output']	=	0;
		}
		if ( !$this->_writeCsv( $tmp, $options, $columns, Factory::getConfig()->get( 'ftp_enable' ), $isNew ) ) {
			if ( is_dir( $path ) ) {
				Folder::delete( $path );
			}
			return false;
		}
		
		if ( $isAjax && !$isAjaxEnd ) {
			return $tmp;
		} else {
			if ( $compression == 1 ) {
				require_once JPATH_ADMINISTRATOR.'/components/com_cck/helpers/pclzip/pclzip.lib.php';
				$tmp		=	$path.'/'.$name.'.zip';
				$archive	=	new PclZip( $tmp );
				if ( $archive->create( $root, PCLZIP_OPT_REMOVE_PATH, $root ) == 0 ) {
					return false;
				}
				$ext	=	'.zip';
			} else {
				$ext	=	'.csv';
			}
			if ( is_file( $tmp ) ) {
				$file	=	$output_path.'/'.$name.$suffix.$ext;
				
				File::move( $tmp, $file );
				
				if ( is_dir( $path ) ) {
					Folder::delete( $path );
				}

				if ( isset( $options['output_extension'] ) && $options['output_extension'] != '' ) {
					$file	=	self::_transform( $file, $options['output_extension'] );
				}

				return ( $output > 0 ) ? true : $file;
			}
		}
		
		return false;
	}

	// prepareExport
	public function prepareExport( $params, $task_id, $ids )
	{
		$columns	=	array();
		$config		=	array();
		$options	=	array();
		
		$this->_prepare( $options, $columns, $task_id, $ids );

		return $this->exportToCsv( $params, $options, $columns, $config );
	}

	// _prepare
	protected function _prepare( &$options, &$columns, $task_id, $ids )
	{
		$app		=	Factory::getApplication();
		$columns	=	array(
							'core'=>false
						);
		$ids		=	implode( ',', $ids );
		$options	=	array(
							'content_type'=>'',
							'filename'=>$app->input->get( 'search', '' ),
							'ids'=>$ids,
							'search_type'=>$app->input->get( 'search', '' ),
							'separator'=>';',
							'storage_location'=>'joomla_article',
						);

		if ( $task_id ) {
			$columns	=	array();
			$options	=	array();
			$task		=	JCckDatabase::loadObject( 'SELECT id, options FROM #__cck_more_sessions WHERE id = '.(int)$task_id );
			if ( !is_object( $task ) ) {
				return false;
			}
			$settings		=	json_decode( $task->options, true );
			
			if ( count( $settings ) ) {
				foreach ( $settings as $k=>$v ) {
					$pos	=	strpos( $k, 'columns_' );
					$pos2	=	strpos( $k, 'options_' );
					$k		=	substr( $k, 8 );
					if ( $pos !== false && $pos == 0 ) {
						$columns[$k]	=	$v;
					} elseif ( $pos2 !== false && $pos2 == 0 ) {
						$options[$k]	=	$v;
					}
				}
			}
		} else {
			return false;
		}

		$options['content_type']	=	''; // Must be forced as empty
		$options['filename']		=	( isset( $options['filename'] ) && $options['filename'] != '' ) ? $options['filename'] : $app->input->get( 'search', '' );
		$options['ids']				=	$ids;
		$options['search_type']		=	$app->input->get( 'search', '' );
		$options['task_id']			=	(int)$task_id;
	}

	// _transform
	protected function _transform( $path, $format = 'xlsx' )
	{
		if ( !is_file( $path ) ) {
			return '';
		}

		if ( $format == 'xlsx' ) {
			$new_path		=	str_replace( '.csv', '.xlsx', $path );

			$reader			= 	new \PhpOffice\PhpSpreadsheet\Reader\Csv();
			$spreadsheet	=	$reader->load( $path );
			$writer			= 	new \PhpOffice\PhpSpreadsheet\Writer\Xlsx( $spreadsheet );
			
			$writer->save( $new_path );

			if ( is_file( $path ) ) {
				File::delete( $path );
			}
		}

		return $new_path;
	}

	// _writeCsv
	protected function _writeCsv( $file, $options, $columns, $ftp = '0', $isNew = true )
	{
		$handle		=	null;
		
		if ( $ftp != '1' ) {
			if ( $isNew ) {
				$handle	=	fopen( $file, 'w' );
				fwrite( $handle, chr(0xEF).chr(0xBB).chr(0xBF) );
			} else {
				$handle	=	fopen( $file, 'a' );
			}
		}

		require_once JPATH_SITE.'/plugins/cck_storage_location/'.$options['storage_location'].'/classes/exporter.php';
		
		// Config
		$config				=	array(
										'app'=>null,
										'authorise'=>0,
										'buffer'=>'',
										'component'=>( ( $options['task_id'] > -1 ) ? '' : 'com_cck_exporter' ),
										'count'=>0,
										'content_type'=>( ( $options['content_type'] ) ? $options['content_type'] : '' ),
										'doTranslation'=>JCck::getConfig_Param( 'language_jtext', ( JCck::is( '4' ) ? 1 : 0 ) ),
										'ftp'=>$ftp,
										'handle'=>$handle,
										'isNew'=>$isNew,
										'ids'=>( ( isset( $options['ids'] ) && $options['ids'] ) ? $options['ids'] : '' ),
										'location'=>$options['storage_location'],
										'pks'=>'',
										'processing'=>array(),
										'prepare_output'=>(int)$options['prepare_output'],
										'separator'=>$options['separator'],
										'table'=>( ( isset( $options['table'] ) && $options['table'] ) ? $options['table'] : '' ),
										'task_id'=>$options['task_id'],
										'types'=>array()
									);

		$processing			=	array();
		$properties			=	array( 'custom', 'table' );
		$properties			=	JCck::callFunc( 'plgCCK_Storage_Location'.$options['storage_location'], 'getStaticProperties', $properties );

		$config['app']		=	new JCckApp;
		$config['app']->loadDefault();
		$config['custom']	=	$properties['custom'];

		if ( $properties['table'] != '' ) {
			$config['table']	=	$properties['table'];
		}

		if ( JCckToolbox::getConfig()->get( 'processing', 0 ) ) {
			$config['processing']	=	JCckDatabaseCache::loadObjectListArray( 'SELECT type, scriptfile, options FROM #__cck_more_processings WHERE published = 1 ORDER BY ordering', 'type' );
			$processing				=	JCckDatabaseCache::loadObjectListArray( 'SELECT type, scriptfile, options FROM #__cck_more_processings WHERE published = 1 ORDER BY ordering', 'type' );
		}

		// Columns
		if ( isset( $columns['core'] ) ) {
			if ( $columns['core'] == -1 ) {
				$config['fields']	=	false;
			} elseif ( $columns['core'] == 1 ) {
				if ( $config['prepare_output'] ) {
					if ( is_array( $columns['core_selected'] ) ) {
						$columns['core_selected']	=	implode( ',', $columns['core_selected'] );
					}
					$columns['core_selected']		=	str_replace( ',', '","', $columns['core_selected'] );
					$config['fields']				=	JCckDatabase::loadObjectList( 'SELECT a.id, a.name, a.type, a.bool, a.bool2, a.bool3, a.bool8, a.options, a.options2, a.storage_location, a.storage_table, a.storage_field, a.storage_field2'
																					. ' FROM #__cck_core_fields AS a'
																					. ' WHERE a.storage_location = "'.$config['location'].'" AND a.storage_table = "'.$config['table'].'" AND a.storage_field IN ("'.$columns['core_selected'].'")'
																					. ' ORDER BY FIELD(storage_field, "'.$columns['core_selected'].'")'
														, 'storage_field' );
				} else {
					if ( !is_array( $columns['core_selected'] ) ) {
						$columns['core_selected']	=	explode( ',', $columns['core_selected'] );
					}
					$config['fields']	=	array_flip( $columns['core_selected'] );
				}
			}
		} else {
			$columns['core']	=	0;
		}
		
		// Fields
		$query				=	'SELECT DISTINCT a.id, a.name, a.type, a.label, b.label as label2, a.bool, a.bool2, a.bool3, a.bool8, a.options, a.options2,'
							.	' a.storage, a.storage_crypt, a.storage_location, a.storage_table, a.storage_field, a.storage_field2'
							.	' FROM #__cck_core_fields AS a';
		if ( !isset( $options['search_type'] ) ) {
			$options['search_type']	=	'';
		}
		if ( isset( $options['search_type2'] ) && $options['search_type2'] ) {
			$options['search_type']	=	$options['search_type2'];
		}
		if ( $options['search_type'] ) {
			$query			.=	' LEFT JOIN #__cck_core_search_field as b ON b.fieldid = a.id'
							.	' LEFT JOIN #__cck_core_searchs as c ON c.id = b.searchid'
							.	' WHERE c.name = "'.$options['search_type'].'"'
							.	' AND (b.client = "list" OR b.client = "item")'
							.	' AND a.name != "cck_id"'
							.	' AND b.access IN ('.implode( ',', Factory::getUser()->getAuthorisedViewLevels() ).')';

			$ordering		=	' ORDER BY b.ordering ASC';
			/* TODO#SEBLOD: */
			$config['authorise']		=	2;
		} else {
			$query			.=	' LEFT JOIN #__cck_core_type_field as b ON b.fieldid = a.id'
							.	' LEFT JOIN #__cck_core_types as c ON c.id = b.typeid';

			if ( strpos( $options['content_type'], ',' ) !== false ) {
				$query	.=	' WHERE c.name IN ("'.str_replace( ',', '","', $options['content_type'] ).'")';
			} else {
				$query	.=	' WHERE c.name = "'.$options['content_type'].'"';
			}

			$ordering		=	' ORDER BY a.name ASC';

			if ( isset( $config['content_type'] ) && $config['content_type'] != '' ) {
				$config['authorise']	=	1;
			}
		}
		$query				.=	' AND a.storage != "none"';
		if ( !( $options['search_type'] && $columns['core'] == -1 ) ) {
			$query			.=	' AND ( ( a.storage_table != "'.$config['table'].'" ) OR ( a.storage_table = "'.$config['table'].'"'
							.	' AND a.storage_field = "'.$config['custom'].'" AND a.storage_field2 != "'.$config['custom'].'" ) )';
		}
		$query				.=	$ordering;
		$config['fields2']	=	JCckDatabase::loadObjectList( $query, 'name' );
		
		// Items
		$limit		=	'';
		$ordering	=	'';
		$where		=	'storage_location = "'.$config['location'].'"';
		if ( $config['ids'] ) {
			// QueryExports
			$event	=	'onCckQueryExports';
			if ( isset( $processing[$event] ) ) {
				foreach ( $processing[$event] as $p ) {
					if ( is_file( JPATH_SITE.$p->scriptfile ) ) {
						$options	=	new Registry( $p->options );

						include JPATH_SITE.$p->scriptfile; /* Variables: $config */
					}
				}
			}
			if ( !$config['ids'] ) {
				return false;
			}
			// QueryExports

			$ordering	=	' ORDER BY FIELD(id, '.$config['ids'].')';
			$where		.=	' AND id IN ('.$config['ids'].')';
		} elseif ( ( ( $ids = Factory::getApplication()->input->getString( 'ids', '0' ) ) != '' ) && $options['task_id'] > 0 ) {
			$config['ids']	=	$ids;

			// QueryExports
			$event	=	'onCckQueryExports';
			if ( isset( $processing[$event] ) ) {
				foreach ( $processing[$event] as $p ) {
					if ( is_file( JPATH_SITE.$p->scriptfile ) ) {
						$options	=	new Registry( $p->options );

						include JPATH_SITE.$p->scriptfile; /* Variables: $config */
					}
				}
			}
			if ( !$config['ids'] ) {
				return false;
			}
			// QueryExports

			$ordering	=	$config['ids'] != '0' ? ' ORDER BY FIELD(id, '.$config['ids'].')' : '';
			$where		.=	' AND id IN ('.$config['ids'].')';
		} elseif ( $config['content_type'] ) {
			if ( strpos( $options['content_type'], ',' ) !== false ) {
				$where		.=	' AND cck IN ("'.str_replace( ',', '","', $options['content_type'] ).'")';
			} else {
				$where		.=	' AND cck = "'.$config['content_type'].'"';	
			}
			
			if ( (int)$options['limit'] ) {
				$limit		=	' ORDER BY id DESC LIMIT '.(int)$options['limit'];
			}
		}
		$items	=	JCckDatabase::loadObjectList( 'SELECT pk, cck, storage_location FROM #__cck_core WHERE '.$where.$ordering.$limit );
		$pks	=	array();

		if ( count( $items ) ) {
			if ( $limit ) {
				krsort( $items );
			}
			foreach ( $items as $item ) {
				$pks[]	=	$item->pk;
			}
		} else {
			return false;
		}
		$pks			=	implode( ',', $pks );
		$config['pks']	=	$pks;

		// BeforeExport
		$event	=	'onCckPreBeforeExports';
		if ( isset( $processing[$event] ) ) {
			foreach ( $processing[$event] as $p ) {
				if ( is_file( JPATH_SITE.$p->scriptfile ) ) {
					$options	=	new Registry( $p->options );

					include JPATH_SITE.$p->scriptfile; /* Variables: $items, $config */
				}
			}
		}

		// onCCK_Storage_LocationBeforeExports
		// JCck::callFunc_Array( 'plgCCK_Storage_Location'.$config['location'].'_Exporter', 'onCCK_Storage_LocationBeforeExports', array( $items, &$config ) );

		$event	=	'onCckPostBeforeExports';
		if ( isset( $processing[$event] ) ) {
			foreach ( $processing[$event] as $p ) {
				if ( is_file( JPATH_SITE.$p->scriptfile ) ) {
					$options	=	new Registry( $p->options );

					include JPATH_SITE.$p->scriptfile; /* Variables: $items, $config */
				}
			}
		}
		
		// Export
		JCck::callFunc_Array( 'plgCCK_Storage_Location'.$config['location'].'_Exporter', 'onCCK_Storage_LocationExport', array( $items, &$config ) );

		if ( $ftp == '1' ) {
			$buffer	=	chr(0xEF).chr(0xBB).chr(0xBF).$config['buffer'];
			File::write( $file, $buffer );
		} else {
			fclose( $handle );
		}

		if ( !$config['count'] ) {
			return false;
		}
		
		return true;
	}
}

// str_putcsv
if ( !function_exists( 'str_putcsv' ) ) {
	function str_putcsv( $input, $delimiter = ',', $enclosure = '"' )
	{
		$fp	=	fopen( 'php://temp', 'r+' );
		
		fputcsv( $fp, $input, $delimiter, $enclosure );
		rewind( $fp );
		$data	=	fread($fp, 1048576);
		fclose( $fp );
		
		return rtrim( $data, "\n" );
	}
}
?>