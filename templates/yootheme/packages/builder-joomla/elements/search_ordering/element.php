<?php

namespace YOOtheme;

use Joomla\CMS\Factory;
use Joomla\Component\Finder\Administrator\Extension\FinderComponent;
use Joomla\Component\Finder\Site\Model\SearchModel;

return [
    'name' => 'search_ordering',
    'title' => 'Search Ordering',
    'group' => 'system',
    'icon' => '${url:images/icon.svg}',
    'iconSmall' => '${url:images/iconSmall.svg}',
    'element' => true,
    'width' => 500,
    'defaults' => [
        'button_style' => 'default',
        'icon_align' => 'left',
    ],
    'templates' => [
        'render' => __DIR__ . '/templates/template.php',
    ],
    'transforms' => [
        'render' => function ($node) {
            $app = Factory::getApplication();

            /** @var FinderComponent $component */
            $component = $app->bootComponent('com_finder');
            /** @var SearchModel $model */
            $model = $component->getMVCFactory()->createModel('Search', 'Site');

            /** Needs to be called once, to populate state */
            $model->getState();
            $sortOrderFields = $model->getSortOrderFields();

            if (empty($sortOrderFields)) {
                return false;
            }

            $node->props['sortOrderFields'] = $sortOrderFields;
        },
    ],
    'fields' => [
        'button_style' => [
            'label' => 'Style',
            'description' => 'Set the button style.',
            'type' => 'select',
            'options' => [
                'Button Default' => 'default',
                'Button Primary' => 'primary',
                'Button Secondary' => 'secondary',
                'Button Danger' => 'danger',
                'Button Text' => 'text',
                'Button Link' => 'link',
            ],
        ],
        'button_size' => [
            'label' => 'Size',
            'type' => 'select',
            'options' => [
                'Small' => 'small',
                'Default' => '',
                'Large' => 'large',
            ],
            'enable' => '!$match(button_style, \'text|link\')'
        ],
        'icon' => [
            'label' => 'Icon',
            'description' => 'Pick an optional icon from the icon library.',
            'type' => 'icon',
            'enable' => '!parent_icon',
        ],
        'parent_icon' => [
            'type' => 'checkbox',
            'text' => 'Show parent icon',
        ],
        'icon_align' => [
            'label' => 'Icon Alignment',
            'description' => 'Choose the icon position.',
            'type' => 'select',
            'options' => [
                'Left' => 'left',
                'Right' => 'right',
            ],
            'enable' => '!parent_icon',
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
        'transform' => '${builder.transform}',
    ],
    'fieldset' => [
        'default' => [
            'type' => 'tabs',
            'fields' => [
                [
                    'title' => 'Settings',
                    'fields' => [
                        [
                            'label' => 'Button',
                            'type' => 'group',
                            'divider' => true,
                            'fields' => [
                                'button_style',
                                'button_size',
                                'icon',
                                'parent_icon',
                                'icon_align',
                            ],
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
