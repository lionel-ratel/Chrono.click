<?php

namespace YOOtheme\Theme\Joomla\Listener;

use Joomla\CMS\Event\Model\PrepareFormEvent;

class LoadModuleForm
{
    /**
     * @param PrepareFormEvent $event
     */
    public static function handle($event): void
    {
        $form = $event->getForm();
        $data = $event->getData();

        if (
            !in_array($form->getName(), [
                'com_config.modules', // Module edit in frontend
                'com_modules.module',
                'com_advancedmodules.module',
            ])
        ) {
            return;
        }

        // copy params config to yoo_config
        if (!isset($data->params['yoo_config']) && isset($data->params['config'])) {
            $data->params['yoo_config'] = $data->params['config'];
        }

        // add yoo_config hidden input field
        $form->load(
            '<form><fields name="params"><fieldset name="advanced"><field name="yoo_config" type="hidden" default="{}" /></fieldset></fields></form>',
        );
    }
}
