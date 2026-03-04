<?php

namespace YOOtheme\Builder\Joomla\Source\Listener;

use YOOtheme\Builder\Joomla\Source\Type;
use YOOtheme\Builder\Source;
use YOOtheme\Builder\Source\Type\RequestType;
use YOOtheme\Builder\Source\Type\SiteType;

class LoadSourceTypes
{
    /**
     * @param Source $source
     */
    public static function handle($source): void
    {
        $query = [
            [Type\ArticleQueryType::class, 'config'],
            [Type\CategoryQueryType::class, 'config'],
            [Type\ContactQueryType::class, 'config'],
            [Type\ArticlesQueryType::class, 'config'],
            [Type\SmartSearchQueryType::class, 'config'],
            [Type\SmartSearchItemsQueryType::class, 'config'],
            [Type\TagsQueryType::class, 'config'],
            [Type\TagItemsQueryType::class, 'config'],
            [Type\CustomArticleQueryType::class, 'config'],
            [Type\CustomArticlesQueryType::class, 'config'],
            [Type\CustomCategoryQueryType::class, 'config'],
            [Type\CustomCategoriesQueryType::class, 'config'],
            [Type\CustomTagQueryType::class, 'config'],
            [Type\CustomTagsQueryType::class, 'config'],
            [Type\CustomMenuItemQueryType::class, 'config'],
            [Type\CustomMenuItemsQueryType::class, 'config'],
            [Type\CustomUserQueryType::class, 'config'],
            [Type\CustomUsersQueryType::class, 'config'],
            [Type\SiteQueryType::class, 'config'],
        ];

        $types = [
            ['Article', [Type\ArticleType::class, 'config']],
            ['ArticleEvent', [Type\ArticleEventType::class, 'config']],
            ['ArticleImages', [Type\ArticleImagesType::class, 'config']],
            ['ArticleUrls', [Type\ArticleUrlsType::class, 'config']],
            ['Category', [Type\CategoryType::class, 'config']],
            ['CategoryParams', [Type\CategoryParamsType::class, 'config']],
            ['Contact', [Type\ContactType::class, 'config']],
            ['Event', [Type\EventType::class, 'config']],
            ['Images', [Type\ImagesType::class, 'config']],
            ['MenuItem', [Type\MenuItemType::class, 'config']],
            ['Request', [RequestType::class, 'config']],
            ['Site', [SiteType::class, 'config']],
            ['SmartSearch', [Type\SmartSearchType::class, 'config']],
            ['SmartSearchItem', [Type\SmartSearchItemType::class, 'config']],
            ['Tag', [Type\TagType::class, 'config']],
            ['TagItem', [Type\TagItemType::class, 'config']],
            ['User', [Type\UserType::class, 'config']],
        ];

        foreach ($query as $args) {
            $source->queryType($args);
        }

        foreach ($types as $args) {
            $source->objectType(...$args);
        }
    }
}
