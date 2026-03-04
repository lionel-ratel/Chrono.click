<?php

namespace YOOtheme\Builder\Joomla\Source\Type;

use Joomla\CMS\Categories\Categories;
use Joomla\CMS\Categories\CategoryNode;
use Joomla\CMS\User\User;
use Joomla\Component\Tags\Site\Helper\RouteHelper;
use stdClass;
use YOOtheme\Builder\Joomla\Source\UserHelper;
use YOOtheme\Builder\Source;
use YOOtheme\Path;
use YOOtheme\View;
use function YOOtheme\app;
use function YOOtheme\trans;

/**
 * @phpstan-import-type ObjectConfig from Source
 */
class TagItemType
{
    /**
     * @return ObjectConfig
     */
    public static function config(): array
    {
        return [
            'fields' => [
                'core_title' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Title'),
                        'filters' => ['limit', 'preserve'],
                    ],
                ],

                'content' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Content'),
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
                    ],
                    'metadata' => [
                        'label' => trans('Teaser'),
                        'arguments' => [
                            'show_excerpt' => [
                                'label' => trans('Excerpt'),
                                'description' => trans(
                                    'Display the excerpt field if it has content, otherwise the content. To use an excerpt field, create a custom field with the name excerpt.',
                                ),
                                'type' => 'checkbox',
                                'default' => true,
                                'text' => trans('Prefer excerpt over regular text'),
                            ],
                        ],
                        'filters' => ['limit', 'preserve'],
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::teaser',
                    ],
                ],

                'core_publish_up' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Published Date'),
                        'filters' => ['date'],
                    ],
                ],

                'core_created_time' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Created Date'),
                        'filters' => ['date'],
                    ],
                ],

                'core_modified_time' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Modified Date'),
                        'filters' => ['date'],
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

                'images' => [
                    'type' => 'Images',
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

                'event' => [
                    'type' => 'ArticleEvent',
                    'metadata' => [
                        'label' => trans('Events'),
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::event',
                    ],
                ],

                'content_type_title' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Content Type Title'),
                    ],
                ],

                'core_alias' => [
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
            ],

            'metadata' => [
                'type' => true,
            ],
        ];
    }

    public static function content(object $item): string
    {
        return $item->core_body ?? '';
    }

    public static function link(object $item): string
    {
        return RouteHelper::getItemRoute(
            $item->content_item_id,
            $item->core_alias,
            $item->core_catid,
            $item->core_language,
            $item->type_alias,
            $item->router,
        );
    }

    /**
     * @param array<string, mixed> $args
     */
    public static function teaser(object $item, array $args): string
    {
        $args += ['show_excerpt' => true];

        if ($args['show_excerpt'] && !empty($item->jcfields['excerpt']->rawvalue)) {
            return $item->jcfields['excerpt']->rawvalue;
        }

        return $item->core_body ?? '';
    }

    /**
     * @param array<string, mixed> $args
     */
    public static function metaString(object $item, array $args): ?string
    {
        if ($item->type_alias !== 'com_content.article') {
            return null;
        }

        $args += [
            'format' => 'list',
            'separator' => '|',
            'link_style' => '',
            'show_publish_date' => true,
            'show_author' => true,
            'show_taxonomy' => 'category',
            'date_format' => '',
        ];

        $props = [
            'id',
            'author',
            'contact_link',
            'core_catid' => 'catid',
            'category_title',
            'core_created_user_id' => 'created_by',
            'core_created_by_alias' => 'created_by_alias',
            'core_publish_up' => 'publish_up',
        ];

        $article = new stdClass();
        foreach ($props as $field => $prop) {
            $article->$prop = $item->$prop ?? ($item->$field ?? null);
        }

        $tags = $args['show_taxonomy'] === 'tag' ? ArticleType::tags($article, $args) : null;

        return app(View::class)->render(
            Path::join(__DIR__, '../../templates/meta'),
            compact('article', 'tags', 'args'),
        );
    }

    /**
     * @return mixed
     */
    public static function images(object $item)
    {
        return json_decode($item->core_images);
    }

    public static function author(object $item): User
    {
        $user = UserHelper::get($item->core_created_user_id);

        if ($item->core_created_by_alias) {
            $user = clone $user;
            $user->name = $item->core_created_by_alias;
        }

        return $user;
    }

    public static function category(object $item): ?CategoryNode
    {
        return isset($item->catid)
            ? Categories::getInstance('content', ['countItems' => true])->get($item->catid)
            : null;
    }

    public static function event(object $item): ?object
    {
        return isset($item->event) ? $item : null;
    }
}
