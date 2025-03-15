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

    /**
     * @test
     */
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
            'http://example.com/?bar=baz&foo=bar' => [
                'url' => 'http://example.com/?bar=baz&foo=bar',
                'key' => 'bar',
                'value' => 'baz',
            ]
        ];
    }
}
