<?php

namespace YOOtheme\Builder\Joomla\RegularLabs\Listener;

use Joomla\CMS\Categories\CategoryNode;
use Joomla\CMS\Plugin\PluginHelper;
use YOOtheme\Builder\Joomla\Fields\Type\FieldsType;
use YOOtheme\Builder\Joomla\Source\ArticleHelper;
use YOOtheme\Builder\Source;

/**
 * @phpstan-import-type Field from FieldsType
 * @phpstan-import-type Article from ArticleHelper
 * @phpstan-import-type FieldConfig from Source
 */
class ArticlesField
{
    /**
     * @param FieldConfig $config
     * @param Field $field
     * @return FieldConfig|list<FieldConfig>|null
     */
    public static function config(
        ?array $config,
        object $field,
        Source $source,
        string $context
    ): ?array {
        if ($field->type !== 'articles') {
            return $config;
        }

        if (!PluginHelper::isEnabled('fields', 'articles')) {
            return $config;
        }

        return [
            [
                'type' => $field->fieldparams->get('multiple')
                    ? ['listOf' => 'Article']
                    : 'Article',
                'extensions' => [
                    'call' => [
                        'func' => __CLASS__ . '::resolve',
                        'args' => [
                            'context' => $context,
                            'field' => $field->name,
                            'id' => $field->id,
                        ],
                    ],
                ],
            ] + $config,
        ];
    }

    /**
     * @param Article|CategoryNode|array<string, string> $item
     * @param array<string, mixed> $args
     * @return list<Article>|Article|null
     */
    public static function resolve($item, array $args)
    {
        /** @var ?Field $field */
        $field = isset($item->id)
            ? FieldsType::getField($args['field'], $item, $args['context'])
            : FieldsType::getSubfield($args['id'] ?? 0, $args['context']);

        if (!$field) {
            return null;
        }

        $fieldValue = $field->rawvalue ?? ($item["field{$args['id']}"] ?? null);

        if (!$fieldValue) {
            return null;
        }

        $params = $field->fieldparams;
        $articles = ArticleHelper::get($fieldValue, [
            'order' => [
                $params['articles_ordering'] ?? 'title' =>
                    $params['articles_ordering_direction'] ?? 'ASC',

                $params['articles_ordering_2'] ?? 'created' =>
                    $params['articles_ordering_direction_2'] ?? 'DESC',
            ],
        ]);

        return empty($params['multiple']) ? array_first($articles) : $articles;
    }
}
