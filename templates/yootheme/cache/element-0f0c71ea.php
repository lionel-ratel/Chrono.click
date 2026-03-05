<?php // $file = /Users/lionelratel/Sites/WebProjects/chrono.click/public_html/plugins/system/yooseblod/modules/module-seblod-joomla/elements/seblod-list/element.json

return [
  '@import' => $filter->apply('path', './element.php', $file),
  'name' => 'seblod-list',
  'title' => 'Seblod List',
  'group' => 'SEBLOD',
  'icon' => $filter->apply('url', 'images/icon.svg', $file),
  'iconSmall' => $filter->apply('url', 'images/iconSmall.svg', $file),
  'element' => true,
  'width' => 500,
  'defaults' => [],
  'templates' => [
    'render' => $filter->apply('path', './templates/template.php', $file),
    'content' => $filter->apply('path', './templates/content.php', $file)
  ],
  'placeholder' => [
    'props' => [
      'list' => 'o_article...'
    ]
  ],
  'fields' => [
    'list' => [
      'label' => 'SEBLOD List',
      'source' => true
    ],
    'position' => $config->get('builder.position'),
    'position_left' => $config->get('builder.position_left'),
    'position_right' => $config->get('builder.position_right'),
    'position_top' => $config->get('builder.position_top'),
    'position_bottom' => $config->get('builder.position_bottom'),
    'position_z_index' => $config->get('builder.position_z_index'),
    'margin' => $config->get('builder.margin'),
    'margin_remove_top' => $config->get('builder.margin_remove_top'),
    'margin_remove_bottom' => $config->get('builder.margin_remove_bottom'),
    'maxwidth' => $config->get('builder.maxwidth'),
    'maxwidth_breakpoint' => $config->get('builder.maxwidth_breakpoint'),
    'block_align' => $config->get('builder.block_align'),
    'block_align_breakpoint' => $config->get('builder.block_align_breakpoint'),
    'block_align_fallback' => $config->get('builder.block_align_fallback'),
    'text_align' => $config->get('builder.text_align_justify'),
    'text_align_breakpoint' => $config->get('builder.text_align_breakpoint'),
    'text_align_fallback' => $config->get('builder.text_align_justify_fallback'),
    'animation' => $config->get('builder.animation'),
    '_parallax_button' => $config->get('builder._parallax_button'),
    'visibility' => $config->get('builder.visibility'),
    'name' => $config->get('builder.name'),
    'status' => $config->get('builder.status'),
    'id' => $config->get('builder.id'),
    'class' => $config->get('builder.cls'),
    'attributes' => $config->get('builder.attrs'),
    'css' => [
      'label' => 'CSS',
      'description' => 'Enter your own custom CSS. The following selectors will be prefixed automatically for this element: <code>.el-element</code>, <code>.el-title</code>, <code>.el-content</code>',
      'type' => 'editor',
      'editor' => 'code',
      'mode' => 'css',
      'attrs' => [
        'debounce' => 500
      ]
    ]
  ],
  'fieldset' => [
    'default' => [
      'type' => 'tabs',
      'fields' => [[
          'title' => 'Content',
          'fields' => ['list']
        ],[
          'title' => 'Settings',
          'fields' => [[
              'label' => 'General',
              'type' => 'group',
              'fields' => ['position','position_left','position_right','position_top','position_bottom','position_z_index','margin','margin_remove_top','margin_remove_bottom','maxwidth','maxwidth_breakpoint','block_align','block_align_breakpoint','block_align_fallback','text_align','text_align_breakpoint','text_align_fallback','animation','_parallax_button','visibility']
            ]]
        ],$config->get('builder.advanced')]
    ]
  ]
];
