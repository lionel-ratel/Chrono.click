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

use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\Registry\Registry;

class JdumpHelper extends CMSObject
{
	static function showPopup()
	{
		$app = Factory::getApplication();
		$option = $app->input->getCmd('option');

		$client     = ApplicationHelper::getClientInfo($app->getClientID());

		// build the url
		$url = URI::base(true).'/index.php?option=com_jdump&view=tree&format=raw';

		// create the javascript
		// We can't use $document, because it's already rendered
		$script = '<!-- J!Dump -->
				<script type="text/javascript">
				window.open( "'.$url.'", "dump_'.$client->name.'");
				</script>
				<!-- / J!Dump -->';

		// add the code to the header (thanks jenscski)
		// Get the response body.
		$body = $app->getBody();
		$body = str_replace('</head>', $script.'</head>', $body);
		$app->setBody($body);

	}

	static function getSourceFunction($trace)
	{
		// Create array to avoid problems in later
		$function = array('function' => '', 'class' => '', 'type' => '');

		for ($i=1, $n=count($trace); $i<$n; $i++)
		{
			$func = $trace[$i]['function'];

			if ($func!='include' && $func!='include_once' && $func!='require' && $func!='require_once')
			{
				if (!empty($trace[$i]['type']) && !empty(@$trace[$i]['class']))
				{
					$function['function'] = $func;
					$function['class']    = $trace[$i]['class'];
					$function['type']     = $trace[$i]['type'];
				}
				else
				{
					$function['function'] = $func;
				}
			}

			if ($function['function']) break;
		}

		return $function;
	}

	static function getSourcePath($trace)
	{
		$path['file'] = str_replace(JPATH_ROOT.DIRECTORY_SEPARATOR, '', $trace[0]['file']);
		$path['line'] = $trace[0]['line'];

		return $path;
	}

	static function getMaxDepth()
	{
		static $maxdepth = null;

		if (!$maxdepth)
		{
			$dumpConfig         = ComponentHelper::getParams('com_jdump');
			$maxdepth           = intval($dumpConfig->get('maxdepth', 5));
			if( $maxdepth > 20 ) $maxdepth = 20;
			if( $maxdepth < 1  ) $maxdepth = 1;
		}

		return $maxdepth;
	}

	static function getVersion()
	{
		static $version = null;

		if (!$version)
		{
			$component = ComponentHelper::getComponent('com_jdump');
			$extension = Table::getInstance('extension');
			$extension->load($component->id);
			$manifest = new Registry($extension->manifest_cache);

			$version = $manifest->get('version');
		}

		return $version;
	}

}
