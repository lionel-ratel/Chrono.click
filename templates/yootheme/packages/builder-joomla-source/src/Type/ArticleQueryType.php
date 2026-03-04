<?php

namespace YOOtheme\Builder\Joomla\Source\Type;

use Joomla\Uri\Uri;
use YOOtheme\Builder\Joomla\Source\ArticleHelper;
use YOOtheme\Builder\Source;
use function YOOtheme\trans;

/**
 * @phpstan-import-type ObjectConfig from Source
 */
class ArticleQueryType
{
    /**
     * @var list<string>
     */
    protected static array $view = ['com_content.article'];

    /**
     * @return ObjectConfig
     */
    public static function config(): array
    {
        return [
            'fields' => [
                'article' => [
                    'type' => 'Article',
                    'metadata' => [
                        'label' => trans('Article'),
                        'view' => static::$view,
                        'group' => trans('Page'),
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::resolve',
                    ],
                ],
                'prevArticle' => [
                    'type' => 'Article',
                    'metadata' => [
                        'label' => trans('Previous Article'),
                        'view' => static::$view,
                        'group' => trans('Page'),
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::resolvePreviousArticle',
                    ],
                ],
                'nextArticle' => [
                    'type' => 'Article',
                    'metadata' => [
                        'label' => trans('Next Article'),
                        'view' => static::$view,
                        'group' => trans('Page'),
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::resolveNextArticle',
                    ],
                ],
            ],
        ];
    }

    /**
     * @param array<string, mixed> $root
     * @return mixed|void
     */
    public static function resolve($root)
    {
        if (in_array($root['template'] ?? '', static::$view)) {
            return $root['article'] ?? $root['item'];
        }
    }

    /**
     * @param array<string, mixed> $root
     * @return object|null|void
     */
    public static function resolvePreviousArticle($root)
    {
        $article = static::resolve($root);

        if (!$article) {
            return;
        }

        ArticleHelper::applyPageNavigation($article);

        if (!empty($article->prev)) {
            return static::getArticleFromUrl($article->prev);
        }
    }

    /**
     * @param array<string, mixed> $root
     * @return object|null|void
     */
    public static function resolveNextArticle($root)
    {
        $article = static::resolve($root);

        if (!$article) {
            return;
        }

        ArticleHelper::applyPageNavigation($article);

        if (!empty($article->next)) {
            return static::getArticleFromUrl($article->next);
        }
    }

    protected static function getArticleFromUrl(string $url): ?object
    {
        $id = (new Uri($url))->getVar('id', '0');

        return $id ? array_first(ArticleHelper::get($id)) : null;
    }
}
