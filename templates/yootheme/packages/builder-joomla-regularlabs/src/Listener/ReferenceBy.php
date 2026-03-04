<?php

namespace YOOtheme\Builder\Joomla\RegularLabs\Listener;

use Joomla\CMS\Categories\Categories;
use Joomla\CMS\Categories\CategoryNode;
use Joomla\CMS\User\User;
use Joomla\Component\Fields\Administrator\Helper\FieldsHelper;
use Joomla\Database\DatabaseDriver;
use YOOtheme\Arr;
use YOOtheme\Builder\Joomla\Fields\Type\FieldsType;
use YOOtheme\Builder\Joomla\Source\ArticleHelper;
use YOOtheme\Builder\Joomla\Source\Type\CustomCategoriesQueryType;
use YOOtheme\Builder\Joomla\Source\UserHelper;
use YOOtheme\Builder\Source;
use function YOOtheme\app;
use function YOOtheme\trans;

/**
 * @phpstan-import-type Field from FieldsType
 * @phpstan-import-type Article from ArticleHelper
 * @phpstan-import-type FieldConfig from Source
 */
class ReferenceBy
{
    /**
     * @return array<string, array<string, FieldConfig>>
     */
    public static function config(): array
    {
        $fields = [];
        foreach (
            [
                'com_content.article' => 'articleField',
                'com_content.categories' => 'categoryField',
                'com_users.user' => 'userField',
            ]
            as $context => $fn
        ) {
            foreach (FieldsHelper::getFields($context) as $field) {
                if ($field->type !== 'articles') {
                    continue;
                }

                $name = strtr($field->name, '-', '_') . 'RelatedBy';
                $group = $field->group_title ?: trans('Fields');
                $fields[$name] = static::$fn($field->title, $group) + [
                    'name' => $name,
                    'extensions' => [
                        'call' => [
                            'func' => __CLASS__ . '::resolve',
                            'args' => [
                                'context' => $context,
                                'field' => $field->name,
                            ],
                        ],
                    ],
                ];
            }
        }

        return ['fields' => $fields];
    }

    /**
     * @return FieldConfig
     */
    protected static function articleField(string $title, string $group): array
    {
        return [
            'type' => ['listOf' => 'Article'],
            'args' => [
                'offset' => [
                    'type' => 'Int',
                ],
                'limit' => [
                    'type' => 'Int',
                ],
                'order' => [
                    'type' => 'String',
                ],
                'order_direction' => [
                    'type' => 'String',
                ],
                'order_alphanum' => [
                    'type' => 'Boolean',
                ],
                'order_reverse' => [
                    'type' => 'Boolean',
                ],
            ],
            'metadata' => [
                'label' => trans('%title% (Referencing me)', ['%title%' => $title]),
                'group' => $group,
                'arguments' => [
                    '_offset' => [
                        'description' => trans(
                            'Set the starting point and limit the number of articles.',
                        ),
                        'type' => 'grid',
                        'width' => '1-2',
                        'fields' => [
                            'offset' => [
                                'label' => trans('Start'),
                                'type' => 'number',
                                'default' => 0,
                                'modifier' => 1,
                                'attrs' => [
                                    'min' => 1,
                                    'required' => true,
                                ],
                            ],
                            'limit' => [
                                'label' => trans('Quantity'),
                                'type' => 'limit',
                                'default' => 10,
                                'attrs' => [
                                    'placeholder' => trans('No limit'),
                                    'min' => 0,
                                ],
                            ],
                        ],
                    ],
                    '_order' => [
                        'type' => 'grid',
                        'width' => '1-2',
                        'fields' => [
                            'order' => [
                                'label' => trans('Order'),
                                'type' => 'select',
                                'default' => 'publish_up',
                                'options' => [
                                    [
                                        'evaluate' =>
                                            'yootheme.builder.sources.articleOrderOptions',
                                    ],
                                ],
                            ],
                            'order_direction' => [
                                'label' => trans('Direction'),
                                'type' => 'select',
                                'default' => 'DESC',
                                'options' => [
                                    trans('Ascending') => 'ASC',
                                    trans('Descending') => 'DESC',
                                ],
                            ],
                        ],
                    ],
                    'order_alphanum' => [
                        'text' => trans('Alphanumeric Ordering'),
                        'type' => 'checkbox',
                    ],
                    'order_reverse' => [
                        'text' => trans('Reverse Results'),
                        'type' => 'checkbox',
                    ],
                ],
                'directives' => [],
            ],
        ];
    }

    /**
     * @return FieldConfig
     */
    protected static function categoryField(string $title, string $group): array
    {
        return [
            'type' => [
                'listOf' => 'Category',
            ],
            'args' => [
                'offset' => [
                    'type' => 'Int',
                ],
                'limit' => [
                    'type' => 'Int',
                ],
                'order' => [
                    'type' => 'String',
                ],
                'order_direction' => [
                    'type' => 'String',
                ],
            ],
            'metadata' => [
                'label' => trans('%title% (Referencing me)', ['%title%' => $title]),
                'group' => trans('%group% (Category)', [
                    '%group%' => $group,
                ]),
                'arguments' => [
                    '_offset' => [
                        'description' => trans(
                            'Set the starting point and limit the number of categories.',
                        ),
                        'type' => 'grid',
                        'width' => '1-2',
                        'fields' => [
                            'offset' => [
                                'label' => trans('Start'),
                                'type' => 'number',
                                'default' => 0,
                                'modifier' => 1,
                                'attrs' => [
                                    'min' => 1,
                                    'required' => true,
                                ],
                            ],
                            'limit' => [
                                'label' => trans('Quantity'),
                                'type' => 'limit',
                                'default' => 10,
                                'attrs' => [
                                    'placeholder' => trans('No limit'),
                                    'min' => 0,
                                ],
                            ],
                        ],
                    ],
                    '_order' => [
                        'type' => 'grid',
                        'width' => '1-2',
                        'fields' => [
                            'order' => [
                                'label' => trans('Order'),
                                'type' => 'select',
                                'default' => 'ordering',
                                'options' => [
                                    trans('Alphabetical') => 'title',
                                    trans('Category Order') => 'ordering',
                                    trans('Random') => 'rand',
                                ],
                            ],
                            'order_direction' => [
                                'label' => trans('Direction'),
                                'type' => 'select',
                                'default' => 'ASC',
                                'options' => [
                                    trans('Ascending') => 'ASC',
                                    trans('Descending') => 'DESC',
                                ],
                            ],
                        ],
                    ],
                ],
                'directives' => [],
            ],
        ];
    }

    /**
     * @return FieldConfig
     */
    protected static function userField(string $title, string $group): array
    {
        return [
            'type' => [
                'listOf' => 'User',
            ],
            'args' => [
                'offset' => [
                    'type' => 'Int',
                ],
                'limit' => [
                    'type' => 'Int',
                ],
                'order' => [
                    'type' => 'String',
                ],
                'order_direction' => [
                    'type' => 'String',
                ],
            ],
            'metadata' => [
                'label' => trans('%title% (Referencing me)', ['%title%' => $title]),
                'group' => trans('%group% (User)', [
                    '%group%' => $group,
                ]),
                'arguments' => [
                    '_offset' => [
                        'description' => trans(
                            'Set the starting point and limit the number of users.',
                        ),
                        'type' => 'grid',
                        'width' => '1-2',
                        'fields' => [
                            'offset' => [
                                'label' => trans('Start'),
                                'type' => 'number',
                                'default' => 0,
                                'modifier' => 1,
                                'attrs' => [
                                    'min' => 1,
                                    'required' => true,
                                ],
                            ],
                            'limit' => [
                                'label' => trans('Quantity'),
                                'type' => 'limit',
                                'default' => 10,
                                'attrs' => [
                                    'placeholder' => trans('No limit'),
                                    'min' => 0,
                                ],
                            ],
                        ],
                    ],
                    '_order' => [
                        'type' => 'grid',
                        'width' => '1-2',
                        'fields' => [
                            'order' => [
                                'label' => trans('Order'),
                                'type' => 'select',
                                'default' => 'name',
                                'options' => [
                                    trans('Alphabetical') => 'name',
                                    trans('Registered Date') => 'registerDate',
                                    trans('Last Visit Date') => 'lastvisitDate',
                                ],
                            ],
                            'order_direction' => [
                                'label' => trans('Direction'),
                                'type' => 'select',
                                'default' => 'ASC',
                                'options' => [
                                    trans('Ascending') => 'ASC',
                                    trans('Descending') => 'DESC',
                                ],
                            ],
                        ],
                    ],
                ],
                'directives' => [],
            ],
        ];
    }

    /**
     * @param array<string, mixed> $args
     * @return list<CategoryNode|User|Article>|null
     */
    public static function resolve(object $item, array $args)
    {
        if (!isset($item->id)) {
            return null;
        }

        if (!($field = FieldsType::getField($args['field'], null, $args['context']))) {
            return null;
        }

        if ($args['context'] === 'com_content.article') {
            return ArticleHelper::query(
                ['fields' => [['id' => $field->id, 'value' => $item->id]]] +
                    Arr::omit($args, ['field', 'context']),
            );
        }

        /** @var DatabaseDriver $db */
        $db = app(DatabaseDriver::class);
        $ids = $db
            ->setQuery(
                $db
                    ->createQuery()
                    ->select($db->qn('item_id'))
                    ->from('#__fields_values')
                    ->where([
                        "{$db->qn('field_id')} = {$db->q($field->id)}", // @phpstan-ignore argument.type
                        "{$db->qn('value')} = {$db->q($item->id)}",
                    ]),
            )
            ->loadColumn();

        if (!$ids) {
            return null;
        }

        if ($args['context'] === 'com_content.categories') {
            $categories = Categories::getInstance('content', ['countItems' => true]);
            return CustomCategoriesQueryType::orderCategories(
                array_filter(array_map(fn($id) => $categories->get($id), $ids)),
                $args,
            );
        }

        if ($args['context'] === 'com_users.user') {
            return static::sortUsers(array_map(fn($id) => UserHelper::get($id), $ids), $args);
        }

        return null;
    }

    /**
     * @param list<User> $users
     * @param array<string, mixed> $args
     * @return list<User>
     */
    protected static function sortUsers($users, $args)
    {
        $prop = $args['order'] === 'ordering' ? 'lft' : $args['order'];

        usort($users, fn($article, $other) => strnatcmp($article->$prop, $other->$prop));

        if ($args['offset'] || $args['limit']) {
            $users = array_slice($users, (int) $args['offset'], (int) $args['limit'] ?: null);
        }

        if ($args['order_direction'] === 'DESC') {
            $users = array_reverse($users);
        }

        return $users;
    }
}
