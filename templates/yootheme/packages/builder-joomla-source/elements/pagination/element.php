<?php

namespace YOOtheme;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Pagination\PaginationObject;
use YOOtheme\Builder\Joomla\Source\ArticleHelper;

return [
    'name' => 'pagination',
    'title' => 'Pagination',
    'group' => 'system',
    'icon' => '${url:images/icon.svg}',
    'iconSmall' => '${url:images/iconSmall.svg}',
    'element' => true,
    'width' => 500,
    'defaults' => [
        'pagination_type' => 'previous/next',
        'text_align' => 'center',
    ],
    'updates' => __DIR__ . '/updates.php',
    'templates' => [
        'render' => __DIR__ . '/templates/template.php',
    ],
    'transforms' => [
        'render' => function ($node, $params) {
            // Single Article
            if (!isset($params['pagination'])) {
                $article = $params['item'] ?? ($params['article'] ?? false);

                if (!$article || !ArticleHelper::applyPageNavigation($article)) {
                    return false;
                }

                $params['pagination'] = [
                    'previous' => $article->prev
                        ? new PaginationObject($article->prev_label, '', null, $article->prev)
                        : null,
                    'next' => $article->next
                        ? new PaginationObject($article->next_label, '', null, $article->next)
                        : null,
                ];
            }

            if (is_callable($params['pagination'])) {
                $params['pagination'] = $params['pagination']();
            }

            if (is_array($params['pagination'])) {
                $node->props['pagination_type'] = 'previous/next';
                $node->props['pagination'] = $params['pagination'];
                return;
            }

            // Article Index
            if (empty($params['pagination']) || $params['pagination']->pagesTotal < 2) {
                return false;
            }

            $list = $params['pagination']->getPaginationPages();

            $total = $params['pagination']->pagesTotal;
            $current = (int) $params['pagination']->pagesCurrent;
            $endSize = 1;
            $midSize = 3;
            $dots = false;

            $pagination = [];

            if ($list['previous']['active']) {
                $pagination['previous'] = $list['previous']['data'];
            }

            $list['start']['data']->text = 1;
            $list['end']['data']->text = $total;

            for ($n = 1; $n <= $total; $n++) {
                $active =
                    $n <= $endSize ||
                    ($current && $n >= $current - $midSize && $n <= $current + $midSize) ||
                    $n > $total - $endSize;

                if ($active || $dots) {
                    if ($active) {
                        $pagination[$n] =
                            $n === 1
                                ? $list['start']['data']
                                : ($n === $total
                                    ? $list['end']['data']
                                    : $list['pages'][$n]['data']);

                        $pagination[$n]->active = $n === $current;
                    } else {
                        $pagination[$n] = new PaginationObject(Text::_('&hellip;'));
                    }

                    $dots = $active;
                }
            }

            if ($list['next']['active']) {
                $pagination['next'] = $list['next']['data'];
            }

            $node->props['pagination'] = $pagination;
        },
    ],
    'fields' => [
        'pagination_type' => [
            'label' => 'Pagination',
            'description' =>
                'Choose between the previous/next or numeric pagination. The numeric pagination is not available for single articles.',
            'type' => 'select',
            'options' => [
                'Previous/Next' => 'previous/next',
                'Numeric' => 'numeric',
            ],
        ],
        'pagination_space_between' => [
            'type' => 'checkbox',
            'text' => 'Show space between links',
            'enable' => 'pagination_type == \'previous/next\'',
        ],
        'position' => '${builder.position}',
        'position_left' => '${builder.position_left}',
        'position_right' => '${builder.position_right}',
        'position_top' => '${builder.position_top}',
        'position_bottom' => '${builder.position_bottom}',
        'position_z_index' => '${builder.position_z_index}',
        'blend' => '${builder.blend}',
        'margin_top' => '${builder.margin_top}',
        'margin_bottom' => '${builder.margin_bottom}',
        'maxwidth' => '${builder.maxwidth}',
        'maxwidth_breakpoint' => '${builder.maxwidth_breakpoint}',
        'block_align' => '${builder.block_align}',
        'block_align_breakpoint' => '${builder.block_align_breakpoint}',
        'block_align_fallback' => '${builder.block_align_fallback}',
        'text_align' => '${builder.text_align}',
        'text_align_breakpoint' => '${builder.text_align_breakpoint}',
        'text_align_fallback' => '${builder.text_align_fallback}',
        'animation' => '${builder.animation}',
        '_parallax_button' => '${builder._parallax_button}',
        'visibility' => '${builder.visibility}',
        'name' => '${builder.name}',
        'status' => '${builder.status}',
        'id' => '${builder.id}',
        'class' => '${builder.cls}',
        'attributes' => '${builder.attrs}',
        'css' => [
            'label' => 'CSS',
            'description' =>
                'Enter your own custom CSS. The following selectors will be prefixed automatically for this element: <code>.el-element</code>',
            'type' => 'editor',
            'editor' => 'code',
            'mode' => 'css',
            'attrs' => [
                'debounce' => 500,
                'hints' => ['.el-element'],
            ],
            'source' => true,
        ],
    ],
    'fieldset' => [
        'default' => [
            'type' => 'tabs',
            'fields' => [
                [
                    'title' => 'Settings',
                    'fields' => [
                        [
                            'label' => 'Pagination',
                            'type' => 'group',
                            'fields' => ['pagination_type', 'pagination_space_between'],
                        ],
                        [
                            'label' => 'General',
                            'type' => 'group',
                            'fields' => [
                                'position',
                                'position_left',
                                'position_right',
                                'position_top',
                                'position_bottom',
                                'position_z_index',
                                'blend',
                                'margin_top',
                                'margin_bottom',
                                'maxwidth',
                                'maxwidth_breakpoint',
                                'block_align',
                                'block_align_breakpoint',
                                'block_align_fallback',
                                'text_align',
                                'text_align_breakpoint',
                                'text_align_fallback',
                                'animation',
                                '_parallax_button',
                                'visibility',
                            ],
                        ],
                    ],
                ],
                '${builder.advanced}',
            ],
        ],
    ],
];
