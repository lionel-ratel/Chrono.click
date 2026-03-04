<?php

namespace YOOtheme;

return [
    '3.0.0-beta.7.1' => function ($node) {
        if (
            str_starts_with($node->source->query->name ?? '', 'customArticle') &&
            isset($node->source->query->arguments->featured)
        ) {
            $node->source->query->arguments->featured = empty(
                $node->source->query->arguments->featured
            )
                ? ''
                : 'only';
        }
    },

    '2.4.0-beta.5' => function ($node) {
        // refactor show_category argument into show_taxonomy argument
        foreach ($node->source->props ?? [] as $prop) {
            if (($prop->name ?? '') === 'metaString' && isset($prop->arguments->show_category)) {
                $arguments = $prop->arguments;
                $arguments->show_taxonomy = $arguments->show_category ? 'category' : '';
                unset($arguments->show_category);
            }
        }
    },
];
