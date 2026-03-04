<?php

namespace YOOtheme\Builder\Joomla\Source\Type;

use Joomla\CMS\Categories\Categories;
use Joomla\CMS\Categories\CategoryNode;
use Joomla\CMS\Helper\TagsHelper;
use Joomla\CMS\User\User;
use Joomla\Component\Content\Site\Helper\RouteHelper;
use YOOtheme\Builder\Joomla\MenuElement;
use YOOtheme\Builder\Joomla\Source\ArticleHelper;
use YOOtheme\Builder\Joomla\Source\TagHelper;
use YOOtheme\Builder\Source;
use YOOtheme\Path;
use YOOtheme\View;
use function YOOtheme\app;
use function YOOtheme\trans;

/**
 * @phpstan-import-type ObjectConfig from Source
 */
class CategoryType
{
    /**
     * @return ObjectConfig
     */
    public static function config(): array
    {
        return [
            'fields' => [
                'title' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Title'),
                        'filters' => ['limit', 'preserve'],
                    ],
                ],

                'description' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Description'),
                        'filters' => ['limit', 'preserve'],
                    ],
                ],

                'params' => [
                    'type' => 'CategoryParams',
                    'metadata' => [
                        'label' => '',
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::params',
                    ],
                ],

                'link' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Link'),
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::link',
                    ],
                ],

                'parent' => [
                    'type' => 'Category',
                    'metadata' => [
                        'label' => trans('Parent Category'),
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::parent',
                    ],
                ],

                'tagString' => [
                    'type' => 'String',
                    'args' => [
                        'parent_id' => [
                            'type' => 'String',
                        ],
                        'separator' => [
                            'type' => 'String',
                        ],
                        'show_link' => [
                            'type' => 'Boolean',
                        ],
                        'link_style' => [
                            'type' => 'String',
                        ],
                    ],
                    'metadata' => [
                        'label' => trans('Tags'),
                        'arguments' => [
                            'parent_id' => [
                                'label' => trans('Parent Tag'),
                                'description' => trans(
                                    'Tags are only loaded from the selected parent tag.',
                                ),
                                'type' => 'select',
                                'default' => '0',
                                'options' => [
                                    ['value' => '0', 'text' => trans('Root')],
                                    ['evaluate' => 'yootheme.builder.tags'],
                                ],
                            ],
                            'separator' => [
                                'label' => trans('Separator'),
                                'description' => trans('Set the separator between tags.'),
                                'default' => ', ',
                            ],
                            'show_link' => [
                                'label' => trans('Link'),
                                'type' => 'checkbox',
                                'default' => true,
                                'text' => trans('Show link'),
                            ],
                            'link_style' => [
                                'label' => trans('Link Style'),
                                'description' => trans('Set the link style.'),
                                'type' => 'select',
                                'default' => '',
                                'options' => [
                                    'Default' => '',
                                    'Muted' => 'link-muted',
                                    'Text' => 'link-text',
                                    'Heading' => 'link-heading',
                                    'Reset' => 'link-reset',
                                ],
                                'enable' => 'arguments.show_link',
                            ],
                        ],
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::tagString',
                    ],
                ],

                'categories' => [
                    'type' => [
                        'listOf' => 'Category',
                    ],
                    'metadata' => [
                        'label' => trans('Child Categories'),
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::categories',
                    ],
                ],

                'articles' => [
                    'type' => [
                        'listOf' => 'Article',
                    ],
                    'args' => [
                        'subcategories' => [
                            'type' => 'Boolean',
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
                        'label' => trans('Articles'),
                        'arguments' => [
                            'subcategories' => [
                                'label' => trans('Filter'),
                                'text' => trans('Include articles from child categories'),
                                'type' => 'checkbox',
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
                        'directives' => [],
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::articles',
                    ],
                ],

                'tags' => [
                    'type' => [
                        'listOf' => 'Tag',
                    ],
                    'args' => [
                        'parent_id' => [
                            'type' => 'String',
                        ],
                    ],
                    'metadata' => [
                        'label' => trans('Tags'),
                        'fields' => [
                            'parent_id' => [
                                'label' => trans('Parent Tag'),
                                'description' => trans(
                                    'Tags are only loaded from the selected parent tag.',
                                ),
                                'type' => 'select',
                                'default' => '0',
                                'options' => [
                                    ['value' => '0', 'text' => trans('Root')],
                                    ['evaluate' => 'yootheme.builder.tags'],
                                ],
                            ],
                        ],
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::tags',
                    ],
                ],

                'numitems' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Article Count'),
                    ],
                ],

                'alias' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Alias'),
                    ],
                ],

                'active' => [
                    'type' => 'Boolean',
                    'metadata' => [
                        'label' => trans('Active'),
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::active',
                    ],
                ],

                'id' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('ID'),
                    ],
                ],
            ],

            'metadata' => [
                'type' => true,
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function params(CategoryNode $category): array
    {
        return $category->getParams()->toArray();
    }

    public static function link(CategoryNode $category): string
    {
        // @phpstan-ignore argument.type
        return RouteHelper::getCategoryRoute($category->id, $category->language);
    }

    public static function parent(CategoryNode $category): ?CategoryNode
    {
        /** @var ?CategoryNode $parent */
        $parent = $category->getParent();

        // @phpstan-ignore notIdentical.alwaysTrue
        return $parent && $parent->id !== 'root' ? $parent : null;
    }

    /**
     * @return CategoryNode[]
     */
    public static function categories(CategoryNode $category): array
    {
        $groups = app(User::class)->getAuthorisedViewLevels();

        return array_filter(
            $category->getChildren(),
            fn($child) => in_array($child->access, $groups),
        );
    }

    /**
     * @param array<string, mixed> $args
     * @return array<object>
     */
    public static function articles(CategoryNode $category, array $args)
    {
        return ArticleHelper::query(['catid' => $category->id] + $args);
    }

    /**
     * @param array<string, mixed> $args
     * @return int|array<object>|array<null>
     */
    public static function tags(CategoryNode $category, array $args)
    {
        $tags =
            $category->tags->itemTags ??
            (new TagsHelper())->getItemTags('com_content.category', $category->id);

        if (!empty($args['parent_id'])) {
            return TagHelper::filterTags($tags, $args['parent_id']);
        }

        return $tags;
    }

    /**
     * @param array<string, mixed> $args
     */
    public static function tagString(CategoryNode $category, array $args): string
    {
        $tags = static::tags($category, $args);
        $args += [
            'separator' => ', ',
            'show_link' => true,
            'link_style' => '',
        ];

        return app(View::class)->render(
            Path::join(__DIR__, '../../templates/tags'),
            compact('category', 'tags', 'args'),
        );
    }

    public static function active(CategoryNode $category): bool
    {
        $id = MenuElement::getCategoryFromRequest();

        if (!$id) {
            return false;
        }

        if ($id === $category->id) {
            return true;
        }

        $child = Categories::getInstance('content')->get($id);

        return $child->lft > $category->lft && $child->rgt < $category->rgt;
    }
}
