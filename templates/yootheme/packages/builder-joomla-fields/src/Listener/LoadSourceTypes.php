<?php

namespace YOOtheme\Builder\Joomla\Fields\Listener;

use Joomla\Component\Fields\Administrator\Helper\FieldsHelper;
use YOOtheme\Builder\Joomla\Fields\Type;
use YOOtheme\Builder\Source;

class LoadSourceTypes
{
    /**
     * @param Source $source
     */
    public static function handle($source): void
    {
        if (!class_exists(FieldsHelper::class)) {
            return;
        }

        $types = [
            'User' => 'com_users.user',
            'Article' => 'com_content.article',
            'Category' => 'com_content.categories',
            'Contact' => 'com_contact.contact',
            'TagItem' => 'com_content.article',
            'SmartSearchItem' => 'com_content.article',
        ];

        $source->objectType('SqlField', [Type\SqlFieldType::class, 'config']);
        $source->objectType('ValueField', [Type\ValueFieldType::class, 'config']);
        $source->objectType('MediaField', [Type\MediaFieldType::class, 'config']);
        $source->objectType('ChoiceField', [Type\ChoiceFieldType::class, 'config']);
        $source->objectType('ChoiceFieldString', [Type\ChoiceFieldStringType::class, 'config']);

        foreach ($types as $type => $context) {
            static::configFields($source, $type, $context);
        }
    }

    protected static function configFields(Source $source, string $type, string $context): void
    {
        $fieldType = "{$type}Fields";

        // add field on type
        $source->objectType($type, [
            'fields' => [
                'field' => [
                    'type' => $fieldType,
                    'extensions' => [
                        'call' => Type\FieldsType::class . '::field',
                    ],
                ],
            ],
        ]);

        // configure field type
        $source->objectType(
            $fieldType,
            fn() => Type\FieldsType::config(
                $source,
                $type,
                $context,
                FieldsHelper::getFields($context),
            ),
        );
    }
}
