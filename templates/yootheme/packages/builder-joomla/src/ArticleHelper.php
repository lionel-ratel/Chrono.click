<?php

namespace YOOtheme\Builder\Joomla;

use Joomla\CMS\Factory;
use Joomla\CMS\User\UserFactoryInterface;

/**
 * @phpstan-import-type Article from Source\ArticleHelper
 */
class ArticleHelper
{
    public const PATTERN = '/^<!--\s?(\{.*})\s?-->/';

    public static function matchContent(?string $content): ?string
    {
        return str_contains((string) $content, '<!--') &&
            preg_match(static::PATTERN, $content, $matches)
            ? $matches[1]
            : null;
    }

    /**
     * @param Article $article
     *
     * @return array{contentHash: string, modifiedBy: string}
     */
    public static function getCollision(object $article): array
    {
        $user = Factory::getContainer()
            ->get(UserFactoryInterface::class)
            ->loadUserById($article->modified_by);

        return [
            'contentHash' => md5($article->fulltext . $article->introtext),
            'modifiedBy' => $user->username ?: '',
        ];
    }

    public static function isArticleView(): bool
    {
        $input = Factory::getApplication()->getInput();

        return $input->getCmd('option') === 'com_content' &&
            $input->getCmd('view') === 'article' &&
            $input->getCmd('task', '') === '';
    }
}
