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
        $url = new Url('http://example.com/?bar=baz&foo=bar#test=23');

        $url = $url->removeQueryParameter('bar');

        self::assertSame('http://example.com/?foo=bar#test=23', (string)$url);

        $url = $url->removeQueryParameter('foo');

        self::assertSame('http://example.com/#test=23', (string)$url);
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
    public function itWillSetWithAFragment(): void
    {
        $url = new Url('http://example.com');

        $url = $url->withFragment('some=test');

        self::assertSame('http://example.com#some=test', (string)$url);

        $url = $url->withFragment('0');

        self::assertSame('http://example.com#0', (string)$url);
    }


    #[Test]
    public function itWillAllowAFragmentToBeSetToNull(): void
    {
        $url = new Url('http://example.com#title');

        $url = $url->setFragment(null);

        self::assertSame('http://example.com', (string)$url);
    }

    #[Test]
    public function itWillAllowAUrlWithEmptyFragment(): void
    {
        $url = new Url('http://example.com#title');

        $url = $url->withFragment('');

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
        self::assertSame($port, new Url($url)->getPort(true));
    }

    public static function portsWithDefaults(): array
    {
        $ports = self::ports();
        unset($ports['http://example.com/?bar=&int=3']);

        return [
            ...$ports,
            ...self::schemePortDefaults(),
        ];
    }

    #[Test]
    #[DataProvider('schemePortDefaults')]
    public function itWillNotReturnPortIfReturningDefaultsIsTurnedOff(string $url, ?int $port): void
    {
        self::assertNull(new Url($url)->getPort());
    }

    #[Test]
    #[DataProvider('schemePortDefaults')]
    public function itWillReturnPortDefaultForScheme(string $url, ?int $port): void
    {
        self::assertSame($port, new Url($url)->getDefaultPortForScheme());
    }

    public static function schemePortDefaults(): array
    {
        return [
            'https://example.com/?bar=&int=3' => [
                'url' => 'https://example.com/?bar=&int=3',
                'port' => 443,
            ],
            'https://example.com:443/?bar=&int=3' => [
                'url' => 'https://example.com/?bar=&int:443=3',
                'port' => 443,
            ],
            'http://example.com/?bar=&int=3' => [
                'url' => 'http://example.com/?bar=&int=3',
                'port' => 80,
            ],
            'http://example.com:80/?bar=&int=3' => [
                'url' => 'http://example.com/?bar=&int:80=3',
                'port' => 80,
            ],
            'ftp://example.com/?bar=&int=3' => [
                'url' => 'http://example.com/?bar=&int=3',
                'port' => 80,
            ],
            'ftp://example.com:80/?bar=&int=3' => [
                'url' => 'http://example.com/?bar=&int:80=3',
                'port' => 80,
            ],
            'somethingrandom://example.com/?bar=&int=3' => [
                'url' => 'somethingrandom://example.com/?bar=&int=3',
                'port' => null,
            ],
            'somethingrandom://example.com:null/?bar=&int=3' => [
                'url' => 'somethingrandom://example.com/?bar=&int:null=3',
                'port' => null,
            ],
            'http://example.com/somepath' => [
                'url' => 'http://example.com/somepath',
                'port' => 80,
            ],
            'http://example.com:80/somepath' => [
                'url' => 'http://example.com:80/somepath',
                'port' => 80,
            ],
            'https://example.com/somepath' => [
                'url' => 'https://example.com/somepath',
                'port' => 443,
            ],
            'https://example.com:443/somepath' => [
                'url' => 'https://example.com:443/somepath',
                'port' => 443,
            ],
            'ftp://example.com/somepath' => [
                'url' => 'ftp://example.com/somepath',
                'port' => 21,
            ],
            'ftp://example.com:21/somepath' => [
                'url' => 'ftp://example.com:21/somepath',
                'port' => 21,
            ],
            'ftps://example.com/somepath' => [
                'url' => 'ftps://example.com/somepath',
                'port' => 990,
            ],
            'ftps://example.com:990/somepath' => [
                'url' => 'ftps://example.com:990/somepath',
                'port' => 990,
            ],
            'sftp://example.com/somepath' => [
                'url' => 'sftp://example.com/somepath',
                'port' => 22,
            ],
            'sftp://example.com:22/somepath' => [
                'url' => 'sftp://example.com:22/somepath',
                'port' => 22,
            ],
            'ssh://example.com/somepath' => [
                'url' => 'ssh://example.com/somepath',
                'port' => 22,
            ],
            'ssh://example.com:22/somepath' => [
                'url' => 'ssh://example.com:22/somepath',
                'port' => 22,
            ],
            'telnet://example.com/somepath' => [
                'url' => 'telnet://example.com/somepath',
                'port' => 23,
            ],
            'telnet://example.com:23/somepath' => [
                'url' => 'telnet://example.com:23/somepath',
                'port' => 23,
            ],
            'smtp://example.com/somepath' => [
                'url' => 'smtp://example.com/somepath',
                'port' => 25,
            ],
            'smtp://example.com:25/somepath' => [
                'url' => 'smtp://example.com:25/somepath',
                'port' => 25,
            ],
            'smtps://example.com/somepath' => [
                'url' => 'smtps://example.com/somepath',
                'port' => 465,
            ],
            'smtps://example.com:465/somepath' => [
                'url' => 'smtps://example.com:465/somepath',
                'port' => 465,
            ],
            'imap://example.com/somepath' => [
                'url' => 'imap://example.com/somepath',
                'port' => 143,
            ],
            'imap://example.com:143/somepath' => [
                'url' => 'imap://example.com:143/somepath',
                'port' => 143,
            ],
            'imaps://example.com/somepath' => [
                'url' => 'imaps://example.com/somepath',
                'port' => 993,
            ],
            'imaps://example.com:993/somepath' => [
                'url' => 'imaps://example.com:993/somepath',
                'port' => 993,
            ],
            'pop3://example.com/somepath' => [
                'url' => 'pop3://example.com/somepath',
                'port' => 110,
            ],
            'pop3://example.com:110/somepath' => [
                'url' => 'pop3://example.com:110/somepath',
                'port' => 110,
            ],
            'pop3s://example.com/somepath' => [
                'url' => 'pop3s://example.com/somepath',
                'port' => 995,
            ],
            'pop3s://example.com:995/somepath' => [
                'url' => 'pop3s://example.com:995/somepath',
                'port' => 995,
            ],
            'ldap://example.com/somepath' => [
                'url' => 'ldap://example.com/somepath',
                'port' => 389,
            ],
            'ldap://example.com:389/somepath' => [
                'url' => 'ldap://example.com:389/somepath',
                'port' => 389,
            ],
            'ldaps://example.com/somepath' => [
                'url' => 'ldaps://example.com/somepath',
                'port' => 636,
            ],
            'ldaps://example.com:636/somepath' => [
                'url' => 'ldaps://example.com:636/somepath',
                'port' => 636,
            ],
            'dns://example.com/somepath' => [
                'url' => 'dns://example.com/somepath',
                'port' => 53,
            ],
            'dns://example.com:53/somepath' => [
                'url' => 'dns://example.com:53/somepath',
                'port' => 53,
            ],
            'dhcp://example.com/somepath' => [
                'url' => 'dhcp://example.com/somepath',
                'port' => 67,
            ],
            'dhcp://example.com:67/somepath' => [
                'url' => 'dhcp://example.com:67/somepath',
                'port' => 67,
            ],
            'ntp://example.com/somepath' => [
                'url' => 'ntp://example.com/somepath',
                'port' => 123,
            ],
            'ntp://example.com:123/somepath' => [
                'url' => 'ntp://example.com:123/somepath',
                'port' => 123,
            ],
            'NTP://example.com/somepath' => [
                'url' => 'NTP://example.com/somepath',
                'port' => 123,
            ],
            'NTP://example.com:123/somepath' => [
                'url' => 'NTP://example.com:123/somepath',
                'port' => 123,
            ],
            'mysql://example.com/somepath' => [
                'url' => 'mysql://example.com/somepath',
                'port' => 3306,
            ],
            'mysql://example.com:3306/somepath' => [
                'url' => 'mysql://example.com:3306/somepath',
                'port' => 3306,
            ],
            'pgsql://example.com/somepath' => [
                'url' => 'pgsql://example.com/somepath',
                'port' => 5432,
            ],
            'pgsql://example.com:5432/somepath' => [
                'url' => 'pgsql://example.com:5432/somepath',
                'port' => 5432,
            ],
            'mssql://example.com/somepath' => [
                'url' => 'mssql://example.com/somepath',
                'port' => 1433,
            ],
            'mssql://example.com:1433/somepath' => [
                'url' => 'mssql://example.com:1433/somepath',
                'port' => 1433,
            ],
            'rdp://example.com/somepath' => [
                'url' => 'rdp://example.com/somepath',
                'port' => 3389,
            ],
            'rdp://example.com:3389/somepath' => [
                'url' => 'rdp://example.com:3389/somepath',
                'port' => 3389,
            ],
            'redis://example.com/somepath' => [
                'url' => 'redis://example.com/somepath',
                'port' => 6379,
            ],
            'redis://example.com:6379/somepath' => [
                'url' => 'redis://example.com:6379/somepath',
                'port' => 6379,
            ],
            'memcached://example.com/somepath' => [
                'url' => 'memcached://example.com/somepath',
                'port' => 11211,
            ],
            'memcached://example.com:11211/somepath' => [
                'url' => 'memcached://example.com:11211/somepath',
                'port' => 11211,
            ],
            'MEMCACHED://example.com/somepath' => [
                'url' => 'MEMCACHED://example.com/somepath',
                'port' => 11211,
            ],
            'MEMCACHED://example.com:11211/somepath' => [
                'url' => 'MEMCACHED://example.com:11211/somepath',
                'port' => 11211,
            ],
            'mongodb://example.com/somepath' => [
                'url' => 'mongodb://example.com/somepath',
                'port' => 27017,
            ],
            'mongodb://example.com:27017/somepath' => [
                'url' => 'mongodb://example.com:27017/somepath',
                'port' => 27017,
            ],
        ];
    }

    #[Test]
    #[DataProvider('allQueryParameters')]
    public function itWillSupportGettingAllQueryParameters(string $url, array $result): void
    {
        self::assertSame($result, new Url($url)->allQueryParameters());
    }

    public static function allQueryParameters(): array
    {
        return [
            'https://example.com?query=123' => [
                'url' => 'https://example.com?query=123',
                'result' => [
                    'query' => 123,
                ],
            ],
            'https://example.com' => [
                'url' => 'https://example.com',
                'result' => [],
            ],
            'https://example.com?query=some%20text&other=1243#test=123&other=124' => [
                'url' => 'https://example.com?query=some%20text&other=1243#test=123&other=124',
                'result' => [
                    'query' => 'some text',
                    'other' => 1243,
                ],
            ],
            'https://example.com?' => [
                'url' => 'https://example.com?',
                'result' => [],
            ],
            'https://example.com?empty=&null' => [
                'url' => 'https://example.com?empty=&null',
                'result' => [
                    'empty' => '',
                    'null' => null,
                ],
            ],
            // TODO: SUPPORT ARRAY QUERY PARAMETERS
        ];
    }

    #[Test]
    #[DataProvider('withQueryParametersCleared')]
    public function itWillSupportClearingQueryParameters(string $url, string $result): void
    {
        self::assertSame($result, (string)(new Url($url)->clearQuery()));
    }

    public static function withQueryParametersCleared(): array
    {
        return [
            'https://example.com?query=123' => [
                'url' => 'https://example.com?query=123',
                'result' => 'https://example.com',
            ],
            'https://example.com?query=123&other=1243#test=123' => [
                'url' => 'https://example.com?query=123&other=1243#test=123',
                'result' => 'https://example.com#test=123',
            ],
            'https://example.com?' => [
                'url' => 'https://example.com?',
                'result' => 'https://example.com',
            ],
        ];
    }

    #[Test]
    #[DataProvider('withQueryStringToAdd')]
    public function itWillAllowWithQuery(string $url, string $query, string $result): void
    {
        self::assertSame($result, (string)new Url($url)->withQuery($query));
    }

    public static function withQueryStringToAdd(): array
    {
        return [
            'https://example.com?query=123' => [
                'url' => 'https://example.com?query=123',
                'query' => 'query=123',
                'result' => 'https://example.com?query=123',
            ],
            'https://example.com?query=123&other=test%20space#test=123' => [
                'url' => 'https://example.com?query=123&another=some%20testother=1243#test=123',
                'query' => 'query=123&other=test space',
                'result' => 'https://example.com?query=123&other=test+space#test=123',
            ],
            'https://example.com?query=123&other=other%20text#test=123' => [
                'url' => 'https://example.com?query=123&&another=some%20testother=1243#test=123',
                'query' => 'query=123&other=other%20text',
                'result' => 'https://example.com?query=123&other=other%20text#test=123',
            ],
            'https://example.com' => [
                'url' => 'https://example.com?',
                'query' => '',
                'result' => 'https://example.com',
            ],
        ];
    }
}
