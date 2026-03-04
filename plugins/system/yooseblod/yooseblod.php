<?php

defined('_JEXEC') || exit();

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Uri\Uri;;
use Joomla\Event\SubscriberInterface;
use YOOtheme\Application;

class plgSystemYooseblod extends CMSPlugin implements SubscriberInterface
{
	/**
	 * @var CMSApplication
	 */
	public $app;

	/**
	 * CMS events the plugin will listen to.
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			'onAfterInitialise' => 'onAfterInitialise',
			'onBeforeCompileHead' => 'onBeforeCompileHead'
		];
	}

	/**
	 * Use 'onAfterInitialise' event.
	 */
	public function onAfterInitialise()
	{
 	   	\JLoader::registerNamespace( 'YooSeblod\Integration', __DIR__.'/vendor/seblod/integration-utils/src', true );

		if (!class_exists(Application::class, false)) {
			return;
		}

		$app    =   Application::getInstance();
		$joomla	=	$app(CMSApplication::class);

        if ( $joomla->isClient( 'site' ) ) {

	        $uri		=	Uri::getInstance();
	        $query		=	'SELECT JSON_UNQUOTE(JSON_EXTRACT(configuration, '.JCckDatabase::quote('$."template_style"').')) AS style_id'
	        			.	' FROM #__cck_core_sites'
	        			.	' WHERE name="'.$uri->getHost().'"';
	        $style_id	=	(int)JCckDatabase::loadResult( $query );

			if ( $style_id > 0 ) {
				$joomla->input->set( 'templateStyle', $style_id );
			}
        }

		// include autoload
		require __DIR__ . '/vendor/autoload.php';

		// Load module from the same directory
		$app->load(__DIR__ . '/modules/*/bootstrap.php');
	}

	/**
	 * Use 'onBeforeCompileHead' event.
	 */
	public function onBeforeCompileHead()
	{
		// Check if the YOOtheme app class exists
		if (!class_exists(Application::class, false)) {
			return;
		}

		$app    =   Application::getInstance();
		$joomla	=	$app(CMSApplication::class);

		/*
		if ( !$joomla->isClient( 'site' ) ) {
			return;
		}

		if ( $joomla->input->getCmd( 'option' ) !== 'com_ajax' || $joomla->input->getCmd( 'p' ) !== 'customizer' ) {
			return;
		}

		if ( $joomla->input->getCmd( 'section' ) && $joomla->input->getCmd( 'section' ) !== 'builder' ) {
			return;
		}

		if ( $joomla->input->getCmd( 'do' ) !== 'section' ) {
			return;
		}

		$wa	=	$joomla->getDocument()->getWebAssetManager();

		$wa->registerAndUseScript(
			'plg_yootheme.seblod',
			'media/plg_system_yootheme_seblod/js/script.js',
			[],
			['defer' => true, 'version' => 'auto']
		);
		*/
	}	
}
