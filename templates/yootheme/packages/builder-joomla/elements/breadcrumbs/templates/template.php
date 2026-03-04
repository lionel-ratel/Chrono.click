<?php

namespace YOOtheme;

use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Module\Breadcrumbs\Site\Helper\BreadcrumbsHelper;
use Joomla\Registry\Registry;
use YOOtheme\Theme\Joomla\LoadTemplateEvent;

$rand = rand();
$marker = "<!-- breadcrumbs_{$rand} -->";
$render = function () use ($__dir, $attrs, $props) {

    // Get the breadcrumbs
    $params = new Registry([
        'showHome' => $props['show_home'],
        'homeText' => Text::_($props['home_text'] ?: 'Home', 'yootheme'),
    ]);

    /** @var SiteApplication $application */
    $application = Factory::getApplication();
    /** @var BreadcrumbsHelper $helper */
    $helper = $application->bootModule('mod_breadcrumbs', 'site')
        ->getHelper('BreadcrumbsHelper');

    $items = $helper->getBreadcrumbs($params, $application);

    if (!$props['show_current']) {
        array_pop($items);
    } elseif ($items) {
        array_last($items)->link = '';
    }

    $props['items'] = $items;

    return $this->render("{$__dir}/template-breadcrumbs", compact('attrs', 'props'));
};

if ($prefix === 'page') {
    app('dispatcher')->addListener(
        'onLoadTemplate',
        /**
         * @param LoadTemplateEvent $event
         */
        function ($event) use ($render, $marker): void {
            if ($output = $event->getOutput()) {
                $event->setOutput(str_replace($marker, $render(), $output));
            }
        }
    );

    echo $marker;
} else {
    echo $render();
}
