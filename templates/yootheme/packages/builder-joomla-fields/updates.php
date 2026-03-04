<?php

namespace YOOtheme;

use Joomla\Component\Fields\Administrator\Helper\FieldsHelper;

return [
    '2.6.0-beta.0.1' => function ($node) {
        if (!class_exists(FieldsHelper::class) || !isset($node->source->props)) {
            return;
        }

        static $fields;

        if ($fields === null) {
            $fields = FieldsHelper::getFields('', null, false, null, true);
        }

        // update media fields to new MediaFieldType
        foreach ($node->source->props as $prop) {
            if (str_contains($prop->name ?? '', 'field.')) {
                foreach ($fields as $field) {
                    if (
                        str_ends_with($prop->name, 'field.' . strtr($field->name, '-', '_')) &&
                        $field->type === 'media'
                    ) {
                        $prop->name .= '.imagefile';
                    }
                }
                $prop->name = strtr($prop->name, '-', '_');
            }
        }

        if (str_contains($node->source->query->field->name ?? '', 'field.')) {
            foreach ($fields as $field) {
                if (
                    str_ends_with(
                        $node->source->query->field->name,
                        'field.' . strtr($field->name, '-', '_'),
                    )
                ) {
                    if ($field->type === 'subform') {
                        foreach ($node->source->props as $prop) {
                            $prop->name = Str::snakeCase($prop->name);
                        }

                        foreach ((array) $field->fieldparams->get('options', []) as $option) {
                            foreach ($fields as $subField) {
                                if (
                                    $subField->id === $option->customfield &&
                                    $subField->type === 'media'
                                ) {
                                    $prefix = "{$field->name}_";
                                    foreach ($node->source->props as $prop) {
                                        if (
                                            $prop->name === strtr($subField->name, '-', '_') ||
                                            $prop->name ===
                                                strtr(
                                                    substr($subField->name, strlen($prefix)),
                                                    '-',
                                                    '_',
                                                )
                                        ) {
                                            $prop->name .= '.imagefile';
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    },

    '2.2.0-beta.0.1' => function ($node) {
        if (!class_exists(FieldsHelper::class)) {
            return;
        }

        static $fields;

        if ($fields === null) {
            $fields = array_column(
                FieldsHelper::getFields('', null, false, null, true),
                'type',
                'name',
            );
        }

        if (in_array('field', explode('.', $node->source->query->field->name ?? ''))) {
            $node->source->query->field->name = strtr($node->source->query->field->name, '-', '_');
        }

        // snake case custom field names
        foreach ($node->source->props ?? [] as $prop) {
            if (in_array('field', explode('.', $prop->name ?? ''))) {
                $prop->name = strtr($prop->name, '-', '_');
            }
        }
    },
];
