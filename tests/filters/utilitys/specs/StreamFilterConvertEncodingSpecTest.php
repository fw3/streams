<?php
/**
 *     _______       _______
 *    / ____/ |     / /__  /
 *   / /_   | | /| / / /_ <
 *  / __/   | |/ |/ /___/ /
 * /_/      |__/|__//____/
 *
 * Flywheel3: the inertia php framework
 *
 * @category    Flywheel3
 * @package     streams
 * @author      wakaba <wakabadou@gmail.com>
 * @copyright   Copyright (c) @2019  Wakabadou (http://www.wakabadou.net/) / Project ICKX (https://ickx.jp/). All rights reserved.
 * @license     http://opensource.org/licenses/MIT The MIT License.
 *              This software is released under the MIT License.
 * @varsion     1.0.0
 */

declare(strict_types=1);

namespace Tests\streams\filters\utilitys\specs;

use fw3\streams\filters\utilitys\entitys\StreamFilterSpecEntity;
use fw3\streams\filters\utilitys\specs\entitys\resources\FileResourceSpec;
use fw3\streams\filters\utilitys\specs\entitys\resources\PhpFdResourceSpec;
use fw3\streams\filters\utilitys\specs\entitys\resources\PhpInputResourceSpec;
use fw3\streams\filters\utilitys\specs\entitys\resources\PhpMemoryResourceSpec;
use fw3\streams\filters\utilitys\specs\entitys\resources\PhpOutputResourceSpec;
use fw3\streams\filters\utilitys\specs\entitys\resources\PhpStdinResourceSpec;
use fw3\streams\filters\utilitys\specs\entitys\resources\PhpStdoutResourceSpec;
use fw3\streams\filters\utilitys\specs\entitys\resources\PhpTempResourceSpec;
use fw3\streams\filters\utilitys\specs\entitys\resources\RawResourceSpec;
use fw3\streams\filters\utilitys\specs\entitys\resources\ZipResourceSpec;
use fw3\streams\filters\utilitys\specs\StreamFilterConvertEncodingSpec;
use fw3\streams\filters\utilitys\StreamFilterSpec;
use PHPUnit\Framework\TestCase;

/**
 * ストリームフィルタ：ConvertEncodingFilterSpecのテスト
 * @internal
 */
class StreamFilterConvertEncodingSpecTest extends TestCase
{
    /**
     * 現在のフィルタ名のストリームフィルタが登録されているかのテスト
     *
     * @runInSeparateProcess
     *
     * @test
     */
    public function registeredFilterName(): void
    {
        $this->assertFalse(StreamFilterConvertEncodingSpec::registeredFilterName());

        StreamFilterSpec::registerConvertEncodingFilter();

        $this->assertTrue(StreamFilterConvertEncodingSpec::registeredFilterName());

        StreamFilterConvertEncodingSpec::filterName('test.convert.encoding');
        $this->assertFalse(StreamFilterConvertEncodingSpec::registeredFilterName());
    }

    /**
     * リソースアクセサのテスト
     *
     * @test
     */
    public function resourceAccessor(): void
    {
        $this->assertInstanceOf(StreamFilterSpecEntity::class, $streamFilterSpecEntity = StreamFilterSpecEntity::factory([
            'resource' => 'test.csv',
        ]));
        $this->assertInstanceOf(FileResourceSpec::class, $streamFilterSpecEntity->resource());

        $this->assertInstanceOf(StreamFilterSpecEntity::class, $streamFilterSpecEntity = StreamFilterSpecEntity::factory([
            'resource' => ['', 'test.csv'],
        ]));
        $this->assertInstanceOf(FileResourceSpec::class, $streamFilterSpecEntity->resource());

        $this->assertInstanceOf(StreamFilterSpecEntity::class, $streamFilterSpecEntity = StreamFilterSpecEntity::factory([
            'resource' => 'php://stdin',
        ]));
        $this->assertInstanceOf(PhpStdinResourceSpec::class, $streamFilterSpecEntity->resource());

        $this->assertInstanceOf(StreamFilterSpecEntity::class, $streamFilterSpecEntity = StreamFilterSpecEntity::factory([
            'resource' => ['zip://', 'test.csv', 'hoge.txt'],
        ]));
        $this->assertInstanceOf(ZipResourceSpec::class, $streamFilterSpecEntity->resource());

        $this->assertInstanceOf(StreamFilterSpecEntity::class, $streamFilterSpecEntity = StreamFilterSpecEntity::factory([
            'resource' => 'zip://hoge.zip#huga.csv',
        ]));
        $this->assertInstanceOf(RawResourceSpec::class, $streamFilterSpecEntity->resource());

        $this->assertInstanceOf(StreamFilterSpecEntity::class, $streamFilterSpecEntity = StreamFilterSpecEntity::factory()->resource('test.csv'));
        $this->assertInstanceOf(FileResourceSpec::class, $streamFilterSpecEntity->resource());

        $this->assertInstanceOf(StreamFilterSpecEntity::class, $streamFilterSpecEntity = StreamFilterSpecEntity::factory()->resource('zip://hoge.zip#huga.csv'));
        $this->assertInstanceOf(RawResourceSpec::class, $streamFilterSpecEntity->resource());

        $this->assertInstanceOf(StreamFilterSpecEntity::class, $streamFilterSpecEntity = StreamFilterSpecEntity::factory()->resource(FileResourceSpec::factory('test.csv')));
        $this->assertInstanceOf(FileResourceSpec::class, $streamFilterSpecEntity->resource());

        $this->assertInstanceOf(StreamFilterSpecEntity::class, $streamFilterSpecEntity = StreamFilterSpecEntity::factory()->resourceFile('test.csv'));
        $this->assertInstanceOf(FileResourceSpec::class, $streamFilterSpecEntity->resource());

        $this->assertInstanceOf(StreamFilterSpecEntity::class, $streamFilterSpecEntity = StreamFilterSpecEntity::factory()->resourceStdin());
        $this->assertInstanceOf(PhpStdinResourceSpec::class, $streamFilterSpecEntity->resource());

        $this->assertInstanceOf(StreamFilterSpecEntity::class, $streamFilterSpecEntity = StreamFilterSpecEntity::factory()->resourceStdout());
        $this->assertInstanceOf(PhpStdoutResourceSpec::class, $streamFilterSpecEntity->resource());

        $this->assertInstanceOf(StreamFilterSpecEntity::class, $streamFilterSpecEntity = StreamFilterSpecEntity::factory()->resourceInput());
        $this->assertInstanceOf(PhpInputResourceSpec::class, $streamFilterSpecEntity->resource());

        $this->assertInstanceOf(StreamFilterSpecEntity::class, $streamFilterSpecEntity = StreamFilterSpecEntity::factory()->resourceOutput());
        $this->assertInstanceOf(PhpOutputResourceSpec::class, $streamFilterSpecEntity->resource());

        $this->assertInstanceOf(StreamFilterSpecEntity::class, $streamFilterSpecEntity = StreamFilterSpecEntity::factory()->resourceFd());
        $this->assertInstanceOf(PhpFdResourceSpec::class, $streamFilterSpecEntity->resource());

        $this->assertInstanceOf(StreamFilterSpecEntity::class, $streamFilterSpecEntity = StreamFilterSpecEntity::factory()->resourceMemory());
        $this->assertInstanceOf(PhpMemoryResourceSpec::class, $streamFilterSpecEntity->resource());

        $this->assertInstanceOf(StreamFilterSpecEntity::class, $streamFilterSpecEntity = StreamFilterSpecEntity::factory()->resourceTemp());
        $this->assertInstanceOf(PhpTempResourceSpec::class, $streamFilterSpecEntity->resource());

        $this->assertInstanceOf(StreamFilterSpecEntity::class, $streamFilterSpecEntity = StreamFilterSpecEntity::factory()->resourceRaw('zip://hoge.zip#huga.csv'));
        $this->assertInstanceOf(RawResourceSpec::class, $streamFilterSpecEntity->resource());

        $this->assertInstanceOf(StreamFilterSpecEntity::class, $streamFilterSpecEntity = StreamFilterSpecEntity::factory()->resourceZip('test.csv', 'hoge.txt'));
        $this->assertInstanceOf(ZipResourceSpec::class, $streamFilterSpecEntity->resource());
    }

    /**
     * リソースをめぐるビルドのテスト
     *
     * @test
     */
    public function resourceBuild(): void
    {
        $this->assertSame('php://filter/resource=test.csv', StreamFilterSpecEntity::factory([
            'resource' => 'test.csv',
        ])->build());

        $this->assertSame('php://filter/resource=test.csv', StreamFilterSpecEntity::factory([
            'resource' => ['', 'test.csv'],
        ])->build());

        $this->assertSame('php://filter/resource=php://stdin', StreamFilterSpecEntity::factory([
            'resource' => 'php://stdin',
        ])->build());

        $this->assertSame('php://filter/resource=zip://hoge.zip#fuga.csv', StreamFilterSpecEntity::factory([
            'resource' => ['zip://', 'hoge.zip', 'fuga.csv'],
        ])->build());

        $this->assertSame('php://filter/resource=zip://hoge.zip#fuga.csv', StreamFilterSpecEntity::factory([
            'resource' => 'zip://hoge.zip#fuga.csv',
        ])->build());

        $this->assertSame('php://filter/resource=test.csv', StreamFilterSpecEntity::factory()->resource('test.csv')->build());

        $this->assertSame('php://filter/resource=zip://hoge.zip#fuga.csv', StreamFilterSpecEntity::factory()->resource('zip://hoge.zip#fuga.csv')->build());

        $this->assertSame('php://filter/resource=test.csv', StreamFilterSpecEntity::factory()->resource(FileResourceSpec::factory('test.csv'))->build());

        $this->assertSame('php://filter/resource=test.csv', StreamFilterSpecEntity::factory()->resourceFile('test.csv')->build());

        $this->assertSame('php://filter/resource=php://stdin', StreamFilterSpecEntity::factory()->resourceStdin()->build());

        $this->assertSame('php://filter/resource=zip://hoge.zip#fuga.csv', StreamFilterSpecEntity::factory()->resourceRaw('zip://hoge.zip#fuga.csv')->build());

        $this->assertSame('php://filter/resource=zip://hoge.zip#fuga.csv', StreamFilterSpecEntity::factory()->resourceZip('hoge.zip', 'fuga.csv')->build());
    }
}
