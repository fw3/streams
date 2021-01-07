<?php
/**    _______       _______
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

namespace Tests\streams\filters;

use PHPUnit\Framework\TestCase;
use fw3\streams\filters\ConvertLinefeedFilter;
use fw3\streams\filters\utilitys\StreamFilterSpec;
use fw3\streams\filters\utilitys\specs\StreamFilterConvertEncodingSpec;
use fw3\streams\filters\utilitys\specs\StreamFilterConvertLinefeedSpec;
use fw3\tests\streams\traits\StreamFilterTestTrait;

/**
 * 行末の改行コードを変換するストリームフィルタクラスのテスト
 */
class ConvertLinefeedFilterTest extends TestCase
{
    use StreamFilterTestTrait;

    /**
     * @var string  テストデータ：空文字
     */
    protected const TEST_DATA_EMPTY       = '';

    /**
     * @var string  テストデータ：パターン1：CR
     */
    protected const TEST_DATA_ONLY_CR1    = "\r";

    /**
     * @var string  テストデータ：パターン1：LF
     */
    protected const TEST_DATA_ONLY_LF1    = "\n";

    /**
     * @var string  テストデータ：パターン1：CRLF
     */
    protected const TEST_DATA_ONLY_CRLF1  = "\r\n";

    /**
     * @var string  テストデータ：パターン2：CR
     */
    protected const TEST_DATA_ONLY_CR2    = "\r\r";

    /**
     * @var string  テストデータ：パターン2：LF
     */
    protected const TEST_DATA_ONLY_LF2    = "\n\n";

    /**
     * @var string  テストデータ：パターン2：CRLF
     */
    protected const TEST_DATA_ONLY_CRLF2  = "\r\n\r\n";

    /**
     * @var string  テストデータ：パターン3：CR
     */
    protected const TEST_DATA_ONLY_CR3    = "\r\r\r";

    /**
     * @var string  テストデータ：パターン3：LF
     */
    protected const TEST_DATA_ONLY_LF3    = "\n\n\n";

    /**
     * @var string  テストデータ：パターン3：CRLF
     */
    protected const TEST_DATA_ONLY_CRLF3  = "\r\n\r\n\r\n";

    /**
     * @var string  テストデータ：パターン4：CR
     */
    protected const TEST_DATA_ONLY_CR4    = "\r\r\r\r";

    /**
     * @var string  テストデータ：パターン4：LF
     */
    protected const TEST_DATA_ONLY_LF4    = "\n\n\n\n";

    /**
     * @var string  テストデータ：パターン4：CRLF
     */
    protected const TEST_DATA_ONLY_CRLF4  = "\r\n\r\n\r\n\r\n";

    /**
     * @var string  テストデータ：複雑な組み合わせ1
     */
    protected const TEST_DATA_COMPLEX1    = "\r\n\n\r";

    /**
     * @var string  テストデータ：複雑な組み合わせ2
     */
    protected const TEST_DATA_COMPLEX2    = "\n\r\r\n";

    /**
     * @var string  テストデータ：複雑な組み合わせ3
     */
    protected const TEST_DATA_COMPLEX3    = "\r\r\n\n";

    /**
     * @var string  テストデータ：複雑な組み合わせ4
     */
    protected const TEST_DATA_COMPLEX4    = "\n\n\r\r";

    /**
     * @var string  テストデータ：複雑な組み合わせ5
     */
    protected const TEST_DATA_COMPLEX5    = "\n\r";

    /**
     * Setup
     */
    protected function setUp() : void
    {
        StreamFilterSpec::registerConvertLinefeedFilter();
    }

    /**
     * フィルタ名テスト
     */
    public function testFilterName() : void
    {
        StreamFilterSpec::registerConvertLinefeedFilter('aaaa');
        $this->assertWriteStreamFilterSame("\r\n\n", "\r\n\r", 'php://filter/write=aaaa.lf:cr/resource=php://temp');

        StreamFilterSpec::registerConvertLinefeedFilter('aaaa.bbb.ccc');
        $this->assertWriteStreamFilterSame("\r\n\n", "\r\n\r", 'php://filter/write=aaaa.bbb.ccc.lf:cr/resource=php://temp');

        StreamFilterSpec::registerConvertLinefeedFilter();
    }

    /**
     * 例外テスト
     */
    public function testException() : void
    {
        try {
            $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF1, static::TEST_DATA_ONLY_LF1, [StreamFilterConvertLinefeedSpec::toLf()->fromLf()]);
            throw new \Exception();
        } catch (\Exception $e) {
            $this->assertSame('変換前後の改行コード指定が同じです。to_linefeed:LF, from_linefeed:LF', $e->getMessage());
        }

        try {
            $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF1, static::TEST_DATA_ONLY_LF1, 'php://filter/write=convert.linefeed.aaa:lf/resource=php://temp');
            throw new \Exception();
        } catch (\Exception $e) {
            $this->assertSame('変換先の改行コード指定が無効です。to_linefeed:aaa', $e->getMessage());
        }

        try {
            $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF1, static::TEST_DATA_ONLY_LF1, 'php://filter/write=convert.linefeed.cr:aaa/resource=php://temp');
            throw new \Exception();
        } catch (\Exception $e) {
            $this->assertSame('変換元の改行コード指定が無効です。from_linefeed:aaa', $e->getMessage());
        }
    }

    /**
     * LFへの変換テスト
     */
    public function testConvert2Lf() : void
    {
        $stream_wrapper = [StreamFilterConvertLinefeedSpec::toLf()->fromCr()];
        $this->assertWriteStreamFilterSame("\r\n\n", "\r\n\r", $stream_wrapper);

        $stream_wrapper = [StreamFilterConvertLinefeedSpec::toLf()->fromCr()];
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF1, static::TEST_DATA_ONLY_CR1, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF2, static::TEST_DATA_ONLY_CR2, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF3, static::TEST_DATA_ONLY_CR3, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF4, static::TEST_DATA_ONLY_CR4, $stream_wrapper);

        $stream_wrapper = [StreamFilterConvertLinefeedSpec::toLf()->fromCrLf()];
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF1, static::TEST_DATA_ONLY_CRLF1, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF2, static::TEST_DATA_ONLY_CRLF2, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF3, static::TEST_DATA_ONLY_CRLF3, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF4, static::TEST_DATA_ONLY_CRLF4, $stream_wrapper);

        $stream_wrapper = [StreamFilterConvertLinefeedSpec::toLf()->fromAll()];
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF1, static::TEST_DATA_ONLY_LF1, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF2, static::TEST_DATA_ONLY_LF2, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF3, static::TEST_DATA_ONLY_LF3, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF4, static::TEST_DATA_ONLY_LF4, $stream_wrapper);

        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF1, static::TEST_DATA_ONLY_CR1, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF2, static::TEST_DATA_ONLY_CR2, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF3, static::TEST_DATA_ONLY_CR3, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF4, static::TEST_DATA_ONLY_CR4, $stream_wrapper);

        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF1, static::TEST_DATA_ONLY_CRLF1, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF2, static::TEST_DATA_ONLY_CRLF2, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF3, static::TEST_DATA_ONLY_CRLF3, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF4, static::TEST_DATA_ONLY_CRLF4, $stream_wrapper);

        $stream_wrapper = [StreamFilterConvertLinefeedSpec::toLf()];
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF1, static::TEST_DATA_ONLY_LF1, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF2, static::TEST_DATA_ONLY_LF2, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF3, static::TEST_DATA_ONLY_LF3, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF4, static::TEST_DATA_ONLY_LF4, $stream_wrapper);

        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF1, static::TEST_DATA_ONLY_CR1, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF2, static::TEST_DATA_ONLY_CR2, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF3, static::TEST_DATA_ONLY_CR3, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF4, static::TEST_DATA_ONLY_CR4, $stream_wrapper);

        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF1, static::TEST_DATA_ONLY_CRLF1, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF2, static::TEST_DATA_ONLY_CRLF2, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF3, static::TEST_DATA_ONLY_CRLF3, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF4, static::TEST_DATA_ONLY_CRLF4, $stream_wrapper);
    }

    /**
     * CRへの変換テスト
     */
    public function testConvert2Cr() : void
    {
        $stream_wrapper = [StreamFilterConvertLinefeedSpec::toCr()->fromLf()];
        $this->assertWriteStreamFilterSame("\n\r\r", "\n\r\n", $stream_wrapper);

        $stream_wrapper = [StreamFilterConvertLinefeedSpec::toCr()->fromLf()];
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR1, static::TEST_DATA_ONLY_LF1, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR2, static::TEST_DATA_ONLY_LF2, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR3, static::TEST_DATA_ONLY_LF3, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR4, static::TEST_DATA_ONLY_LF4, $stream_wrapper);

        $stream_wrapper = [StreamFilterConvertLinefeedSpec::toCr()->fromCrLf()];
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR1, static::TEST_DATA_ONLY_CRLF1, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR2, static::TEST_DATA_ONLY_CRLF2, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR3, static::TEST_DATA_ONLY_CRLF3, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR4, static::TEST_DATA_ONLY_CRLF4, $stream_wrapper);

        $stream_wrapper = [StreamFilterConvertLinefeedSpec::toCr()];
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR1, static::TEST_DATA_ONLY_LF1, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR2, static::TEST_DATA_ONLY_LF2, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR3, static::TEST_DATA_ONLY_LF3, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR4, static::TEST_DATA_ONLY_LF4, $stream_wrapper);

        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR1, static::TEST_DATA_ONLY_CR1, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR2, static::TEST_DATA_ONLY_CR2, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR3, static::TEST_DATA_ONLY_CR3, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR4, static::TEST_DATA_ONLY_CR4, $stream_wrapper);

        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR1, static::TEST_DATA_ONLY_CRLF1, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR2, static::TEST_DATA_ONLY_CRLF2, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR3, static::TEST_DATA_ONLY_CRLF3, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR4, static::TEST_DATA_ONLY_CRLF4, $stream_wrapper);

        $stream_wrapper = [StreamFilterConvertLinefeedSpec::toCr()->fromAll()];
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR1, static::TEST_DATA_ONLY_LF1, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR2, static::TEST_DATA_ONLY_LF2, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR3, static::TEST_DATA_ONLY_LF3, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR4, static::TEST_DATA_ONLY_LF4, $stream_wrapper);

        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR1, static::TEST_DATA_ONLY_CR1, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR2, static::TEST_DATA_ONLY_CR2, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR3, static::TEST_DATA_ONLY_CR3, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR4, static::TEST_DATA_ONLY_CR4, $stream_wrapper);

        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR1, static::TEST_DATA_ONLY_CRLF1, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR2, static::TEST_DATA_ONLY_CRLF2, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR3, static::TEST_DATA_ONLY_CRLF3, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR4, static::TEST_DATA_ONLY_CRLF4, $stream_wrapper);
    }

    /**
     * CRLFへの変換テスト
     */
    public function testConvert2CrLf() : void
    {
        $stream_wrapper = [StreamFilterConvertLinefeedSpec::toCrLf()->fromCr()];
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF1, static::TEST_DATA_ONLY_CR1, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF2, static::TEST_DATA_ONLY_CR2, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF3, static::TEST_DATA_ONLY_CR3, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF4, static::TEST_DATA_ONLY_CR4, $stream_wrapper);

        $stream_wrapper = [StreamFilterConvertLinefeedSpec::toCrLf()->fromLf()];
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF1, static::TEST_DATA_ONLY_LF1, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF2, static::TEST_DATA_ONLY_LF2, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF3, static::TEST_DATA_ONLY_LF3, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF4, static::TEST_DATA_ONLY_LF4, $stream_wrapper);

        $stream_wrapper = [StreamFilterConvertLinefeedSpec::toCrLf()->fromAll()];
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF1, static::TEST_DATA_ONLY_CR1, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF2, static::TEST_DATA_ONLY_CR2, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF3, static::TEST_DATA_ONLY_CR3, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF4, static::TEST_DATA_ONLY_CR4, $stream_wrapper);

        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF1, static::TEST_DATA_ONLY_LF1, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF2, static::TEST_DATA_ONLY_LF2, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF3, static::TEST_DATA_ONLY_LF3, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF4, static::TEST_DATA_ONLY_LF4, $stream_wrapper);

        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF1, static::TEST_DATA_ONLY_CRLF1, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF2, static::TEST_DATA_ONLY_CRLF2, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF3, static::TEST_DATA_ONLY_CRLF3, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF4, static::TEST_DATA_ONLY_CRLF4, $stream_wrapper);

        $stream_wrapper = [StreamFilterConvertLinefeedSpec::toCrLf()];
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF1, static::TEST_DATA_ONLY_CR1, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF2, static::TEST_DATA_ONLY_CR2, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF3, static::TEST_DATA_ONLY_CR3, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF4, static::TEST_DATA_ONLY_CR4, $stream_wrapper);

        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF1, static::TEST_DATA_ONLY_LF1, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF2, static::TEST_DATA_ONLY_LF2, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF3, static::TEST_DATA_ONLY_LF3, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF4, static::TEST_DATA_ONLY_LF4, $stream_wrapper);

        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF1, static::TEST_DATA_ONLY_CRLF1, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF2, static::TEST_DATA_ONLY_CRLF2, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF3, static::TEST_DATA_ONLY_CRLF3, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF4, static::TEST_DATA_ONLY_CRLF4, $stream_wrapper);
    }

    /**
     * i/7テスト
     */
    public function testI01() : void
    {
        $actual     = implode(ConvertLinefeedFilter::LF, [
            '1111,1111',
            '2222,2222',
            '3333,3333',
            '4444,4444',
            '5555,5555',
            '6666,6666',
            '7777,7777',
            '8888,8888',
        ]);

        $expected   = [
            ['1111', '1111'],
            ['2222', '2222'],
            ['3333', '3333'],
            ['4444', '4444'],
            ['5555', '5555'],
            ['6666', '6666'],
            ['7777', '7777'],
            ['8888', '8888'],
        ];

        $read_parameters    = [
            StreamFilterConvertEncodingSpec::toUtf8()->fromSjisWin(),
            StreamFilterConvertLinefeedSpec::toCrLf(),
        ];

        $stream_chunk_size  = 1024;
        $this->assertCsvInputStreamFilterSame($expected, $actual, $stream_chunk_size, $read_parameters);
    }
}
