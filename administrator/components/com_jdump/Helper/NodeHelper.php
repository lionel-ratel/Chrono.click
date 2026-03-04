<?php
/**
 * J!Dump
 * @version      $Id$
 * @package      com_jdump
 * @copyright    Copyright (C) 2007 - 2018 Mathias Verraes. All rights reserved.
 * @license      GNU/GPL
 * @link         https://github.com/mathiasverraes/jdump
 */
namespace Joomla\Component\Jdump\Administrator\Helper;

defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Component\ComponentHelper;
use Joomla\Component\Jdump\Administrator\Helper\JdumpHelper;
//use Joomla\CMS\String\String

class NodeHelper
{
	static function getNode($var, $name, $type = null, $level = 0, $source = null)
	{
		$node['name']       = $name;
		$node['type']       = strtolower($type ? $type : gettype( $var ));
		$node['children']   = array();
		$node['level']      = $level;
		$node['source']     = $source;

		// expand the var according to type
		switch ($node['type'])
		{
			case 'backtrace': // *Skip source when backtrace, and change to array
				//$node['source'] = null; // source not needed but *nice to know where you find dumpTrace
				$node['type']   = 'array';

			case 'array':
				if ($level >= JdumpHelper::getMaxDepth())
				{
					$node['children'][] = NodeHelper::getNode('Maximum depth reached', null, 'message');
				}
				else
				{
					ksort($var);
					foreach ($var as $key => $value)
					{
						$node['children'][] = NodeHelper::getNode($value, $key, null, $level + 1);
					}
				}
				break;

			case 'object':
				if ($level >= JdumpHelper::getMaxDepth())
				{
					$node['children'][] = NodeHelper::getNode('Maximum depth reached', null, 'message');
				}
				else
				{
					$object_vars = get_object_vars($var) ;
					$methods     = get_class_methods($var) ;
					if (count($object_vars))
					{
						$node['children'][] = NodeHelper::getNode($var, 'Properties', 'properties', $level);
					}
					if (count($methods))
					{
						$node['children'][] = NodeHelper::getNode($var, 'Methods', 'methods', $level);
					}
				}
				$node['classname'] = get_class($var);
				break;

			case 'properties':
				$object_vars = get_object_vars($var);
				ksort($object_vars);
				foreach ($object_vars as $key => $value)
				{
					$node['children'][] = NodeHelper::getNode($value, $key, null, $level + 1);
				}
				break;

			case 'methods':
				$methods = get_class_methods($var);
				sort($methods);
				foreach ($methods as $value)
				{
					$node['children'][] = NodeHelper::getNode(null, $value, 'method');
				}
				break;


			case 'string':

				$dumpConfig		= ComponentHelper::getParams('com_jdump');
				$trimstrings	= $dumpConfig->get('trimstrings', 1);
				$maxstrlength	= $dumpConfig->get('maxstrlength', 150);

				//original string length
				$length			= strlen($var);

				// trim string if needed
				if ($trimstrings AND $length > $maxstrlength)
				{
					$var = substr($var, 0, $maxstrlength) . '...';
					$node['length'] = $length;
				}

				$node['value']	= $var;
				break;

			default:
				$node['value'] = $var;
				break;
		}

		return $node;
	}

}
