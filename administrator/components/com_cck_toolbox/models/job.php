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
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Uri\Uri;

// Model
class CCK_ToolboxModelJob extends AdminModel
{
	protected $text_prefix	=	'COM_CCK';
	protected $vName		=	'job';

	// canDelete
	protected function canDelete( $record )
	{
		$user	=	Factory::getUser();
		
		if ( ! empty( $record->folder ) ) {
			// Folder Permissions
			return $user->authorise( 'core.delete', 'com_cck.folder.'.(int)$record->folder );
		}

		// Component Permissions
		return parent::canDelete( $record );
	}

	// canEditState
	protected function canEditState( $record )
	{
		$user	=	Factory::getUser();

		if ( ! empty( $record->folder ) ) {
			// Folder Permissions
			return $user->authorise( 'core.edit.state', 'com_cck.folder.'.(int)$record->folder );
		}

		// Component Permissions
		return parent::canEditState( $record );
	}
	
	// populateState
	protected function populateState()
	{
		$app	=	Factory::getApplication( 'administrator' );
		$pk		=	$app->input->getInt( 'id', 0 );
		
		$this->setState( 'job.id', $pk );
	}
	
	// getForm
	public function getForm( $data = array(), $loadData = true )
	{
		$form	=	$this->loadForm( CCK_ADDON.'.'.$this->vName, $this->vName, array( 'control' => 'jform', 'load_data' => $loadData ) );
		if ( empty( $form ) ) {
			return false;
		}
		
		return $form;
	}
	
	// getItem
	public function getItem( $pk = null )
	{
		if ( $item = parent::getItem( $pk ) ) {
			if ( isset( $item->scriptfile ) && $item->scriptfile[0] == '/' ) {
				$item->scriptfile	=	substr( $item->scriptfile, 1 );
			}
		}
		
		return $item;
	}
	
	// getTable
	public function getTable( $type = 'Job', $prefix = CCK_TABLE, $config = array() )
	{
		return Table::getInstance( $type, $prefix, $config );
	}
	
	// loadFormData
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data	=	Factory::getApplication()->getUserState( CCK_ADDON.'.edit.'.$this->vName.'.data', array() );

		if ( empty( $data ) ) {
			$data	=	$this->getItem();
		}

		return $data;
	}
	
	// prepareTable
	protected function prepareTable( $table )
	{
		$data	=	$this->prepareData();
		
		$table->bind( $data );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Store
	
	// prepareData
	protected function prepareData()
	{
		$app					=	Factory::getApplication();
		$data					=	$app->input->post->getArray();
		$data['description']	=	$app->input->post->get( 'description', '', 'raw' );
		
		if ( !$data['run_as_mode'] ) {
			$data['run_as']	=	0;
		}
		$data['run_url']	=	(int)$data['run_url'];

		if ( $data['type'] == 'cron_cli' ) {
			$app	=	Factory::getApplication();
			$path	=	$app->get( 'tmp_path' );
			$path	=	substr( $path, 0, strrpos( $path, '/' ) );
			$cmd	=	$path.'/cli/cck_job_'.$data['name'].'.php';
			$path	=	substr( $path, 0, strpos( $path, 'public_html' ) );
			$host	=	Uri::getInstance()->getHost();

			if ( $path != '' ) {
				$path	.=	'etc/php5/';
			} else {
				$path	=	'[...]';
			}
			$cmd2	=	'env php -c '.$path.' '.$cmd;
			$cli	=	JPATH_SITE.'/cli/cck_job_'.$data['name'].'.php';
			
			$cmd1	=	'su -l '.str_replace( 'www.', '', $host ).' -s /bin/bash -c \'/usr/bin/php-fpm-cli "$HOME/public_html/cli/cck_job_'.$data['name'].'.php"\'';
			$cmd3	=	'/usr/bin/php-fpm-cli "$HOME/public_html/cli/cck_job_'.$data['name'].'.php" -H "host: '.$host.'" > /dev/null 2>&1';

			$app->enqueueMessage( Text::sprintf( 'COM_CCK_CRON_CLI_SET_UP', '<strong>'.$cmd1.'</strong>', '<strong>'.$cmd2.'</strong>', '<strong>'.$cmd3.'</strong>' ) );
			
			if ( !is_file( $cli ) ) {
				$buffer		=	file_get_contents( JPATH_SITE.'/libraries/cck/development/cli/job/cck_job_%name%.php' );
				$buffer		=	str_replace( '%name%', $data['name'], $buffer );

				File::write( $cli, $buffer );
			}
		}
		
		unset( $data['processings'] );

		return $data;
	}

	// postStore
	public function postStore( $pk )
	{
		$data		=	Factory::getApplication()->input->post->getArray();

		// Processings
		$clause		=	'job_id = '.$pk;
		$table_name	=	'#__cck_more_job_processing';
		$join		=	JCckTableBatch::getInstance( $table_name );
		$join->delete( $clause );
		
		if ( is_array( $data['processings'] ) && count( $data['processings'] ) ) {
			$join->bindArray( array( 'processing_id'=>$data['processings'] ) );
			$join->check( array( 'id'=>'', 'job_id'=>$pk ) );
			$join->store();
		}
	}
}
?>