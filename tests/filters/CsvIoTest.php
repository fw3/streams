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
use fw3\streams\filters\ConvertEncodingFilter;
use fw3\streams\filters\ConvertLienFeedFilter;
use fw3\tests\streams\traits\StreamFilterTestTrait;

/**
 * エンコーディングを変換するストリームフィルタクラスのテスト
 */
class CsvIoTest extends TestCase
{
    use StreamFilterTestTrait;

    /**
     * @var string  テストデータ：ダメ文字開始
     */
    protected const TEST_DATA_SIMPLE_TEXT1    = 'ソソソソん';

    /**
     * @var string  テストデータ：ダメ文字+セパレータ
     */
    protected const TEST_DATA_SIMPLE_TEXT2    = 'ソ ソ ソ ソ ソ ';

    /**
     * @var string  テストデータ：複数のダメ文字
     */
    protected const TEST_DATA_SIMPLE_TEXT3    = 'ソソソソん①㈱㌔髙﨑纊ソｱｲｳｴｵあいうえおabc';

    /**
     * Setup
     */
    protected function setUp(): void
    {
        \stream_filter_register('convert.encoding.*', ConvertEncodingFilter::class);
        \stream_filter_register('line_feed.*', ConvertLienFeedFilter::class);

        ConvertEncodingFilter::startChangeLocale();
    }

    /**
     * Windows向けCSV出力テスト
     */
    public function testCsvOutput() : void
    {
        $steram_wrapper = [
            'write' => [
                'convert.encoding.SJIS-win',
                'line_feed.crlf',
            ],
        ];

        $expected   = \mb_convert_encoding(\implode(
            ConvertLienFeedFilter::CRLF,
            [
                \implode(',', [static::TEST_DATA_SIMPLE_TEXT1, '"'. static::TEST_DATA_SIMPLE_TEXT2 .'"', static::TEST_DATA_SIMPLE_TEXT3]),
                \implode(',', ['"'. static::TEST_DATA_SIMPLE_TEXT2 .'"', static::TEST_DATA_SIMPLE_TEXT3, static::TEST_DATA_SIMPLE_TEXT1]),
                \implode(',', [static::TEST_DATA_SIMPLE_TEXT3, static::TEST_DATA_SIMPLE_TEXT1, '"'. static::TEST_DATA_SIMPLE_TEXT2 .'"']),
                ''
            ]
        ), 'SJIS-win', 'UTF-8');

        $csv_data   = [
            [static::TEST_DATA_SIMPLE_TEXT1, static::TEST_DATA_SIMPLE_TEXT2, static::TEST_DATA_SIMPLE_TEXT3],
            [static::TEST_DATA_SIMPLE_TEXT2, static::TEST_DATA_SIMPLE_TEXT3, static::TEST_DATA_SIMPLE_TEXT1],
            [static::TEST_DATA_SIMPLE_TEXT3, static::TEST_DATA_SIMPLE_TEXT1, static::TEST_DATA_SIMPLE_TEXT2],
        ];

        $stream_chunk_size  = 1024;

        $this->assertCsvOutputStreamFilterSame($expected, $csv_data, $stream_chunk_size, $steram_wrapper);
    }

    /**
     * Windows向けCSV入力テスト
     */
    public function testCsvInput() : void
    {
        $steram_wrapper = [
            'read' => [
                'convert.encoding.UTF-8',
            ]
        ];

        $expected   = [
            [static::TEST_DATA_SIMPLE_TEXT1, static::TEST_DATA_SIMPLE_TEXT2, static::TEST_DATA_SIMPLE_TEXT3],
            [static::TEST_DATA_SIMPLE_TEXT2, static::TEST_DATA_SIMPLE_TEXT3, static::TEST_DATA_SIMPLE_TEXT1],
            [static::TEST_DATA_SIMPLE_TEXT3, static::TEST_DATA_SIMPLE_TEXT1, static::TEST_DATA_SIMPLE_TEXT2],
        ];

        $csv_text   = \mb_convert_encoding(\implode(
            ConvertLienFeedFilter::CRLF,
            [
                \implode(',', [static::TEST_DATA_SIMPLE_TEXT1, '"'. static::TEST_DATA_SIMPLE_TEXT2 .'"', static::TEST_DATA_SIMPLE_TEXT3]),
                \implode(',', ['"'. static::TEST_DATA_SIMPLE_TEXT2 .'"', static::TEST_DATA_SIMPLE_TEXT3, static::TEST_DATA_SIMPLE_TEXT1]),
                \implode(',', [static::TEST_DATA_SIMPLE_TEXT3, static::TEST_DATA_SIMPLE_TEXT1, '"'. static::TEST_DATA_SIMPLE_TEXT2 .'"']),
                ''
            ]
        ), 'SJIS-win', 'UTF-8');

        $stream_chunk_size  = 1024;

        $this->assertCsvInputStreamFilterSame($expected, $csv_text, $stream_chunk_size, $steram_wrapper);
    }

    /**
     * Teardown
     */
    protected function tearDown(): void
    {
        ConvertEncodingFilter::endChangeLocale();
    }
}
