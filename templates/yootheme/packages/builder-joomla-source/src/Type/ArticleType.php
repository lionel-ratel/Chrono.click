<?php

namespace YOOtheme\Builder\Joomla\Source\Type;

use Joomla\CMS\Categories\Categories;
use Joomla\CMS\Categories\CategoryNode;
use Joomla\CMS\Helper\TagsHelper;
use Joomla\CMS\User\User;
use Joomla\Component\Content\Site\Helper\RouteHelper;
use YOOtheme\Builder\Joomla\Fields\Type\FieldsType;
use YOOtheme\Builder\Joomla\Source\ArticleHelper;
use YOOtheme\Builder\Joomla\Source\TagHelper;
use YOOtheme\Builder\Joomla\Source\UserHelper;
use YOOtheme\Builder\Source;
use YOOtheme\Path;
use YOOtheme\View;
use function YOOtheme\app;
use function YOOtheme\trans;

/**
 * @phpstan-import-type ObjectConfig from Source
 * @phpstan-import-type Article from ArticleHelper
 */
class ArticleType
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

                'content' => [
                    'type' => 'String',
                    'args' => [
                        'show_intro_text' => [
                            'type' => 'Boolean',
                        ],
                    ],
                    'metadata' => [
                        'label' => trans('Content'),
                        'arguments' => [
                            'show_intro_text' => [
                                'label' => trans('Intro Text'),
                                'description' => trans('Show or hide the intro text.'),
                                'type' => 'checkbox',
                                'default' => true,
                                'text' => trans('Show intro text'),
                            ],
                        ],
                        'filters' => ['limit', 'preserve'],
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::content',
                    ],
                ],

                'teaser' => [
                    'type' => 'String',
                    'args' => [
                        'show_excerpt' => [
                            'type' => 'Boolean',
                        ],
                        'show_content' => [
                            'type' => 'Boolean',
                        ],
                    ],
                    'metadata' => [
                        'label' => trans('Teaser'),
                        'arguments' => [
                            'show_excerpt' => [
                                'label' => trans('Intro Text'),
                                'description' => trans(
                                    'Render the intro text if the read more tag is present. Otherwise, fall back to the full content. Optionally, prefer the excerpt field over the intro text. To use an excerpt field, create a custom field with the name excerpt.',
                                ),
                                'type' => 'checkbox',
                                'default' => true,
                                'text' => trans('Prefer excerpt over intro text'),
                            ],
                            'show_content' => [
                                'type' => 'checkbox',
                                'default' => true,
                                'text' => trans('Fall back to content'),
                            ],
                        ],
                        'filters' => ['limit', 'preserve'],
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::teaser',
                    ],
                ],

                'publish_up' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Published Date'),
                        'filters' => ['date'],
                    ],
                ],

                'created' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Created Date'),
                        'filters' => ['date'],
                    ],
                ],

                'modified' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Modified Date'),
                        'filters' => ['date'],
                    ],
                ],

                'featured' => [
                    'type' => 'Boolean',
                    'metadata' => [
                        'label' => trans('Featured'),
                        'condition' => true,
                    ],
                ],

                'metaString' => [
                    'type' => 'String',
                    'args' => [
                        'format' => [
                            'type' => 'String',
                        ],
                        'separator' => [
                            'type' => 'String',
                        ],
                        'link_style' => [
                            'type' => 'String',
                        ],
                        'show_publish_date' => [
                            'type' => 'Boolean',
                        ],
                        'show_author' => [
                            'type' => 'Boolean',
                        ],
                        'show_taxonomy' => [
                            'type' => 'String',
                        ],
                        'parent_id' => [
                            'type' => 'String',
                        ],
                        'date_format' => [
                            'type' => 'String',
                        ],
                    ],
                    'metadata' => [
                        'label' => trans('Meta'),
                        'arguments' => [
                            'format' => [
                                'label' => trans('Format'),
                                'description' => trans(
                                    'Display the meta text in a sentence or a horizontal list.',
                                ),
                                'type' => 'select',
                                'default' => 'list',
                                'options' => [
                                    trans('List') => 'list',
                                    trans('Sentence') => 'sentence',
                                ],
                            ],
                            'separator' => [
                                'label' => trans('Separator'),
                                'description' => trans('Set the separator between fields.'),
                                'default' => '|',
                                'enable' => 'arguments.format === "list"',
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
                            ],
                            'show_publish_date' => [
                                'label' => trans('Display'),
                                'description' => trans('Show or hide fields in the meta text.'),
                                'type' => 'checkbox',
                                'default' => true,
                                'text' => trans('Show date'),
                            ],
                            'show_author' => [
                                'type' => 'checkbox',
                                'default' => true,
                                'text' => trans('Show author'),
                            ],
                            'show_taxonomy' => [
                                'type' => 'select',
                                'default' => 'category',
                                'options' => [
                                    trans('Hide Term List') => '',
                                    trans('Show Category') => 'category',
                                    trans('Show Tags') => 'tag',
                                ],
                            ],
                            'parent_id' => [
                                'label' => trans('Parent Tag'),
                                'description' => trans(
                                    'Tags are only loaded from the selected parent tag.',
                                ),
                                'type' => 'select',
                                'default' => '0',
                                'show' => 'arguments.show_taxonomy === "tag"',
                                'options' => [
                                    ['value' => '0', 'text' => trans('Root')],
                                    ['evaluate' => 'yootheme.builder.tags'],
                                ],
                            ],
                            'date_format' => [
                                'label' => trans('Date Format'),
                                'description' => trans(
                                    'Select a predefined date format or enter a custom format.',
                                ),
                                'type' => 'data-list',
                                'default' => '',
                                'options' => [
                                    'Aug 6, 1999 (M j, Y)' => 'M j, Y',
                                    'August 06, 1999 (F d, Y)' => 'F d, Y',
                                    '08/06/1999 (m/d/Y)' => 'm/d/Y',
                                    '08.06.1999 (m.d.Y)' => 'm.d.Y',
                                    '6 Aug, 1999 (j M, Y)' => 'j M, Y',
                                    'Tuesday, Aug 06 (l, M d)' => 'l, M d',
                                ],
                                'enable' => 'arguments.show_publish_date',
                                'attrs' => [
                                    'placeholder' => 'Default',
                                ],
                            ],
                        ],
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::metaString',
                    ],
                ],

                'category' => [
                    'type' => 'Category',
                    'metadata' => [
                        'label' => trans('Category'),
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::category',
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

                'images' => [
                    'type' => 'ArticleImages',
                    'metadata' => [
                        'label' => '',
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::images',
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

                'author' => [
                    'type' => 'User',
                    'metadata' => [
                        'label' => trans('Author'),
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::author',
                    ],
                ],

                'hits' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Hits'),
                    ],
                ],

                'rating' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Rating'),
                    ],
                ],

                'rating_count' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Votes'),
                    ],
                ],

                'urls' => [
                    'type' => 'ArticleUrls',
                    'metadata' => [
                        'label' => trans('Links ABC'),
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::urls',
                    ],
                ],

                'event' => [
                    'type' => 'ArticleEvent',
                    'metadata' => [
                        'label' => trans('System Events'),
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::event',
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
                        ],
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::tags',
                    ],
                ],

                'alias' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Alias'),
                    ],
                ],

                'id' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('ID'),
                    ],
                ],

                'relatedArticles' => [
                    'type' => ['listOf' => 'Article'],
                    'args' => [
                        'category' => [
                            'type' => 'String',
                        ],
                        'tags' => [
                            'type' => 'String',
                        ],
                        'author' => [
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
                        'label' => trans('Related Articles'),
                        'arguments' => [
                            'category' => [
                                'label' => trans('Relationship'),
                                'type' => 'select',
                                'default' => 'IN',
                                'options' => [
                                    trans('Ignore category') => '',
                                    trans('Match category (OR)') => 'IN',
                                    trans('Don\'t match category (NOR)') => 'NOT IN',
                                ],
                            ],
                            'tags' => [
                                'type' => 'select',
                                'options' => [
                                    trans('Ignore tags') => '',
                                    trans('Match one tag (OR)') => 'IN',
                                    trans('Match all tags (AND)') => 'AND',
                                    trans('Don\'t match tags (NOR)') => 'NOT IN',
                                ],
                            ],
                            'author' => [
                                'description' => trans(
                                    'Set the logical operators for how the articles relate to category, tags and author. Choose between matching at least one term, all terms or none of the terms.',
                                ),
                                'type' => 'select',
                                'options' => [
                                    trans('Ignore author') => '',
                                    trans('Match author (OR)') => 'IN',
                                    trans('Don\'t match author (NOR)') => 'NOT IN',
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
                        'directives' => [],
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::relatedArticles',
                    ],
                ],
            ],

            'metadata' => [
                'type' => true,
            ],
        ];
    }

    /**
     * @param Article $article
     * @param array<string, mixed> $args
     */
    public static function content(object $article, array $args): string
    {
        if (
            !$article->params->get('access-view') &&
            $article->params->get('show_noauth') &&
            app(User::class)->get('guest')
        ) {
            return $article->introtext;
        }

        $args += ['show_intro_text' => true];

        if (isset($article->text) && $args['show_intro_text']) {
            return ($article->toc ?? '') . $article->text;
        }

        if ($article->params->get('show_intro', '1') === '1' && $args['show_intro_text']) {
            return "{$article->introtext} {$article->fulltext}";
        }

        if ($article->fulltext) {
            return $article->fulltext;
        }

        return $article->introtext;
    }

    /**
     * @param Article $article
     * @param array<string, mixed> $args
     */
    public static function teaser(object $article, array $args): string
    {
        $args += ['show_excerpt' => true, 'show_content' => true];

        if (
            $args['show_excerpt'] &&
            ($field = FieldsType::getField('excerpt', $article, 'com_content.article')) &&
            $field->rawvalue != ''
        ) {
            return $field->rawvalue;
        }

        if (!$args['show_content'] && $article->fulltext == '') {
            return '';
        }

        return $article->introtext;
    }

    /**
     * @param Article $article
     */
    public static function link(object $article): string
    {
        return RouteHelper::getArticleRoute(
            "{$article->id}:{$article->alias}", // @phpstan-ignore argument.type
            $article->catid,
            $article->language,
        );
    }

    /**
     * @param Article $article
     * @return mixed
     */
    public static function images(object $article)
    {
        return json_decode($article->images);
    }

    /**
     * @param Article $article
     * @return mixed
     */
    public static function urls(object $article)
    {
        return json_decode($article->urls);
    }

    /**
     * @param Article $article
     */
    public static function author(object $article): User
    {
        $user = UserHelper::get((int) $article->created_by);

        if ($article->created_by_alias) {
            $user = clone $user;
            $user->name = $article->created_by_alias;
        }

        return $user;
    }

    /**
     * @param Article $article
     */
    public static function category(object $article): ?CategoryNode
    {
        return Categories::getInstance('content', ['countItems' => true])->get($article->catid);
    }

    /**
     * @param Article $article
     * @param array<string, mixed> $args
     * @return list<object>
     */
    public static function tags(object $article, $args)
    {
        $tags =
            $article->tags->itemTags ??
            (new TagsHelper())->getItemTags('com_content.article', $article->id);

        if (!empty($args['parent_id'])) {
            return TagHelper::filterTags($tags, $args['parent_id']);
        }

        return $tags;
    }

    public static function event(object $article): object
    {
        return $article;
    }

    /**
     * @param Article $article
     * @param array<string, mixed> $args
     */
    public static function tagString(object $article, array $args): string
    {
        $tags = static::tags($article, $args);
        $args += ['separator' => ', ', 'show_link' => true, 'link_style' => ''];

        return app(View::class)->render(
            Path::join(__DIR__, '../../templates/tags'),
            compact('tags', 'args'),
        );
    }

    /**
     * @param Article $article
     * @param array<string, mixed> $args
     */
    public static function metaString(object $article, array $args): string
    {
        $args += [
            'format' => 'list',
            'separator' => '|',
            'link_style' => '',
            'show_publish_date' => true,
            'show_author' => true,
            'show_taxonomy' => 'category',
            'date_format' => '',
        ];

        $tags = $args['show_taxonomy'] === 'tag' ? static::tags($article, $args) : null;

        return app(View::class)->render(
            Path::join(__DIR__, '../../templates/meta'),
            compact('article', 'tags', 'args'),
        );
    }

    /**
     * @param Article $article
     * @param array<string, mixed> $args
     * @return ?array<object>
     */
    public static function relatedArticles(object $article, array $args): ?array
    {
        $args['article'] = $article->id;
        $args['article_operator'] = 'NOT IN';

        if (!empty($args['category'])) {
            $args['cat_operator'] = $args['category'];
            $args['catid'] = (array) $article->catid;
        }

        if (!empty($args['tags'])) {
            $args['tag_operator'] = $args['tags'];
            $args['tags'] = array_column(static::tags($article, []), 'id');
            if (empty($args['tags'])) {
                return null;
            }
        }

        if (!empty($args['author'])) {
            $args['users'] = $article->created_by;
            $args['users_operator'] = $args['author'];
        }

        return ArticleHelper::query($args);
    }
}
