<?php

use Joomla\CMS\Factory;

class mod_seblod_linkInstallerScript
{
	public function install()
	{
		$db		=	Factory::getDBO();
		$module	=	(object) [
			'title' => 'SEBLOD Link',
			'position' => 'menu',
			'published' => 1,
			'client_id' => 1,
			'module' => 'mod_seblod_link',
			'access' => 1,
			'params' => '',
			'language' => '*',
			'publish_up' => Factory::getDate()->toSql(),
			'ordering' => 2,
		];

		$db->insertObject( '#__modules', $module );

		$menu = (object) ['moduleid' => $db->insertid(), 'menuid' => 0];

		$db->insertObject( '#__modules_menu', $menu );
	}
}
