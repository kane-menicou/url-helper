<?php

declare(strict_types=1);

namespace Kanemenicou\UrlHelper;

use Psr\Http\Message\UriInterface;
use Symfony\Component\String\UnicodeString;

use function count;
use function is_numeric;
use function Symfony\Component\String\s;

final class Url extends UnicodeString implements UriInterface
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

    public function getPort(bool $returnDefault = false): ?int
    {
        $defaultPortForScheme = $this->getDefaultPortForScheme();
        $explicitlyDefinedPort = $this->getExplicitlyDefinedPort();

        if (! $returnDefault && $explicitlyDefinedPort === $defaultPortForScheme) {
            return null;
        }

        if ($explicitlyDefinedPort !== null || ! $returnDefault) {
            return $explicitlyDefinedPort;
        }

        return $defaultPortForScheme;
    }

    public function getAuthority(): string
    {
        // TODO: Implement getAuthority() method.
    }

    public function getUserInfo(): string
    {
        // TODO: Implement getUserInfo() method.
    }

    public function getHost(): string
    {
        // TODO: Implement getHost() method.
    }

    public function getPath(): string
    {
        // TODO: Implement getPath() method.
    }

    public function getQuery(): string
    {
        // TODO: Implement getQuery() method.
    }

    public function getFragment(): string
    {
        // TODO: Implement getFragment() method.
    }

    public function withScheme(string $scheme): self
    {
        // TODO: Implement withScheme() method.
    }

    public function withUserInfo(string $user, ?string $password = null): self
    {
        // TODO: Implement withUserInfo() method.
    }

    public function withHost(string $host): self
    {
        // TODO: Implement withHost() method.
    }

    public function withPort(?int $port): self
    {
        // TODO: Implement withPort() method.
    }

    public function withPath(string $path): self
    {
        // TODO: Implement withPath() method.
    }

    public function withQuery(string $query): self
    {
        // TODO: Implement withQuery() method.
    }

    public function withFragment(string $fragment): self
    {
        return $this->setFragment($fragment !== '' ? $fragment : null);
    }

    public function getDefaultPortForScheme(): ?int
    {
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
