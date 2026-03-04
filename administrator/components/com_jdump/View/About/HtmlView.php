<?php
/**
 * J!Dump
 * @version      $Id$
 * @package      com_jdump
 * @copyright    Copyright (C) 2007 - 2018 Mathias Verraes. All rights reserved.
 * @license      GNU/GPL
 * @link         https://github.com/mathiasverraes/jdump
 */
namespace Joomla\Component\Jdump\Administrator\View\About;

defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\Component\Jdump\Administrator\Helper\JdumpHelper;

class HtmlView extends BaseHtmlView
{
	protected $pluginEnabled;

	function display($tpl = null)
	{
		$this->pluginEnabled = $this->get('CheckPlugin');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new \JViewGenericdataexception(implode("\n", $errors), 500);
		}

		ToolbarHelper::title('J!Dump v ' . JdumpHelper::getVersion());

		// Get the toolbar object instance
		$toolbar = Toolbar::getInstance('toolbar');

		$toolbar->LinkButton('link')
		->text('Popup')
		->url('index.php?option=com_jdump&view=tree&format=raw&closebutton=0');

		if (!$this->pluginEnabled)
		{
			$toolbar->standardButton('activated')
			->text('Activate')
			->button_class('btn btn-sm btn-success')
			->name('thumbs-up')
			->task('plugin.activate');
		}
		else
		{
			$toolbar->standardButton('deactivated')
			->text('Deactivate')
			->button_class('btn btn-sm btn-danger')
			->name('thumbs-down')
			->task('plugin.deactivate');
		}
		//$type = 'Link', $name = 'back', $text = '', $url = null

		$toolbar->preferences('com_jdump');

		parent::display($tpl);
	}

	protected function checkPlugin()
	{
		return PluginHelper::isEnabled('system', 'J!Dump');

	}
}
