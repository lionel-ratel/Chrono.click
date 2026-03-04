<?php

namespace YOOtheme\Builder\Joomla\Source\Type;

use YOOtheme\Builder\Joomla\Source\ArticleHelper;
use YOOtheme\Builder\Source;
use function YOOtheme\trans;

/**
 * @phpstan-import-type Article from ArticleHelper
 * @phpstan-import-type ObjectConfig from Source
 */
class CustomArticlesQueryType
{
    /**
     * @return ObjectConfig
     */
    public static function config(): array
    {
        return [
            'fields' => [
                'customArticles' => [
                    'type' => [
                        'listOf' => 'Article',
                    ],

                    'args' => [
                        'catid' => [
                            'type' => [
                                'listOf' => 'String',
                            ],
                        ],
                        'cat_operator' => [
                            'type' => 'String',
                        ],
                        'include_child_categories' => [
                            'type' => 'String',
                        ],
                        'tags' => [
                            'type' => [
                                'listOf' => 'String',
                            ],
                        ],
                        'tag_operator' => [
                            'type' => 'String',
                        ],
                        'include_child_tags' => [
                            'type' => 'String',
                        ],
                        'users' => [
                            'type' => [
                                'listOf' => 'String',
                            ],
                        ],
                        'users_operator' => [
                            'type' => 'String',
                        ],
                        'featured' => [
                            'type' => 'String',
                        ],
                        'date_column' => [
                            'type' => 'String',
                        ],
                        'date_range' => [
                            'type' => 'String',
                        ],
                        'date_relative' => [
                            'type' => 'String',
                        ],
                        'date_relative_value' => [
                            'type' => 'Int',
                        ],
                        'date_relative_unit' => [
                            'type' => 'String',
                        ],
                        'date_relative_unit_this' => [
                            'type' => 'String',
                        ],
                        'date_relative_start_today' => [
                            'type' => 'Boolean',
                        ],
                        'date_start' => [
                            'type' => 'String',
                        ],
                        'date_end' => [
                            'type' => 'String',
                        ],
                        'date_start_custom' => [
                            'type' => 'String',
                        ],
                        'date_end_custom' => [
                            'type' => 'String',
                        ],
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
                        'label' => trans('Custom Articles'),
                        'group' => trans('Custom'),
                        'fields' => [
                            'catid' => [
                                'label' => trans('Filter by Categories'),
                                'type' => 'select',
                                'default' => [],
                                'options' => [['evaluate' => 'yootheme.builder.categories']],
                                'attrs' => [
                                    'multiple' => true,
                                    'class' => 'uk-height-small',
                                ],
                            ],
                            'cat_operator' => [
                                'type' => 'select',
                                'default' => 'IN',
                                'options' => [
                                    trans('Match (OR)') => 'IN',
                                    trans('Don\'t match (NOR)') => 'NOT IN',
                                ],
                            ],
                            'include_child_categories' => [
                                'type' => 'select',
                                'description' => trans(
                                    'Filter articles by categories. Use the <kbd>shift</kbd> or <kbd>ctrl/cmd</kbd> key to select multiple categories. Set the logical operator to match or not match the selected categories.',
                                ),
                                'options' => [
                                    trans('Exclude child categories') => '',
                                    trans('Include child categories') => 'include',
                                    trans('Only include child categories') => 'only',
                                ],
                            ],
                            'tags' => [
                                'label' => trans('Filter by Tags'),
                                'type' => 'select',
                                'default' => [],
                                'options' => [['evaluate' => 'yootheme.builder.tags']],
                                'attrs' => [
                                    'multiple' => true,
                                    'class' => 'uk-height-small',
                                ],
                            ],
                            'tag_operator' => [
                                'type' => 'select',
                                'default' => 'IN',
                                'options' => [
                                    trans('Match one (OR)') => 'IN',
                                    trans('Match all (AND)') => 'AND',
                                    trans('Don\'t match (NOR)') => 'NOT IN',
                                ],
                            ],
                            'include_child_tags' => [
                                'type' => 'select',
                                'description' => trans(
                                    'Filter articles by tags. Use the <kbd>shift</kbd> or <kbd>ctrl/cmd</kbd> key to select multiple tags. Set the logical operator to match at least one of the tags, none of the tags or all tags.',
                                ),
                                'options' => [
                                    trans('Exclude child tags') => '',
                                    trans('Include child tags') => 'include',
                                    trans('Only include child tags') => 'only',
                                ],
                            ],
                            'users' => [
                                'label' => trans('Filter by Authors'),
                                'type' => 'select',
                                'default' => [],
                                'options' => [['evaluate' => 'yootheme.builder.authors']],
                                'attrs' => [
                                    'multiple' => true,
                                    'class' => 'uk-height-small',
                                ],
                            ],
                            'users_operator' => [
                                'description' => trans(
                                    'Filter articles by authors. Use the <kbd>shift</kbd> or <kbd>ctrl/cmd</kbd> key to select multiple authors. Set the logical operator to match or not match the selected authors.',
                                ),
                                'type' => 'select',
                                'default' => 'IN',
                                'options' => [
                                    trans('Match (OR)') => 'IN',
                                    trans('Don\'t match (NOR)') => 'NOT IN',
                                ],
                            ],
                            'featured' => [
                                'label' => trans('Filter by Featured Articles'),
                                'description' => trans(
                                    'Filter articles by featured status. Load all articles, featured articles only, or articles which are not featured.',
                                ),
                                'type' => 'select',
                                'options' => [
                                    'None' => '',
                                    'Featured only' => 'only',
                                    'Not featured' => 'hide',
                                ],
                            ],
                            '_date' => [
                                'label' => trans('Filter by Date'),
                                'description' =>
                                    'Filter articles by a range relative to the current date or by a fixed start and end date.',
                                'type' => 'grid',
                                'width' => '1-2',
                                'fields' => [
                                    'date_column' => [
                                        'type' => 'select',
                                        'options' => [
                                            ['value' => '', 'text' => trans('None')],
                                            [
                                                'evaluate' =>
                                                    'yootheme.builder.sources.articleDateFilterOptions',
                                            ],
                                        ],
                                    ],
                                    'date_range' => [
                                        'type' => 'select',
                                        'default' => 'relative',
                                        'options' => [
                                            trans('Relative Range') => 'relative',
                                            trans('Fixed Range') => 'fixed',
                                            trans('Custom Format Range') => 'custom',
                                        ],
                                        'enable' => 'date_column',
                                    ],
                                ],
                            ],
                            '_date_range_relative' => [
                                'label' => trans('Date Range'),
                                'type' => 'grid',
                                'width' => 'expand,auto,expand',
                                'fields' => [
                                    'date_relative' => [
                                        'type' => 'select',
                                        'default' => 'next',
                                        'options' => [
                                            trans('Is in the next') => 'next',
                                            trans('Is in this') => 'this',
                                            trans('Is in the last') => 'last',
                                        ],
                                    ],
                                    'date_relative_value' => [
                                        'type' => 'limit',
                                        'attrs' => [
                                            'min' => 0,
                                            'class' => 'uk-form-width-xsmall',
                                            'placeholder' => '∞',
                                        ],
                                        'show' => 'date_relative !== \'this\'',
                                    ],
                                    'date_relative_unit' => [
                                        'type' => 'select',
                                        'default' => 'day',
                                        'options' => [
                                            trans('Days') => 'day',
                                            trans('Weeks') => 'week',
                                            trans('Months') => 'month',
                                            trans('Years') => 'year',
                                            trans('Calendar Weeks') => 'week_calendar',
                                            trans('Calendar Months') => 'month_calendar',
                                            trans('Calendar Years') => 'year_calendar',
                                        ],
                                        'show' => 'date_relative !== \'this\'',
                                    ],
                                    'date_relative_unit_this' => [
                                        'type' => 'select',
                                        'default' => 'day',
                                        'options' => [
                                            trans('Day') => 'day',
                                            trans('Week') => 'week',
                                            trans('Month') => 'month',
                                            trans('Year') => 'year',
                                        ],
                                        'show' => 'date_relative === \'this\'',
                                    ],
                                ],
                                'show' => 'date_column && date_range === \'relative\'',
                            ],
                            'date_relative_start_today' => [
                                'type' => 'checkbox',
                                'text' => trans('Start today'),
                                'description' =>
                                    'Set a range starting tomorrow or the next full calendar period. Optionally, start today, which includes the current partial period for calendar ranges. Today refers to the full calendar day.',
                                'enable' => 'date_relative !== \'this\'',
                                'show' => 'date_column && date_range === \'relative\'',
                            ],
                            '_date_range_fixed' => [
                                'type' => 'grid',
                                'description' =>
                                    'Set only one date to load all articles either before or after that date.',
                                'width' => '1-2',
                                'fields' => [
                                    'date_start' => [
                                        'label' => trans('Start Date'),
                                        'type' => 'datetime',
                                    ],
                                    'date_end' => [
                                        'label' => trans('End Date'),
                                        'type' => 'datetime',
                                    ],
                                ],
                                'show' => 'date_column && date_range === \'fixed\'',
                            ],
                            '_date_range_custom' => [
                                'type' => 'grid',
                                'description' =>
                                    'Use the <a href="https://www.php.net/manual/en/datetime.formats.php#datetime.formats.relative" target="_blank">PHP relative date formats</a> in a BNF-like syntax. Set only one date to load all articles either before or after that date.',
                                'width' => '1-2',
                                'fields' => [
                                    'date_start_custom' => [
                                        'label' => trans('Start Date'),
                                        'type' => 'data-list',
                                        'options' => [
                                            'This month' => 'first day of +0 month 00:00:00',
                                            'Next month' => 'first day of +1 month 00:00:00',
                                            'Month after next month' =>
                                                'first day of +2 month 00:00:00',
                                        ],
                                    ],
                                    'date_end_custom' => [
                                        'label' => trans('End Date'),
                                        'type' => 'data-list',
                                        'options' => [
                                            'This month' => 'last day of +0 month 23:59:59',
                                            'Next month' => 'last day of +1 month 23:59:59',
                                            'Month after next month' =>
                                                'last day of +2 month 23:59:59',
                                        ],
                                    ],
                                ],
                                'show' => 'date_column && date_range === \'custom\'',
                            ],
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
                                            'min' => 1,
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
                    ],

                    'extensions' => [
                        'call' => __CLASS__ . '::resolve',
                    ],
                ],
            ],
        ];
    }

    /**
     * @param array<string, mixed> $root
     * @param array<string, mixed> $args
     * @return list<Article>
     */
    public static function resolve($root, array $args)
    {
        return ArticleHelper::query($args);
    }
}
