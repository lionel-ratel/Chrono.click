<?php
/**
 * J!Dump
 * @version      $Id$
 * @package      com_jdump
 * @copyright    Copyright (C) 2007 - 2018 Mathias Verraes. All rights reserved.
 * @license      GNU/GPL
 * @link         https://github.com/mathiasverraes/jdump
 */
namespace Joomla\Component\Jdump\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\Utilities\ArrayHelper;

/**
 * Recipes list controller class.
 *
 * @since  1.6
 */
class PluginController extends AdminController
{
	/**
	 * Constructor.
	 *
	 * @param   array                $config   An optional associative array of configuration settings.
	 * Recognized key values include 'name', 'default_task', 'model_path', and
	 * 'view_path' (this list is not meant to be comprehensive).
	 * @param   MVCFactoryInterface  $factory  The factory.
	 * @param   CmsApplication       $app      The JApplication for the dispatcher
	 * @param   Input                $input    Input
	 *
	 */
	public function __construct($config = array(), MVCFactoryInterface $factory = null, $app = null, $input = null)
	{
		parent::__construct($config, $factory, $app, $input);

		$this->registerTask('deactivate', 'activate');
	}

	public function activate ()
	{
		Session::checkToken() or die(Text::_('JINVALID_TOKEN'));

		// Get items to approve
		$user = Factory::getUser();
		$data = array('activate' => 1, 'deactivate' => 0);
		$task = $this->getTask();
		$value = ArrayHelper::getValue($data, $task, 0, 'int');

		// Access checks.
		if (!$user->authorise('core.edit.state', 'com_jdump'))
		{
			Factory::getApplication()->enqueueMessage(Text::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED', 'warning'));
		}

		// Get the model.
		$model = $this->getModel('Plugin', 'Administrator', array());

		// Activate the plugin.
		if (!$model->activate($value))
		{
			Factory::getApplication()->enqueueMessage($model->getError(), 'warning');
		}
		else
		{
			if ($value == 1)
			{
				$ntext = 'Plug in activated';
			}
			elseif ($value == 0)
			{
				$ntext = 'Plug in deactivated';
			}
			$this->setMessage($ntext);
		}

		$this->setRedirect(Route::_('index.php?option=com_jdump&view=about', false));
	}

/**
	 * Proxy for getModel.
	 * @return  Joomla\CMS\MVC\Model\BaseDatabaseModel
	 *
	 */
	public function getModel($name = 'Plugin', $prefix = 'Administrator', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, $config);
	}
}
