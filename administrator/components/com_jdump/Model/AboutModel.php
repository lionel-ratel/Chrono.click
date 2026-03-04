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

class AboutModel extends BaseDatabaseModel
{
	public function __construct()
	{
		parent::__construct();
	}

	public function getCheckPlugin()
	{
		try
		{
			$db = $db = $this->_db;

			// get the enable status
			$query = $db->getquery(true)
			->select($db->quotename('enabled'))
			->from($db->quotename('#__extensions'))
			->where(array(
				$db->quotename('type') . ' = ' . $db->quote('plugin'),
				$db->quotename('folder') . ' = ' . $db->quote('system'),
				$db->quotename('element') . ' = ' . $db->quote('jdump'))
			);

			$db->setquery($query);

			$result = $db->loadResult();

			return $result;
		}
		catch (\RuntimeException $e)
		{
			$this->setError($e->getMessage());
			return false;
		}
	}
}
