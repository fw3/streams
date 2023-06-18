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

namespace Tests\streams\filters\utilitys;

use fw3\streams\filters\ConvertEncodingFilter;
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
use fw3\streams\filters\utilitys\specs\StreamFilterConvertLinefeedSpec;
use fw3\streams\filters\utilitys\StreamFilterSpec;
use fw3\tests\streams\traits\StreamFilterTestTrait;
use PHPUnit\Framework\TestCase;

/**
 * ストリームフィルタスペックのテスト
 * @internal
 */
class StreamFilterSpecTest extends TestCase
{
    use StreamFilterTestTrait;

    /**
     * Specインスタンスのテスト
     *
     * @test
     */
    public function spec(): void
    {
        $streamFilterSpec                   = StreamFilterSpec::factory();
        $streamFilterConvertEncodingSpec    = StreamFilterConvertEncodingSpec::factory();
        $streamFilterConvertLinefeedSpec    = StreamFilterConvertLinefeedSpec::factory();

        $this->assertEquals($streamFilterSpec->with(), $streamFilterSpec);
        $this->assertNotSame($streamFilterSpec->with(), $streamFilterSpec);

        if (\version_compare(\PHP_VERSION, '8.1')) {
            $this->assertSame('php://filter/write=convert.encoding.CP932:default', $streamFilterSpec->with()->appendWriteChain($streamFilterConvertEncodingSpec->with()->setupForSjisOut())->build());
        } else {
            $this->assertSame('php://filter/write=convert.encoding.SJIS-win:default', $streamFilterSpec->with()->appendWriteChain($streamFilterConvertEncodingSpec->with()->setupForSjisOut())->build());
        }
        $this->assertSame('php://filter/write=convert.encoding.eucJP-win:default', $streamFilterSpec->with()->appendWriteChain($streamFilterConvertEncodingSpec->with()->setupForEucjpOut())->build());
        $this->assertSame('php://filter/write=convert.encoding.UTF-8:default', $streamFilterSpec->with()->appendWriteChain($streamFilterConvertEncodingSpec->with()->setupForUtf8Out())->build());

        $this->assertSame('php://filter/write=convert.linefeed.LF:ALL', $streamFilterSpec->with()->appendWriteChain($streamFilterConvertLinefeedSpec->with()->setupForUnix())->build());
        $this->assertSame('php://filter/write=convert.linefeed.CRLF:ALL', $streamFilterSpec->with()->appendWriteChain($streamFilterConvertLinefeedSpec->with()->setupForWindows())->build());

        $this->assertSame('convert.encoding.CP932:auto', StreamFilterConvertEncodingSpec::toSjisWin()->fromAuto()->build());
        $this->assertSame('convert.encoding.CP932:auto:UTF-8', StreamFilterConvertEncodingSpec::toSjisWin()->fromAuto('UTF-8')->build());

        $this->assertSame('convert.encoding.CP932:auto', StreamFilterConvertEncodingSpec::fromAuto()->toSjisWin()->build());
        $this->assertSame('convert.encoding.CP932:auto:UTF-8', StreamFilterConvertEncodingSpec::fromAuto('UTF-8')->toSjisWin()->build());

        $this->assertSame('php://filter/write=convert.encoding.CP932:auto/resource=php://memory', StreamFilterSpec::resourceMemory()->write([
            StreamFilterConvertEncodingSpec::toSjisWin()->fromAuto(),
        ])->build());

        $this->assertSame('php://filter/write=convert.encoding.CP932:auto:UTF-8/resource=php://memory', StreamFilterSpec::resourceMemory()->write([
            StreamFilterConvertEncodingSpec::toSjisWin()->fromAuto('UTF-8'),
        ])->build());

        $this->assertSame('php://filter/read=convert.encoding.CP932:auto/resource=php://memory', StreamFilterSpec::resourceMemory()->read([
            StreamFilterConvertEncodingSpec::toSjisWin()->fromAuto(),
        ])->build());

        $this->assertSame('php://filter/read=convert.encoding.CP932:auto:UTF-8/resource=php://memory', StreamFilterSpec::resourceMemory()->read([
            StreamFilterConvertEncodingSpec::toSjisWin()->fromAuto('UTF-8'),
        ])->build());
    }

    /**
     * 指定された名前のストリームフィルタが登録されているかのテスト
     *
     * @runInSeparateProcess
     *
     * @test
     */
    public function existStreamFilterName(): void
    {
        $this->assertFalse(StreamFilterSpec::registeredStreamFilterName(StreamFilterConvertEncodingSpec::registerFilterName()));
        $this->assertFalse(StreamFilterSpec::registeredStreamFilterName(StreamFilterConvertLinefeedSpec::registerFilterName()));

        $this->assertFalse(StreamFilterSpec::registeredStreamFilterName(StreamFilterConvertEncodingSpec::DEFAULT_FILTER_NAME));
        $this->assertFalse(StreamFilterSpec::registeredStreamFilterName(StreamFilterConvertLinefeedSpec::DEFAULT_FILTER_NAME));

        StreamFilterSpec::registerConvertEncodingFilter();
        StreamFilterSpec::registerConvertLinefeedFilter();

        $this->assertTrue(StreamFilterSpec::registeredStreamFilterName(StreamFilterConvertEncodingSpec::registerFilterName()));
        $this->assertTrue(StreamFilterSpec::registeredStreamFilterName(StreamFilterConvertLinefeedSpec::registerFilterName()));

        $this->assertFalse(StreamFilterSpec::registeredStreamFilterName(StreamFilterConvertEncodingSpec::DEFAULT_FILTER_NAME));
        $this->assertFalse(StreamFilterSpec::registeredStreamFilterName(StreamFilterConvertLinefeedSpec::DEFAULT_FILTER_NAME));
    }

    /**
     * CSV入出力を行うにあたって必要な事前・事後処理を行い、$callbackで指定された処理のテスト
     *
     * @test
     */
    public function decorateForCsv(): void
    {
        // ----------------------------------------------
        $system_locale                  = ConvertEncodingFilter::startChangeLocale();
        $system_substitute_character    = ConvertEncodingFilter::startChangeSubstituteCharacter();

        $expected   = [
            ['ソソソソん', 'ソ ソ ソ ソ ソ ', 'ソソソソん①㈱㌔髙﨑纊ソｱｲｳｴｵあいうえおabc'],
            ['ソソソソん', 'ソ ソ ソ ソ ソ ', 'ソソソソん①㈱㌔髙﨑纊ソｱｲｳｴｵあいうえおabc'],
            ['ソソソソん', 'ソ ソ ソ ソ ソ ', 'ソソソソん①㈱㌔髙﨑纊ソｱｲｳｴｵあいうえおabc'],
        ];
        $actual     = StreamFilterSpec::decorateForCsv(function() use ($expected) {
            $data   = $expected;

            $spec   = StreamFilterSpec::resourceTemp()->write([
                StreamFilterConvertEncodingSpec::toSjisWin()->fromUtf8(),
                StreamFilterConvertLinefeedSpec::toCrLf()->fromAll(),
            ])->read([
                StreamFilterConvertEncodingSpec::toUtf8()->fromSjisWin(),
            ]);

            $fp     = \fopen($spec->build(), 'r+b');

            // 書き込み
            foreach ($data as $datum) {
                \fputcsv($fp, $datum);
            }

            // 読み込み
            \rewind($fp);

            $rows   = [];

            for (;($row = \fgetcsv($fp, 1024)) !== false;$rows[] = $row);

            \fclose($fp);

            return $rows;
        });

        $this->assertEquals($expected, $actual);

        ConvertEncodingFilter::endChangeSubstituteCharacter();
        $this->assertEquals($system_substitute_character, ConvertEncodingFilter::currentSubstituteCharacter());

        ConvertEncodingFilter::endChangeLocale();
        $this->assertEquals($system_locale, ConvertEncodingFilter::currentLocale());

        // ----------------------------------------------
        $system_locale                  = ConvertEncodingFilter::startChangeLocale();
        $system_substitute_character    = ConvertEncodingFilter::startChangeSubstituteCharacter();

        $data   = [
            ['𩸽鎽艗神'],
        ];

        $expected   = [
            ['U+29E3DU+93BDU+8257神'],
        ];
        $actual     = StreamFilterSpec::decorateForCsv(function() use ($data) {
            $spec   = StreamFilterSpec::resourceTemp()->write([
                StreamFilterConvertEncodingSpec::toSjisWin()->fromUtf8(),
                StreamFilterConvertLinefeedSpec::toCrLf()->fromAll(),
            ])->read([
                StreamFilterConvertEncodingSpec::toUtf8()->fromSjisWin(),
            ]);

            $fp     = \fopen($spec->build(), 'r+b');

            // 書き込み
            foreach ($data as $datum) {
                \fputcsv($fp, $datum);
            }

            // 読み込み
            \rewind($fp);

            $rows   = [];

            for (;($row = \fgetcsv($fp, 1024)) !== false;$rows[] = $row);

            \fclose($fp);

            return $rows;
        }, ConvertEncodingFilter::SUBSTITUTE_CHARACTER_LONG);

        $this->assertEquals($expected, $actual);

        ConvertEncodingFilter::endChangeSubstituteCharacter();
        $this->assertEquals($system_substitute_character, ConvertEncodingFilter::currentSubstituteCharacter());

        ConvertEncodingFilter::endChangeLocale();
        $this->assertEquals($system_locale, ConvertEncodingFilter::currentLocale());

        // ----------------------------------------------
        $system_locale                  = ConvertEncodingFilter::startChangeLocale();
        $system_substitute_character    = ConvertEncodingFilter::startChangeSubstituteCharacter();

        $data   = [
            ['あかさたな'],
        ];

        $expected   = [
            ['あかさたな'],
        ];
        $actual     = StreamFilterSpec::decorateForCsv(function() use ($data) {
            $spec   = StreamFilterSpec::resourceTemp()->write([
                StreamFilterConvertEncodingSpec::toSjisWin(),
                StreamFilterConvertLinefeedSpec::toCrLf()->fromAll(),
            ])->read([
                StreamFilterConvertEncodingSpec::toUtf8(),
            ]);

            $fp     = \fopen($spec->build(), 'r+b');

            // 書き込み
            foreach ($data as $datum) {
                \fputcsv($fp, $datum);
            }

            // 読み込み
            \rewind($fp);

            $rows   = [];

            for (;($row = \fgetcsv($fp, 1024)) !== false;$rows[] = $row);

            \fclose($fp);

            return $rows;
        }, null, ['UTF-8', 'SJIS-win']);

        $this->assertEquals($expected, $actual);

        ConvertEncodingFilter::endChangeSubstituteCharacter();
        $this->assertEquals($system_substitute_character, ConvertEncodingFilter::currentSubstituteCharacter());

        ConvertEncodingFilter::endChangeLocale();
        $this->assertEquals($system_locale, ConvertEncodingFilter::currentLocale());

        // ----------------------------------------------
        $system_locale                  = ConvertEncodingFilter::startChangeLocale();
        $system_substitute_character    = ConvertEncodingFilter::startChangeSubstituteCharacter();

        $data   = [
            ['あかさたな'],
        ];

        $expected   = [
            ['あかさたな'],
        ];
        $actual     = StreamFilterSpec::decorateForCsv(function() use ($data) {
            $spec   = StreamFilterSpec::resourceTemp()->write([
                StreamFilterConvertEncodingSpec::toSjisWin()->fromUtf8(),
                StreamFilterConvertLinefeedSpec::toCrLf()->fromAll(),
            ])->read([
                StreamFilterConvertEncodingSpec::toUtf8()->fromSjisWin(),
            ]);

            $fp     = \fopen($spec->build(), 'r+b');

            // 書き込み
            foreach ($data as $datum) {
                \fputcsv($fp, $datum);
            }

            // 読み込み
            \rewind($fp);

            $rows   = [];

            for (;($row = \fgetcsv($fp, 1024)) !== false;$rows[] = $row);

            \fclose($fp);

            return $rows;
        }, null, null, ConvertEncodingFilter::getSafeLocale());

        $this->assertEquals($expected, $actual);

        ConvertEncodingFilter::endChangeSubstituteCharacter();
        $this->assertEquals($system_substitute_character, ConvertEncodingFilter::currentSubstituteCharacter());

        ConvertEncodingFilter::endChangeLocale();
        $this->assertEquals($system_locale, ConvertEncodingFilter::currentLocale());

        // ----------------------------------------------
        $expected   = [
        ];

        try {
            $actual     = StreamFilterSpec::decorateForCsv(function() use ($expected) {
                $data   = $expected;

                if (empty($data)) {
                    throw new \Exception('空の配列を指定されました。');
                }

                $spec   = StreamFilterSpec::resourceTemp()->write([
                    StreamFilterConvertEncodingSpec::toSjisWin()->fromUtf8(),
                    StreamFilterConvertLinefeedSpec::toCrLf()->fromAll(),
                ])->read([
                    StreamFilterConvertEncodingSpec::toUtf8()->fromSjisWin(),
                ]);

                $fp     = \fopen($spec->build(), 'r+b');

                // 書き込み
                foreach ($data as $datum) {
                    \fputcsv($fp, $datum);
                }

                // 読み込み
                \rewind($fp);

                $rows   = [];

                for (;($row = \fgetcsv($fp, 1024)) !== false;$rows[] = $row);

                \fclose($fp);

                return $rows;
            });
        } catch (\Exception $e) {
            $this->assertEquals('空の配列を指定されました。', $e->getMessage());

            $this->assertEquals($system_substitute_character, ConvertEncodingFilter::currentSubstituteCharacter());

            $this->assertEquals($system_locale, ConvertEncodingFilter::currentLocale());
        }
    }

    /**
     * リソースアクセサのテスト
     *
     * @test
     */
    public function resourceAccessor(): void
    {
        $this->assertInstanceOf(StreamFilterSpecEntity::class, $streamFilterSpecEntity = StreamFilterSpec::resource(__FILE__));
        $this->assertInstanceOf(FileResourceSpec::class, $streamFilterSpecEntity->resource());

        $this->assertInstanceOf(StreamFilterSpecEntity::class, $streamFilterSpecEntity = StreamFilterSpec::resource('zip://hoge.zip#huga.csv'));
        $this->assertInstanceOf(RawResourceSpec::class, $streamFilterSpecEntity->resource());

        $this->assertInstanceOf(StreamFilterSpecEntity::class, $streamFilterSpecEntity = StreamFilterSpec::resource(FileResourceSpec::factory(__FILE__)));
        $this->assertInstanceOf(FileResourceSpec::class, $streamFilterSpecEntity->resource());

        $this->assertInstanceOf(StreamFilterSpecEntity::class, $streamFilterSpecEntity = StreamFilterSpec::resourceFile(__FILE__));
        $this->assertInstanceOf(FileResourceSpec::class, $streamFilterSpecEntity->resource());

        $this->assertInstanceOf(StreamFilterSpecEntity::class, $streamFilterSpecEntity = StreamFilterSpec::resourceStdin());
        $this->assertInstanceOf(PhpStdinResourceSpec::class, $streamFilterSpecEntity->resource());

        $this->assertInstanceOf(StreamFilterSpecEntity::class, $streamFilterSpecEntity = StreamFilterSpec::resourceStdout());
        $this->assertInstanceOf(PhpStdoutResourceSpec::class, $streamFilterSpecEntity->resource());

        $this->assertInstanceOf(StreamFilterSpecEntity::class, $streamFilterSpecEntity = StreamFilterSpec::resourceInput());
        $this->assertInstanceOf(PhpInputResourceSpec::class, $streamFilterSpecEntity->resource());

        $this->assertInstanceOf(StreamFilterSpecEntity::class, $streamFilterSpecEntity = StreamFilterSpec::resourceOutput());
        $this->assertInstanceOf(PhpOutputResourceSpec::class, $streamFilterSpecEntity->resource());

        $this->assertInstanceOf(StreamFilterSpecEntity::class, $streamFilterSpecEntity = StreamFilterSpec::resourceFd());
        $this->assertInstanceOf(PhpFdResourceSpec::class, $streamFilterSpecEntity->resource());

        $this->assertInstanceOf(StreamFilterSpecEntity::class, $streamFilterSpecEntity = StreamFilterSpec::resourceMemory());
        $this->assertInstanceOf(PhpMemoryResourceSpec::class, $streamFilterSpecEntity->resource());

        $this->assertInstanceOf(StreamFilterSpecEntity::class, $streamFilterSpecEntity = StreamFilterSpec::resourceTemp());
        $this->assertInstanceOf(PhpTempResourceSpec::class, $streamFilterSpecEntity->resource());

        $this->assertInstanceOf(StreamFilterSpecEntity::class, $streamFilterSpecEntity = StreamFilterSpec::resourceRaw('zip://hoge.zip#huga.csv'));
        $this->assertInstanceOf(RawResourceSpec::class, $streamFilterSpecEntity->resource());

        $this->assertInstanceOf(StreamFilterSpecEntity::class, $streamFilterSpecEntity = StreamFilterSpec::resourceZip(__FILE__, 'hoge.txt'));
        $this->assertInstanceOf(ZipResourceSpec::class, $streamFilterSpecEntity->resource());
    }
}
