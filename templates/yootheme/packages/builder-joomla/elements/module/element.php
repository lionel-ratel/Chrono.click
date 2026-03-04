<?php

namespace YOOtheme;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Document\HtmlDocument;
use Joomla\CMS\Document\Renderer\Html\ModuleRenderer;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;

return [
    'name' => 'module',
    'title' => 'Module',
    'group' => 'system',
    'icon' => '${url:images/icon.svg}',
    'iconSmall' => '${url:images/iconSmall.svg}',
    'element' => true,
    'width' => 500,
    'defaults' => [
        'menu_style' => 'default',
        'menu_image_margin' => true,
        'menu_image_align' => 'center',
    ],
    'updates' => __DIR__ . '/updates.php',
    'templates' => [
        'render' => __DIR__ . '/templates/template.php',
    ],
    'transforms' => [
        'render' => function ($node) {
            if (empty($node->props['module'])) {
                return false;
            }

            $module = ModuleHelper::getModuleById((string) $node->props['module']);

            $config = app(Config::class);
            $index = "~theme.modules.{$module->id}";
            $props = $config->get($index, ['class' => []]);

            $node->attrs['class'] = array_merge($node->attrs['class'], $props['class']);
            $node->props = Arr::merge($props, $node->props);

            // override module config with props
            $config->set($index, $node->props);

            // make sure module gets re-rendered in Joomla 4+
            unset($module->contentRendered);

            /** @var CMSApplication $joomla */
            $joomla = Factory::getApplication();

            /** @var HtmlDocument $document */
            $document = $joomla->getDocument();

            /** @var ModuleRenderer $renderer */
            $renderer = $document->loadRenderer('module');

            // render module content
            $node->module = (object) [
                'title' => $module->title,
                'content' => $renderer->render($module),
            ];

            // reset module config
            $config->set($index, $props);

            // return false, if no module content was found
            if (empty($node->module->content)) {
                return false;
            }
        },
    ],
    'fields' => [
        'module' => [
            'type' => 'select-item',
            'label' => 'Module',
            'route' => 'joomla/modules',
            'description' => 'Any Joomla module can be displayed in your custom layout.',
        ],
        '_edit_button' => [
            'type' => 'button',
            'text' => 'Edit Module',
            'attrs' => [
                'class' => 'uk-margin-medium-top uk-display-block',
            ],
            'event' => 'openEditModule',
            'enable' => 'module && yootheme.customizer.module.canCreate',
        ],
        'style' => [
            'type' => 'select',
            'label' => 'Style',
            'description' => 'Select a panel style.',
            'options' => [
                'None' => '',
                'Card Default' => 'card-default',
                'Card Primary' => 'card-primary',
                'Card Secondary' => 'card-secondary',
                'Card Hover' => 'card-hover',
            ],
        ],
        'title_style' => [
            'type' => 'select',
            'label' => 'Style',
            'description' =>
                'Title styles differ in font-size but may also come with a predefined color, size and font.',
            'options' => [
                'None' => '',
                'Heading 3X-Large' => 'heading-3xlarge',
                'Heading 2X-Large' => 'heading-2xlarge',
                'Heading X-Large' => 'heading-xlarge',
                'Heading Large' => 'heading-large',
                'Heading Medium' => 'heading-medium',
                'Heading Small' => 'heading-small',
                'Heading H1' => 'h1',
                'Heading H2' => 'h2',
                'Heading H3' => 'h3',
                'Heading H4' => 'h4',
                'Heading H5' => 'h5',
                'Heading H6' => 'h6',
                'Text Meta' => 'meta',
                'Text Lead' => 'lead',
            ],
        ],
        'title_decoration' => [
            'type' => 'select',
            'label' => 'Decoration',
            'description' =>
                'Decorate the title with a divider, bullet or a line that is vertically centered to the heading.',
            'options' => [
                'None' => '',
                'Divider' => 'divider',
                'Bullet' => 'bullet',
                'Line' => 'line',
            ],
        ],
        'title_font_family' => [
            'label' => 'Font Family',
            'description' =>
                'Select an alternative font family. Mind that not all styles have different font families.',
            'type' => 'select',
            'options' => [
                'None' => '',
                'Default' => 'default',
                'Primary' => 'primary',
                'Secondary' => 'secondary',
                'Tertiary' => 'tertiary',
            ],
        ],
        'title_color' => [
            'type' => 'select',
            'label' => 'Color',
            'description' =>
                'Select the text color. If the Background option is selected, styles that don\'t apply a background image use the primary color instead.',
            'options' => [
                'None' => '',
                'Muted' => 'muted',
                'Primary' => 'primary',
                'Success' => 'success',
                'Warning' => 'warning',
                'Danger' => 'danger',
                'Background' => 'background',
            ],
        ],
        'list_style' => [
            'label' => 'List Style',
            'description' => 'Select the list style.',
            'type' => 'select',
            'options' => [
                'None' => '',
                'Divider' => 'divider',
            ],
        ],
        'link_style' => [
            'label' => 'Link Style',
            'description' => 'Select the link style.',
            'type' => 'select',
            'options' => [
                'None' => '',
                'Muted' => 'muted',
            ],
        ],
        'menu_type' => [
            'label' => 'Type',
            'description' => 'Select the menu type.',
            'type' => 'select',
            'default' => 'nav',
            'options' => [
                'Nav' => 'nav',
                'Subnav' => 'subnav',
                'Iconnav' => 'iconnav',
            ],
        ],
        'menu_divider' => [
            'label' => 'Divider',
            'description' => 'Show optional dividers between nav or subnav items.',
            'type' => 'checkbox',
            'text' => 'Show dividers',
        ],
        'menu_style' => [
            'label' => 'Style',
            'description' => 'Select the nav style.',
            'type' => 'select',
            'options' => [
                'Default' => 'default',
                'Primary' => 'primary',
                'Secondary' => 'secondary',
            ],
        ],
        'menu_size' => [
            'label' => 'Primary Size',
            'description' => 'Select the primary nav size.',
            'type' => 'select',
            'options' => [
                'Default' => '',
                'Medium' => 'medium',
                'Large' => 'large',
                'X-Large' => 'xlarge',
            ],
            'enable' => 'menu_style == \'primary\'',
        ],
        'menu_image_width' => [
            'attrs' => [
                'placeholder' => 'auto',
            ],
        ],
        'menu_image_height' => [
            'attrs' => [
                'placeholder' => 'auto',
            ],
        ],
        'menu_image_svg_inline' => [
            'label' => 'Inline SVG',
            'description' =>
                'Inject SVG images into the markup so they adopt the text color automatically.',
            'type' => 'checkbox',
            'text' => 'Make SVG stylable with CSS',
        ],
        'menu_image_margin' => [
            'label' => 'Image and Title',
            'type' => 'checkbox',
            'text' => 'Add margin between',
        ],
        'menu_image_align' => [
            'label' => 'Image Align',
            'type' => 'select',
            'options' => [
                'Top' => 'top',
                'Center' => 'center',
            ],
        ],
        'language_parent_icon' => [
            'label' => 'Dropdown',
            'text' => 'Show parent icon',
            'type' => 'checkbox',
            'default' => true,
        ],
        'language_icon' => [
            'description' => 'Show a general icon instead of a specific flag icon.',
            'text' => 'Show language icon',
            'type' => 'checkbox',
            'default' => true,
        ],
        'language_icon_width' => [
            'label' => 'Dropdown Icon Width',
        ],
        'language_icon_margin' => [
            'type' => 'checkbox',
            'text' => 'Add margin between',
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
        'text_align' => '${builder.text_align_justify}',
        'text_align_breakpoint' => '${builder.text_align_breakpoint}',
        'text_align_fallback' => '${builder.text_align_justify_fallback}',
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
                'Enter your own custom CSS. The following selectors will be prefixed automatically for this element: <code>.el-element</code>, <code>.el-title</code>',
            'type' => 'editor',
            'editor' => 'code',
            'mode' => 'css',
            'attrs' => [
                'debounce' => 500,
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
                    'title' => 'Content',
                    'fields' => ['module', '_edit_button'],
                ],
                [
                    'title' => 'Settings',
                    'fields' => [
                        [
                            'label' => 'Panel',
                            'type' => 'group',
                            'divider' => true,
                            'fields' => ['style'],
                        ],
                        [
                            'label' => 'Title',
                            'type' => 'group',
                            'divider' => true,
                            'fields' => [
                                'title_style',
                                'title_decoration',
                                'title_font_family',
                                'title_color',
                            ],
                        ],
                        [
                            'label' => 'List',
                            'type' => 'group',
                            'divider' => true,
                            'fields' => ['list_style', 'link_style'],
                            'show' =>
                                '$match(type, \'articles_(archive|categories|latest|popular)|tags_(popular|similar)\')',
                        ],
                        [
                            'label' => 'Menu',
                            'type' => 'group',
                            'divider' => true,
                            'fields' => [
                                'menu_type',
                                'menu_divider',
                                'menu_style',
                                'menu_size',
                                [
                                    'label' => 'Image Width/Height',
                                    'description' =>
                                        'Setting just one value preserves the original proportions. The image will be resized and cropped automatically, and where possible, high resolution images will be auto-generated.',
                                    'type' => 'grid',
                                    'width' => '1-2',
                                    'fields' => ['menu_image_width', 'menu_image_height'],
                                ],
                                'menu_image_svg_inline',
                                'menu_image_margin',
                                'menu_image_align',
                            ],
                            'show' => '$match(type, \'menu\')',
                        ],
                        [
                            'label' => 'Language Switcher',
                            'type' => 'group',
                            'divider' => true,
                            'fields' => [
                                'language_parent_icon',
                                'language_icon',
                                'language_icon_width',
                                'language_icon_margin',
                            ],
                            'show' => '$match(type, \'languages\')',
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
