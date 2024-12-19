<?php

declare(strict_types=1);

namespace Kanemenicou\UrlHelper;

use Symfony\Component\String\UnicodeString;
use function Symfony\Component\String\s;

final class Url extends UnicodeString
{
    public function addQueryParameter(string $name, mixed $value): static
    {
        $originalQuery = s(parse_url($this->string, PHP_URL_QUERY));
        $newQuery = $originalQuery
            ->append($originalQuery->isEmpty() ? '?' : '&')
            ->append(urlencode($name))
            ->append('=')
            ->append(urlencode($value))
        ;

        return new Url((string)$this->replace((string)$originalQuery, (string)$newQuery));
    }

    public function removeQueryParameter(string $name): static
    {
        return new Url(
            (string)$this
                ->replaceMatches('/[&]' . $name . '=[^&]*/', '')
                ->replaceMatches('/' . $name . '=[^&]*[&]{0,}/', '')
                ->trimSuffix('?'),
        );
    }

    public function trimTrailingSlash(): static
    {
        return new Url((string)$this->trimSuffix('/'));
    }

    public function appendTrailingSlash(): static
    {
        return new Url((string)$this->append('/'));
    }

    public function setFragment(?string $string): static
    {
        $url = $this->clearFragment();

        if ($string !== null) {
            return new Url((string)$url->append('#')->append($string));
        }

        return $url;
    }

    public function clearFragment(): static
    {
        return new Url((string)$this->replaceMatches('/#.*$/', ''));
    }

    public function getQueryParameter(string $name): mixed
    {
    }
}
