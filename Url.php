<?php

declare(strict_types=1);

namespace Kanemenicou\UrlHelper;

use Symfony\Component\String\UnicodeString;

use function count;
use function is_numeric;
use function Symfony\Component\String\s;

final class Url extends UnicodeString
{
    public function addQueryParameter(string $name, mixed $value): self
    {
        $originalQuery = s(parse_url($this->string, PHP_URL_QUERY));
        $newQuery = $originalQuery
            ->append($originalQuery->isEmpty() ? '?' : '&')
            ->append(urlencode($name))
            ->append('=')
            ->append(urlencode($value))
        ;

        return $this->replace((string)$originalQuery, (string)$newQuery);
    }

    public function removeQueryParameter(string $name): self
    {
        return $this
            ->replaceMatches('/[&]' . $name . '=[^&]*/', '')
            ->replaceMatches('/' . $name . '=[^&]*[&]{0,}/', '')
            ->trimSuffix('?')
        ;
    }

    public function trimTrailingSlash(): self
    {
        return $this->trimSuffix('/');
    }

    public function appendTrailingSlash(): self
    {
        return $this->append('/');
    }

    public function setFragment(?string $string): self
    {
        $url = $this->clearFragment();

        if ($string !== null) {
            return $url->append('#')->append($string);
        }

        return $url;
    }

    public function clearFragment(): self
    {
        return $this->replaceMatches('/#.*$/', '');
    }

    public function getQueryParameter(string $name): array|string|int|float|null
    {
        $indexOfQueryParameter = $this->indexOf($name . '=');
        if ($indexOfQueryParameter === null) {
            return null;
        }

        $value = $this->slice($indexOfQueryParameter)->replace($name . '=', '')->split('&')[0];
        if (!is_numeric((string)$value)) {
            return (string)$value;
        }

        if (count($value->match('/^[0-9]{1,}$/')) > 0) {
            return (int)$value->toString();
        }

        return (float)$value->toString();
    }

    public function getScheme(): string
    {
        return (string)s($this->match('/^([A-z0-9]*)\:\/\//')[0])->replace('://', '');
    }

    public function getExplicitlyDefinedPort(): ?int
    {
        $portAndPrefix = $this->match('/:[0-9]{1,5}/')[0] ?? null;
        if ($portAndPrefix === null) {
            return null;
        }

        return (int)(s($portAndPrefix)->trimPrefix(':')->toString());
    }

    public function getPort(): ?int
    {
        $port = $this->getExplicitlyDefinedPort();
        if ($port !== null) {
            return $port;
        }

        return match (s($this->getScheme())->lower()->toString()) {
            'http' => 80,
            'https' => 443,
            'ftp' => 21,
            'ftps' => 990,
            'sftp', 'ssh' => 22,
            'telnet' => 23,
            'smtp' => 25,
            'smtps' => 465,
            'imap' => 143,
            'imaps' => 993,
            'pop3' => 110,
            'pop3s' => 995,
            'ldap' => 389,
            'ldaps' => 636,
            'dns' => 53,
            'dhcp' => 67,
            'ntp' => 123,
            'mysql' => 3306,
            'pgsql' => 5432,
            'mssql' => 1433,
            'rdp' => 3389,
            'redis' => 6379,
            'memcached' => 11211,
            'mongodb' => 27017,
            default => null,
        };
    }
}
