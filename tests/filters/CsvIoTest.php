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

namespace Tests\streams\filters;

use PHPUnit\Framework\TestCase;
use fw3\streams\filters\ConvertEncodingFilter;
use fw3\streams\filters\ConvertLinefeedFilter;
use fw3\streams\filters\utilitys\StreamFilterSpec;
use fw3\tests\streams\traits\StreamFilterTestTrait;
use fw3\tests\streams\test_utilitys\FgetCsvPolyfill;
use fw3\streams\filters\utilitys\specs\StreamFilterConvertEncodingSpec;
use fw3\streams\filters\utilitys\specs\StreamFilterConvertLinefeedSpec;
use fw3\streams\filters\utilitys\specs\entitys\resources\ZipResourceSpec;

\define('CsvIoTest_TEST_DATA_SIMPLE_TEXT4', "\xE3\x83\x8F\xE3\x82\x9A");
\define('CsvIoTest_TEST_EXPECTED_SIMPLE_TEXT4', \mb_convert_encoding(\mb_convert_encoding(\CsvIoTest_TEST_DATA_SIMPLE_TEXT4, 'CP932', 'UTF-8'), 'UTF-8', 'CP932'));

/**
 * エンコーディングを変換するストリームフィルタクラスのテスト
 * @internal
 */
class CsvIoTest extends TestCase
{
    use StreamFilterTestTrait;

    /**
     * @var string tests\resources\encrypted_test.zip用パスワード
     */
    protected const TEST_ENCRYPTED_TEST_PASSWORD = 'd/>967{6IpQ!55S4';

    /**
     * @var string テストデータ：ダメ文字開始
     */
    protected const TEST_DATA_SIMPLE_TEXT1    = 'ソソソソん';

    /**
     * @var string テストデータ：ダメ文字+セパレータ
     */
    protected const TEST_DATA_SIMPLE_TEXT2    = 'ソ ソ ソ ソ ソ ';

    /**
     * @var string テストデータ：複数のダメ文字
     */
    protected const TEST_DATA_SIMPLE_TEXT3    = 'ソソソソん①㈱㌔髙﨑纊ソｱｲｳｴｵあいうえおabc';

    /**
     * @var string テストデータ：合字
     */
    protected const TEST_DATA_SIMPLE_TEXT4    = CsvIoTest_TEST_DATA_SIMPLE_TEXT4;

    /**
     * @var string 期待値：合字
     */
    protected const TEST_EXPECTED_SIMPLE_TEXT4  = CsvIoTest_TEST_EXPECTED_SIMPLE_TEXT4;

    /**
     * Windows向けCSV出力テスト
     *
     * @test
     */
    public function csvOutput(): void
    {
        $write_parameters   = [
            StreamFilterConvertEncodingSpec::setupForSjisOut(),
            StreamFilterConvertLinefeedSpec::setupForWindows(),
        ];

        $expected   = \mb_convert_encoding(\implode(
            ConvertLinefeedFilter::CRLF,
            [
                \implode(',', [static::TEST_DATA_SIMPLE_TEXT1, '"' . static::TEST_DATA_SIMPLE_TEXT2 . '"', static::TEST_DATA_SIMPLE_TEXT3, static::TEST_DATA_SIMPLE_TEXT4]),
                \implode(',', ['"' . static::TEST_DATA_SIMPLE_TEXT2 . '"', static::TEST_DATA_SIMPLE_TEXT3, static::TEST_DATA_SIMPLE_TEXT1, static::TEST_DATA_SIMPLE_TEXT4]),
                \implode(',', [static::TEST_DATA_SIMPLE_TEXT3, static::TEST_DATA_SIMPLE_TEXT1, '"' . static::TEST_DATA_SIMPLE_TEXT2 . '"', static::TEST_DATA_SIMPLE_TEXT4]),
                '',
            ]
        ), 'CP932', 'UTF-8');

        $csv_data   = [
            [static::TEST_DATA_SIMPLE_TEXT1, static::TEST_DATA_SIMPLE_TEXT2, static::TEST_DATA_SIMPLE_TEXT3, static::TEST_EXPECTED_SIMPLE_TEXT4],
            [static::TEST_DATA_SIMPLE_TEXT2, static::TEST_DATA_SIMPLE_TEXT3, static::TEST_DATA_SIMPLE_TEXT1, static::TEST_EXPECTED_SIMPLE_TEXT4],
            [static::TEST_DATA_SIMPLE_TEXT3, static::TEST_DATA_SIMPLE_TEXT1, static::TEST_DATA_SIMPLE_TEXT2, static::TEST_EXPECTED_SIMPLE_TEXT4],
        ];

        $stream_chunk_size  = 1024;

        $this->assertCsvOutputStreamFilterSame($expected, $csv_data, $stream_chunk_size, $write_parameters);
    }

    /**
     * Windows向けCSV入力テスト
     *
     * @test
     */
    public function csvInput(): void
    {
        $read_parameters    = [
            StreamFilterConvertEncodingSpec::setupForUtf8Out(),
        ];

        $expected   = [
            [static::TEST_DATA_SIMPLE_TEXT1, static::TEST_DATA_SIMPLE_TEXT2, static::TEST_DATA_SIMPLE_TEXT3],
            [static::TEST_DATA_SIMPLE_TEXT2, static::TEST_DATA_SIMPLE_TEXT3, static::TEST_DATA_SIMPLE_TEXT1],
            [static::TEST_DATA_SIMPLE_TEXT3, static::TEST_DATA_SIMPLE_TEXT1, static::TEST_DATA_SIMPLE_TEXT2],
        ];

        $csv_text   = \mb_convert_encoding(\implode(
            ConvertLinefeedFilter::CRLF,
            [
                \implode(',', [static::TEST_DATA_SIMPLE_TEXT1, '"' . static::TEST_DATA_SIMPLE_TEXT2 . '"', static::TEST_DATA_SIMPLE_TEXT3]),
                \implode(',', ['"' . static::TEST_DATA_SIMPLE_TEXT2 . '"', static::TEST_DATA_SIMPLE_TEXT3, static::TEST_DATA_SIMPLE_TEXT1]),
                \implode(',', [static::TEST_DATA_SIMPLE_TEXT3, static::TEST_DATA_SIMPLE_TEXT1, '"' . static::TEST_DATA_SIMPLE_TEXT2 . '"']),
                '',
            ]
        ), 'SJIS-win', 'UTF-8');

        $stream_chunk_size  = 1024;

        $this->assertCsvInputStreamFilterSame($expected, $csv_text, $stream_chunk_size, $read_parameters);
    }

    /**
     * ZIPアーカイブサポート
     *
     * @test
     */
    public function zipSupport(): void
    {
        if (isset($_SERVER['GITHUB_ACTIONS']) && $_SERVER['GITHUB_ACTIONS'] && \PHP_OS_FAMILY === 'Windows') {
            $this->assertSame($mirror = '2023/06時点でGithub Actions上のWindowsでZIP拡張を有効にする方法が不明な為、一時的にスキップ。', $mirror);
        } else {
            $expected   = [
                ['同ソ', '島', 'に出展している', 'ickx'],
                ['を', 'よろしくね'],
            ];

            $test_root_dir      = \dirname(__DIR__);
            $test_resources_dir = \sprintf('%s/resources', $test_root_dir);

            // ==============================================
            $csv_zip_path           = \sprintf('%s/test.zip', $test_resources_dir);

            $spec   = StreamFilterSpec::resourceZip($csv_zip_path, 'dir/sjis.csv')->read([
                StreamFilterConvertEncodingSpec::toUtf8()->fromSjisWin(),
            ]);

            $rows       = [];
            $csvFile    = new \SplFileObject($spec->build(), 'r+b');
            $csvFile->setCsvControl(',', '"', FgetCsvPolyfill::FGETCSV_ESCAPE);
            $csvFile->setFlags(\SplFileObject::READ_CSV);

            for ($row = $csvFile->fgetcsv();$row !== false && $row !== null;$row = $csvFile->fgetcsv()) {
                $rows[] = $row;
            }

            $this->assertSame($expected, $rows);

            // 2023/06時点で暗号化ZIPは取り扱えない可能性が高い
            // // ==============================================
            // $encrypted_csv_zip_path = \sprintf('%s/encrypted_test.zip', $test_resources_dir);

            // $spec   = StreamFilterSpec::resourceZip($encrypted_csv_zip_path, 'dir/sjis.csv')->read([
            //     StreamFilterConvertEncodingSpec::toUtf8()->fromSjisWin(),
            // ]);

            // /** @var ZipResourceSpec $resource */
            // $resource   = $spec->resource();
            // $resource->setPassword(self::TEST_ENCRYPTED_TEST_PASSWORD);

            // $rows       = [];
            // $csvFile    = new \SplFileObject($spec->build(), 'r+b', false, $resource->createStreamContext());
            // $csvFile->setFlags(\SplFileObject::READ_CSV);

            // for ($row = $csvFile->fgetcsv();$row !== false;$row = $csvFile->fgetcsv()) {
            //     $rows[] = $row;
            // }

            // $this->assertSame($expected, $rows);
        }
    }

    /**
     * Setup
     */
    protected function setUp(): void
    {
        StreamFilterSpec::registerConvertEncodingFilter();
        StreamFilterSpec::registerConvertLinefeedFilter();

        ConvertEncodingFilter::startChangeLocale();
    }

    /**
     * Teardown
     */
    protected function tearDown(): void
    {
        ConvertEncodingFilter::endChangeLocale();
    }
}
