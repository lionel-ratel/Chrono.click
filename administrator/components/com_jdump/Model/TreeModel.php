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

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;

class TreeModel extends ListModel
{
	var $_nodes = array();

	function __construct()
	{
		$app = Factory::getApplication();

		//get the userstate
		$this->_nodes = $app->getUserState('jdump.nodes');
		if (!is_array($this->_nodes))
		{
			$this->_nodes = array();
		}
		// and clear it
		$app->setUserState('jdump.nodes', array());

		parent::__construct();

	}

	function getNodes()
	{
		return $this->_nodes;
	}

	function countJdumps()
	{
		return count( $this->_nodes ) ;
	}
}
