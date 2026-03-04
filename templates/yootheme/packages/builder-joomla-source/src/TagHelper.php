<?php

namespace YOOtheme\Builder\Joomla\Source;

use YOOtheme\Builder\Joomla\Source\Model\TagModel;
use YOOtheme\Builder\Joomla\Source\Model\TagsModel;
use function YOOtheme\app;

class TagHelper
{
    /**
     * @param array<string, mixed> $args
     * @return list<object>
     */
    public static function query(array $args = []): array
    {
        return app(TagsModel::class)->getItems($args);
    }

    /**
     * @param array<string, mixed> $args
     * @return list<object>
     */
    public static function getItems(int $tagId, array $args = []): array
    {
        return app(TagModel::class)->getItems($tagId, $args);
    }

    /**
     * @param array<object> $tags
     * @param array<int> $parentId
     * @return list<object>
     */
    public static function filterTags($tags, $parentId): array
    {
        $parent = array_first(static::query(['ids' => $parentId]));

        return $parent
            ? array_filter($tags, fn($tag) => $tag->lft > $parent->lft && $tag->rgt < $parent->rgt)
            : [];
    }
}
