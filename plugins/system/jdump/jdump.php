<?php
/**
 * J!dump plugin
 * @version      $Id$
 * @package      J!dump
 * @copyright    Copyright (C) 2007 - 2018 Mathias Verraes. All rights reserved.
 * @license      GNU/GPL
 * @link         https://github.com/mathiasverraes/jdump
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Component\Jdump\Administrator\Helper\JdumpHelper;
use Joomla\Component\Jdump\Administrator\Helper\NodeHelper;
use Joomla\Component\Jdump\Administrator\Helper\SysinfoHelper;

if ( !class_exists( 'JdumpHelper' ) ) {
	require_once JPATH_ADMINISTRATOR.'/components/com_jdump/Helper/JdumpHelper.php';
	require_once JPATH_ADMINISTRATOR.'/components/com_jdump/Helper/NodeHelper.php';
	require_once JPATH_ADMINISTRATOR.'/components/com_jdump/Helper/SysinfoHelper.php';
}

/* Version check not needed - no install on < 4.0
*/

class plgSystemJdump extends CMSPlugin
{
	function __construct($subject, $params)
	{
		parent::__construct($subject, $params);
	}

	function onAfterRender()
	{
		$app = Factory::getApplication();
		$option = $app->input->getCmd('option');

		if ($option == 'com_jdump')
		{
			return;
		}

		// settings from config.xml
		$dumpConfig = ComponentHelper::getParams( 'com_jdump' );
		$autopopup  = $dumpConfig->get( 'autopopup', 1 );

		$userstate  = $app->getUserState( 'jdump.nodes', array() );
		$cnt_dumps  = count($userstate);

		if( $autopopup && $cnt_dumps)
		{
			JdumpHelper::showPopup();
		}
	}
}

/**
 * Add a variable to the list of variables that will be shown in the debug window
 * @param mixed $var The variable you want to dump
 * @param mixed $name The name of the variable you want to dump
 */
function dumpVar($var = null, $name = '(no name)', $type = null, $level = 0, $source = array())
{
	$app = Factory::getApplication();

	if (function_exists('debug_backtrace') && !$source)
	{
		$trace = debug_backtrace();
		$source = array_merge(JdumpHelper::getSourceFunction($trace), JdumpHelper::getSourcePath($trace));
	}

	// create a new node array
	$node           = NodeHelper::getNode($var, $name, $type, $level, $source);
	//get the current userstate
	$userstate      = $app->getUserState('jdump.nodes');
	// append the node to the array
	$userstate[]    = $node;
	// set the userstate to the new array
	$app->setUserState('jdump.nodes', $userstate);
}

/**
 * Shortcut to dump the parameters of a template
 * @param object $var The "$this" object in the template
 */
function dumpTemplate($var, $name = false)
{
	$source = array();

	if (function_exists('debug_backtrace'))
	{
		$trace = debug_backtrace();
		$source = array_merge(JdumpHelper::getSourceFunction($trace), JdumpHelper::getSourcePath($trace));
	}

	$name = $name ? $name :  $var->template;
	dumpVar($var->params->toObject(), "dumpTemplate params : ".$name, '', 0, $source);
}

/**
 * Shortcut to dump a message
 * @param string $msg The message
 */
function dumpMessage($msg = '(Empty message)')
{
	$source = array();

	if (function_exists('debug_backtrace'))
	{
		$trace = debug_backtrace();
		$source = array_merge(JdumpHelper::getSourceFunction($trace), JdumpHelper::getSourcePath($trace));
	}

	dumpVar($msg, null, 'message', 0, $source);
}

/**
 * Shortcut to dump system information
 */
function dumpSysinfo()
{
	$source = array();

	if (function_exists('debug_backtrace'))
	{
		$trace = debug_backtrace();
		$source = array_merge(JdumpHelper::getSourceFunction($trace), JdumpHelper::getSourcePath($trace));
	}

	$sysinfo = new SysinfoHelper();
	dumpVar( $sysinfo->data, 'System Information', '', 0, $source);
}

/**
 * Shortcut to dump the backtrace
 */
function dumpTrace()
{
	$source = array();

	if (function_exists('debug_backtrace'))
	{
		$trace = debug_backtrace();
		$source = array_merge(JdumpHelper::getSourceFunction($trace), JdumpHelper::getSourcePath($trace));
	}

	$trace = debug_backtrace();

	$arr = dumpTraceBuild($trace);

	dumpVar($arr, 'Backtrace', 'backtrace', 0, $source);
}

function dumpTraceBuild($trace)
{
	$ret = array();

	$ret['file']     = $trace[0]['file'];
	$ret['line']     = $trace[0]['line'];

	if (isset($trace[0]['class']) && isset($trace[0]['type']))
		$ret['function'] = $trace[0]['class'].$trace[0]['type'].$trace[0]['function'];
	else
		$ret['function'] = $trace[0]['function'];

	$ret['args']     = $trace[0]['args'];

	array_shift($trace);

	if (count($trace) > 0)
		$ret['backtrace'] = dumpTraceBuild($trace);

	return $ret;
}
