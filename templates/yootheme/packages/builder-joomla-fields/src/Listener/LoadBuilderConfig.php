<?php

namespace YOOtheme\Builder\Joomla\Fields\Listener;

use Joomla\Component\Fields\Administrator\Helper\FieldsHelper;
use YOOtheme\Builder\BuilderConfig;
use function YOOtheme\trans;

class LoadBuilderConfig
{
    /**
     * @param BuilderConfig $config
     */
    public static function handle($config): void
    {
        if (!class_exists(FieldsHelper::class)) {
            return;
        }

        $fields = [];

        foreach (FieldsHelper::getFields('com_content.article') as $field) {
            if ($field->fieldparams->get('multiple') || $field->fieldparams->get('repeat')) {
                continue;
            }

            $fields[$field->group_title ?? trans('Fields')][] = [
                'value' => "field:{$field->id}",
                'text' => $field->title,
            ];
        }

        if ($fields) {
            foreach ($fields as $group => $options) {
                $config->push('sources.articleOrderOptions', [
                    'label' => $group,
                    'options' => $options,
                ]);
            }
        }

        $fields = [];

        foreach (FieldsHelper::getFields('com_content.article') as $field) {
            if ($field->type !== 'calendar') {
                continue;
            }

            $fields[$field->group_title ?? trans('Fields')][] = [
                'value' => "field:{$field->id}",
                'text' => $field->title,
            ];
        }

        if ($fields) {
            foreach ($fields as $group => $options) {
                $config->push('sources.articleDateFilterOptions', [
                    'label' => $group,
                    'options' => $options,
                ]);
            }
        }
    }
}
