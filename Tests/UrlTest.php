<?php

declare(strict_types=1);

namespace Kanemenicou\UrlHelper\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Kanemenicou\UrlHelper\Url;

class UrlTest extends TestCase
{
    #[Test]
    public function testAddQueryParameter()
    {
        $url = new Url('http://example.com/?bar=baz');

        $url = $url->addQueryParameter('foo', 'bar');

        self::assertSame('http://example.com/?bar=baz&foo=bar', (string)$url);
    }

    #[Test]
    public function itWillRemoveQueryParameters(): void
    {
        $url = new Url('http://example.com/?bar=baz&foo=bar');

        $url = $url->removeQueryParameter('bar');

        self::assertSame('http://example.com/?foo=bar', (string)$url);

        $url = $url->removeQueryParameter('foo');

        self::assertSame('http://example.com/', (string)$url);
    }

    #[Test]
    public function itWillTrimTrailingSlashes(): void
    {
        $url = new Url('http://example.com/');

        $url = $url->trimTrailingSlash();

        self::assertSame('http://example.com', (string)$url);
    }

    #[Test]
    public function itWillAppendATrailingSlash(): void
    {
        $url = new Url('http://example.com');

        $url = $url->appendTrailingSlash();

        self::assertSame('http://example.com/', (string)$url);

        $url = $url->appendTrailingSlash();

        self::assertSame('http://example.com//', (string)$url);
    }

    #[Test]
    public function itWillClearAFragment(): void
    {
        $url = new Url('http://example.com#some=test');

        $url = $url->clearFragment();

        self::assertSame('http://example.com', (string)$url);
    }

    #[Test]
    public function itWillSetAFragment(): void
    {
        $url = new Url('http://example.com');

        $url = $url->setFragment('some=test');

        self::assertSame('http://example.com#some=test', (string)$url);

        $url = $url->setFragment('title');

        self::assertSame('http://example.com#title', (string)$url);
    }

    #[Test]
    public function itWillAllowAFragmentToBeSetToNull(): void
    {
        $url = new Url('http://example.com#title');

        $url = $url->setFragment(null);

        self::assertSame('http://example.com', (string)$url);
    }

    #[Test]
    #[DataProvider('queryParameterValues')]
    public function itWillGetValidQueryParameterValue(string $url, string $key, mixed $value): void
    {
        self::assertSame($value, new Url($url)->getQueryParameter($key));
    }

    public static function queryParameterValues(): array
    {
        return [
            'http://example.com/?bar=baz&foo=bar (bar)' => [
                'url' => 'http://example.com/?bar=baz&foo=bar',
                'key' => 'bar',
                'value' => 'baz',
            ],
            'http://example.com/?bar=baz&foo=bar (foo)' => [
                'url' => 'http://example.com/?bar=baz&foo=bar',
                'key' => 'foo',
                'value' => 'bar',
            ],
            'http://example.com/?bar=baz&foo=bar (other)' => [
                'url' => 'http://example.com/?bar=baz&foo=bar',
                'key' => 'other',
                'value' => null,
            ],
            'http://example.com/?bar=baz&pi=3.141 (pi)' => [
                'url' => 'http://example.com/?bar=baz&pi=3.141',
                'key' => 'pi',
                'value' => 3.141,
            ],
            'http://example.com/?bar=&int=3 (bar)' => [
                'url' => 'http://example.com/?bar=&int=3',
                'key' => 'bar',
                'value' => '',
            ],
        ];
    }

    #[Test]
    #[DataProvider('schemes')]
    public function itWillReturnTheScheme(string $url, string $scheme): void
    {
        $url = new Url($url);

        self::assertSame($scheme, $url->getScheme());
    }

    public static function schemes(): array
    {
        return [
            'http://example.com/?bar=&int=3' => [
                'url' => 'http://example.com/?bar=&int=3',
                'scheme' => 'http',
            ],
            'https://example.com/?bar=&int=3' => [
                'url' => 'https://example.com/?bar=&int=3',
                'scheme' => 'https',
            ],
            'ftp://example.com/?bar=&int=3' => [
                'url' => 'ftp://example.com/?bar=&int=3',
                'scheme' => 'ftp',
            ],
            'ftps://example.com/?bar=&int=3' => [
                'url' => 'ftps://example.com/?bar=&int=3',
                'scheme' => 'ftps',
            ],
            'pop3://example.com/?bar=&int=3' => [
                'url' => 'pop3://example.com/?bar=&int=3',
                'scheme' => 'pop3',
            ],
            'pop3s://example.com/?bar=&int=3' => [
                'url' => 'pop3s://example.com/?bar=&int=3',
                'scheme' => 'pop3s',
            ],
        ];
    }

    #[Test]
    #[DataProvider('ports')]
    public function itWillReturnExplicitlyDefinedPorts(string $url, ?int $port): void
    {
        self::assertSame($port, new Url($url)->getExplicitlyDefinedPort());
    }

    public static function ports(): array
    {
        return [
            'http://example.com:12/?bar=&int=3' => [
                'url' => 'http://example.com:12/?bar=&int=3',
                'port' => 12,
            ],
            'https://example.com:543/?bar=&int=3' => [
                'url' => 'https://example.com:543/?bar=&int=3',
                'port' => 543,
            ],
            'ftp://example.com:411/?bar=&int=3' => [
                'url' => 'ftp://example.com:411/?bar=&int=3',
                'port' => 411,
            ],
            'ftps://example.com:43/?bar=&int=3' => [
                'url' => 'ftps://example.com:43/?bar=&int=3',
                'port' => 43,
            ],
            'https://example.com/?bar=&int=3' => [
                'url' => 'https://example.com/?bar=&int=3',
                'port' => null,
            ],
        ];
    }

    #[Test]
    #[DataProvider('portsWithDefaults')]
    public function itWillReturnPorts(string $url, ?int $port): void
    {
        self::assertSame($port, new Url($url)->getPort());
    }

    public static function portsWithDefaults(): array
    {
        $ports = self::ports();
        unset($ports['http://example.com/?bar=&int=3']);

        return [
            ...$ports,
            'https://example.com/?bar=&int=3' => [
                'url' => 'https://example.com/?bar=&int=3',
                'port' => 443,
            ],
            'http://example.com/?bar=&int=3' => [
                'url' => 'http://example.com/?bar=&int=3',
                'port' => 80,
            ],
            'ftp://example.com/?bar=&int=3' => [
                'url' => 'http://example.com/?bar=&int=3',
                'port' => 80,
            ],
            'somethingrandom://example.com/?bar=&int=3' => [
                'url' => 'somethingrandom://example.com/?bar=&int=3',
                'port' => null,
            ],
            'http://example.com/somepath' => [
                'url' => 'http://example.com/somepath',
                'port' => 80,
            ],
            'https://example.com/somepath' => [
                'url' => 'https://example.com/somepath',
                'port' => 443,
            ],
            'ftp://example.com/somepath' => [
                'url' => 'ftp://example.com/somepath',
                'port' => 21,
            ],
            'ftps://example.com/somepath' => [
                'url' => 'ftps://example.com/somepath',
                'port' => 990,
            ],
            'sftp://example.com/somepath' => [
                'url' => 'sftp://example.com/somepath',
                'port' => 22,
            ],
            'ssh://example.com/somepath' => [
                'url' => 'ssh://example.com/somepath',
                'port' => 22,
            ],
            'telnet://example.com/somepath' => [
                'url' => 'telnet://example.com/somepath',
                'port' => 23,
            ],
            'smtp://example.com/somepath' => [
                'url' => 'smtp://example.com/somepath',
                'port' => 25,
            ],
            'smtps://example.com/somepath' => [
                'url' => 'smtps://example.com/somepath',
                'port' => 465,
            ],
            'imap://example.com/somepath' => [
                'url' => 'imap://example.com/somepath',
                'port' => 143,
            ],
            'imaps://example.com/somepath' => [
                'url' => 'imaps://example.com/somepath',
                'port' => 993,
            ],
            'pop3://example.com/somepath' => [
                'url' => 'pop3://example.com/somepath',
                'port' => 110,
            ],
            'pop3s://example.com/somepath' => [
                'url' => 'pop3s://example.com/somepath',
                'port' => 995,
            ],
            'ldap://example.com/somepath' => [
                'url' => 'ldap://example.com/somepath',
                'port' => 389,
            ],
            'ldaps://example.com/somepath' => [
                'url' => 'ldaps://example.com/somepath',
                'port' => 636,
            ],
            'dns://example.com/somepath' => [
                'url' => 'dns://example.com/somepath',
                'port' => 53,
            ],
            'dhcp://example.com/somepath' => [
                'url' => 'dhcp://example.com/somepath',
                'port' => 67,
            ],
            'ntp://example.com/somepath' => [
                'url' => 'ntp://example.com/somepath',
                'port' => 123,
            ],
            'NTP://example.com/somepath' => [
                'url' => 'NTP://example.com/somepath',
                'port' => 123,
            ],
            'mysql://example.com/somepath' => [
                'url' => 'mysql://example.com/somepath',
                'port' => 3306,
            ],
            'pgsql://example.com/somepath' => [
                'url' => 'pgsql://example.com/somepath',
                'port' => 5432,
            ],
            'mssql://example.com/somepath' => [
                'url' => 'mssql://example.com/somepath',
                'port' => 1433,
            ],
            'rdp://example.com/somepath' => [
                'url' => 'rdp://example.com/somepath',
                'port' => 3389,
            ],
            'redis://example.com/somepath' => [
                'url' => 'redis://example.com/somepath',
                'port' => 6379,
            ],
            'memcached://example.com/somepath' => [
                'url' => 'memcached://example.com/somepath',
                'port' => 11211,
            ],
            'MEMCACHED://example.com/somepath' => [
                'url' => 'MEMCACHED://example.com/somepath',
                'port' => 11211,
            ],
            'mongodb://example.com/somepath' => [
                'url' => 'mongodb://example.com/somepath',
                'port' => 27017,
            ],
        ];
    }
}
