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

use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Version;
use Joomla\Component\Jdump\Administrator\Helper\JdumpHelper;
use Joomla\Utilities\ArrayHelper;

class SysinfoHelper extends CMSObject
{
	var $data = array();

	public function __construct()
	{
		// execute all methods that start with '_load'
		foreach (get_class_methods($this) as $method) {
			if ('load' == substr($method, 0, 4)) {
				$this->$method();
			}
		}
		$this->sort( $this->data );
	}

	protected function loadConfig()
	{
		$jconf                  = new \JConfig();
		$jconf->password        = '*******';
		$jconf->ftp_pass        = '*******';
		$jconf->secret          = '*******';
		$this->data['Joomla Configuration'] = ArrayHelper::fromObject($jconf);
	}

	protected function loadVersions()
	{
		$version = new Version();
		$this->data['Versions']['Joomla!']      = $version->getLongVersion();
		$this->data['Versions']['J!Dump']       = JdumpHelper::getVersion();
		$this->data['Versions']['PHP']          = phpversion();
		$this->data['Versions']['Apache']       = function_exists('apache_get_version') ? apache_get_version() : 'unknown';
		$this->data['Versions']['Zend Engine']  = zend_version();
	}

	protected function loadEnvironment()
	{
		$this->data['Environment']['_SERVER']		=  $_SERVER;
		$this->data['Environment']['_GET']			=  $_GET;
		$this->data['Environment']['_POST']			=  $_POST;
		$this->data['Environment']['_COOKIE']		=  $_COOKIE;
		$this->data['Environment']['_FILES']		=  $_FILES;
		$this->data['Environment']['_ENV']			=  $_ENV;
		$this->data['Environment']['_REQUEST']		=  $_REQUEST;
	}

	// recursive natural key sort
	protected function sort(&$array)
	{
		uksort($array, 'strnatcasecmp'); // this will do natural key sorting (A=a)
		foreach (array_keys($array) as $k)
		{
			if ('array' == gettype($array[$k]))
			{
				$this->sort($array[$k]);
			}
		}
	}

}
