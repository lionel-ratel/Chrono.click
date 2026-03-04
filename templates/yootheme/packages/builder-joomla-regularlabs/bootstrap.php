<?php

namespace YOOtheme\Builder\Joomla\RegularLabs;

return [
    'events' => [
        'source.init' => [
            Listener\LoadSourceTypes::class => ['handle', -20],
        ],

        'source.com_fields.field' => [
            Listener\ArticlesField::class => 'config',
        ],
    ],
];
