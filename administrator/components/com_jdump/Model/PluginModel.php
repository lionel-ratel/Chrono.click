<?php
/**
 * J!Dump
 * @version      $Id$
 * @package      com_jdump
 * @copyright    Copyright (C) 2007 - 2018 Mathias Verraes. All rights reserved.
 * @license      GNU/GPL
 * @link         https://github.com/mathiasverraes/jdump
 */
namespace Joomla\Component\Jdump\Administrator\Model;

defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\MVC\Model\BaseDatabaseModel;

class PluginModel extends BaseDatabaseModel
{
	public function __construct()
	{
		parent::__construct();
	}

	public function activate($value = 1)
	{
		try
		{
			$db = $this->_db;

			// Change plug in status
			$query = $db->getquery(true)
			->update($db->quotename('#__extensions'))
			->set($db->quotename('enabled') . ' = ' . (int) $value)
			->where(array(
				$db->quotename('type') . ' = ' . $db->quote('plugin'),
				$db->quotename('folder') . ' = ' . $db->quote('system'),
				$db->quotename('element') . ' = ' . $db->quote('jdump'))
			);

			$db->setquery($query);
			$result = $db->execute();

			$success = $db->getAffectedRows();

			if ($success)
			{
				return true;
			}
			else
			{
				$this->setError('Plug in could not be updated!');
				return false;
			}
		}
		catch (\RuntimeException $e)
		{
			$this->setError($e->getMessage());
			return false;
		}
	}
}
