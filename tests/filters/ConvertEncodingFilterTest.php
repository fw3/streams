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

namespace fw3\tests\streams\filters;

use fw3\streams\filters\ConvertEncodingFilter;
use fw3\streams\filters\utilitys\specs\StreamFilterConvertEncodingSpec;
use fw3\streams\filters\utilitys\StreamFilterSpec;
use fw3\tests\streams\traits\StreamFilterTestTrait;
use PHPUnit\Framework\TestCase;

/**
 * エンコーディングを変換するストリームフィルタクラスのテスト
 * @internal
 */
class ConvertEncodingFilterTest extends TestCase
{
    use StreamFilterTestTrait;

    /**
     * @var string 実行環境吸収用ラベル：Windows向け
     */
    protected const LOCALE_FOR_WINDOWS    = 'locale_for_windows';

    /**
     * @var string 実行環境吸収用ラベル：mac向け
     */
    protected const LOCALE_FOR_MAC  = 'locale_for_mac';

    /**
     * @var string 実行環境吸収用ラベル：Windows以外向け
     */
    protected const LOCALE_FOR_OTHER      = 'locale_for_other';

    /**
     * @var array 実行環境吸収用ロカールマップ
     */
    protected const LOCALE_MAP    = [
        self::LOCALE_FOR_WINDOWS    => [
            'Japanese_Japan.20127',
            'C',
            'Japanese_Japan.20932',
        ],
        self::LOCALE_FOR_MAC    => [
            'ja_JP.eucJP',
            'ja_JP.UTF-8',
            'ja_JP.SJIS',
        ],
        self::LOCALE_FOR_OTHER  => [
            'ja_JP.eucjp',
            'C',
            'ja_JP.ujis',
        ],
    ];

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
     * @var string テストデータ：代替文字
     */
    protected const TEST_DATA_SIMPLE_TEXT4           = '1艗1鎽1𩸽1';

    /**
     * @var string テストデータ：代替文字：消去
     */
    protected const TEST_DATA_SIMPLE_TEXT4_NONE     = '1111';

    /**
     * @var string テストデータ：代替文字：unicode
     */
    protected const TEST_DATA_SIMPLE_TEXT4_LONG     = '1U+82571U+93BD1U+29E3D1';

    /**
     * @var string テストデータ：代替文字：untity
     */
    protected const TEST_DATA_SIMPLE_TEXT4_ENTITY   = '1&#x8257;1&#x93BD;1&#x29E3D;1';

    /**
     * @var string テストデータ：代替文字：任意文字
     */
    protected const TEST_DATA_SIMPLE_TEXT4_WORD     = '1a1a1a1';

    /**
     * @var string テストデータ：代替文字：任意文字用コードポイント：a
     */
    protected const TEST_DATA_SIMPLE_TEXT4_CODE_POINT = 0x0061;

    /**
     * @var string システムバックアップ：ロケール
     */
    protected string $systemLocale  = '';

    /**
     * @var int|string システムバックアップ：代替文字
     */
    protected $systemSubstituteCharacter    = '';

    /**
     * @var null|array スタック検証用ロカールリスト
     */
    protected ?array $localeList    = null;

    /**
     * フィルタ名テスト
     *
     * @test
     */
    public function filterName(): void
    {
        $expected_test_data_simple_text1    = \mb_convert_encoding(static::TEST_DATA_SIMPLE_TEXT1, 'SJIS-win', 'UTF-8');

        StreamFilterSpec::registerConvertEncodingFilter('aaa');
        $this->assertWriteStreamFilterSame($expected_test_data_simple_text1, static::TEST_DATA_SIMPLE_TEXT1, [StreamFilterConvertEncodingSpec::toSjisWin()]);

        StreamFilterSpec::registerConvertEncodingFilter('aaa.bbb.ccc');
        $this->assertWriteStreamFilterSame($expected_test_data_simple_text1, static::TEST_DATA_SIMPLE_TEXT1, [StreamFilterConvertEncodingSpec::toSjisWin()]);

        StreamFilterSpec::registerConvertEncodingFilter();
    }

    /**
     * 例外テスト
     *
     * @test
     */
    public function exception(): void
    {
        try {
            $this->assertWriteStreamFilterSame('あ', 'あ', [StreamFilterConvertEncodingSpec::toUtf8()->fromUtf8()]);

            throw new \Exception();
        } catch (\Exception $e) {
            $this->assertSame('変換前後のエンコーディング名が同じです。to_encoding:UTF-8, from_encoding:UTF-8', $e->getMessage());
        }

        try {
            $stream_wrapper = 'php://filter/write=convert.encoding.aaa:UTF-8/resource=php://temp';
            $this->assertWriteStreamFilterSame('あ', 'あ', $stream_wrapper);

            throw new \Exception();
        } catch (\Exception $e) {
            $this->assertSame('変換先のエンコーディング名が無効です。to_encoding:aaa', $e->getMessage());
        }

        try {
            $stream_wrapper = 'php://filter/write=convert.encoding.UTF-8:aaa/resource=php://temp';
            $this->assertWriteStreamFilterSame('あ', 'あ', $stream_wrapper);

            throw new \Exception();
        } catch (\Exception $e) {
            $this->assertSame('変換元のエンコーディング名が無効です。from_encoding:aaa', $e->getMessage());
        }
    }

    /**
     * ロカール変更のテスト
     *
     * @test
     */
    public function locale(): void
    {
        try {
            ConvertEncodingFilter::startChangeLocale('asdfqwer');

            throw new \Exception();
        } catch (\Exception $e) {
            $this->assertSame('システムで使用できないロカールを指定されました。locale:asdfqwer', $e->getMessage());
        }

        $this->assertSame([$this->systemLocale], ConvertEncodingFilter::getLocaleStack());
        $this->assertSame(ConvertEncodingFilter::getSafeLocale(), ConvertEncodingFilter::currentLocale());

        $this->assertSame(ConvertEncodingFilter::getSafeLocale(), ConvertEncodingFilter::endChangeLocale());
        $this->assertSame([], ConvertEncodingFilter::getLocaleStack());
        $this->assertSame($this->systemLocale, ConvertEncodingFilter::currentLocale());

        $this->assertSame($this->systemLocale, ConvertEncodingFilter::startChangeLocale());
        $this->assertSame([$this->systemLocale], ConvertEncodingFilter::getLocaleStack());
        $this->assertSame(ConvertEncodingFilter::getSafeLocale(), ConvertEncodingFilter::currentLocale());

        $locale_list            = $this->localeList;
        $start_locale_stack     = [];

        foreach ($locale_list as $locale) {
            $start_locale_stack[]   = ConvertEncodingFilter::startChangeLocale($locale);
        }

        \end($locale_list);
        $this->assertSame(\array_merge([$this->systemLocale, ConvertEncodingFilter::getSafeLocale()], \array_slice($locale_list, 0, 2)), ConvertEncodingFilter::getLocaleStack());
        $this->assertSame(\current($locale_list), ConvertEncodingFilter::currentLocale());

        $this->assertSame(\current($locale_list), ConvertEncodingFilter::endChangeLocale(true));
        $this->assertSame([], ConvertEncodingFilter::getLocaleStack());
        $this->assertSame($this->systemLocale, ConvertEncodingFilter::currentLocale());

        $start_locale_stack     = [];

        foreach ($locale_list as $locale) {
            $start_locale_stack[]   = ConvertEncodingFilter::startChangeLocale($locale);
        }
        $start_locale_stack[]   = ConvertEncodingFilter::currentLocale();

        $end_locale_stack   = [
            ConvertEncodingFilter::endChangeLocale(),
            ConvertEncodingFilter::endChangeLocale(),
            ConvertEncodingFilter::endChangeLocale(),
            ConvertEncodingFilter::currentLocale(),
        ];

        try {
            ConvertEncodingFilter::endChangeLocale();

            throw new \Exception();
        } catch (\Exception $e) {
            $this->assertSame('ロカールスタックが空です。', $e->getMessage());
        }

        \krsort($end_locale_stack);
        $this->assertSame($start_locale_stack, \array_values($end_locale_stack));

        ConvertEncodingFilter::startChangeLocale();
    }

    /**
     * 文字コードが無効または存在しない場合の代替文字のテスト
     *
     * @test
     */
    public function substituteCharacter(): void
    {
        try {
            ConvertEncodingFilter::startChangeSubstituteCharacter(ConvertEncodingFilter::SUBSTITUTE_CHARACTER_MAX_CODE_POINT + 1);

            throw new \Exception();
        } catch (\Exception $e) {
            $this->assertSame('使用できない代替文字を与えられました。substitute_character:U+FFFF', $e->getMessage());
        }

        try {
            ConvertEncodingFilter::startChangeSubstituteCharacter('asdf');

            throw new \Exception();
        } catch (\Exception $e) {
            $this->assertSame('使用できない代替文字設定を与えられました。substitute_character:asdf', $e->getMessage());
        }

        $this->assertSame([$this->systemSubstituteCharacter], ConvertEncodingFilter::getSubstituteCharacterStack());
        $this->assertSame(ConvertEncodingFilter::SUBSTITUTE_CHARACTER_DEFAULT, ConvertEncodingFilter::currentSubstituteCharacter());

        $this->assertSame(ConvertEncodingFilter::SUBSTITUTE_CHARACTER_DEFAULT, ConvertEncodingFilter::endChangeSubstituteCharacter());
        $this->assertSame([], ConvertEncodingFilter::getSubstituteCharacterStack());
        $this->assertSame($this->systemSubstituteCharacter, ConvertEncodingFilter::currentSubstituteCharacter());

        $this->assertSame($this->systemSubstituteCharacter, ConvertEncodingFilter::startChangeSubstituteCharacter());
        $this->assertSame([$this->systemSubstituteCharacter], ConvertEncodingFilter::getSubstituteCharacterStack());
        $this->assertSame(ConvertEncodingFilter::SUBSTITUTE_CHARACTER_DEFAULT, ConvertEncodingFilter::currentSubstituteCharacter());

        $substitute_character_list      = \array_values(ConvertEncodingFilter::SUBSTITUTE_CHARACTER_MAP);
        $substitute_character_list[]    = ConvertEncodingFilter::SUBSTITUTE_CHARACTER_MAX_CODE_POINT;
        $substitute_character_list[]    = 1;

        $start_substitute_character_stack     = [];

        foreach ($substitute_character_list as $substitute_character) {
            $start_substitute_character_stack[]   = ConvertEncodingFilter::startChangeSubstituteCharacter($substitute_character);
        }

        \end($substitute_character_list);
        $this->assertSame(\array_merge([$this->systemSubstituteCharacter, ConvertEncodingFilter::SUBSTITUTE_CHARACTER_DEFAULT], \array_slice($substitute_character_list, 0, 4)), ConvertEncodingFilter::getSubstituteCharacterStack());
        $this->assertSame(\current($substitute_character_list), ConvertEncodingFilter::currentSubstituteCharacter());

        $this->assertSame(\current($substitute_character_list), ConvertEncodingFilter::endChangeSubstituteCharacter(true));
        $this->assertSame([], ConvertEncodingFilter::getSubstituteCharacterStack());
        $this->assertSame($this->systemSubstituteCharacter, ConvertEncodingFilter::currentSubstituteCharacter());

        $start_substitute_character_stack     = [];

        foreach ($substitute_character_list as $substitute_character) {
            $start_substitute_character_stack[]   = ConvertEncodingFilter::startChangeSubstituteCharacter($substitute_character);
        }
        $start_substitute_character_stack[]   = ConvertEncodingFilter::currentSubstituteCharacter();

        $end_substitute_character_stack   = [
            ConvertEncodingFilter::endChangeSubstituteCharacter(),
            ConvertEncodingFilter::endChangeSubstituteCharacter(),
            ConvertEncodingFilter::endChangeSubstituteCharacter(),
            ConvertEncodingFilter::endChangeSubstituteCharacter(),
            ConvertEncodingFilter::endChangeSubstituteCharacter(),
            ConvertEncodingFilter::currentSubstituteCharacter(),
        ];

        try {
            ConvertEncodingFilter::endChangeSubstituteCharacter();

            throw new \Exception();
        } catch (\Exception $e) {
            $this->assertSame('代替文字コードスタックが空です。', $e->getMessage());
        }

        \krsort($end_substitute_character_stack);
        $this->assertSame($start_substitute_character_stack, \array_values($end_substitute_character_stack));

        $stream_wrapper = [StreamFilterConvertEncodingSpec::toSjisWin()->fromUtf8()];

        switch ($this->systemSubstituteCharacter) {
            case ConvertEncodingFilter::SUBSTITUTE_CHARACTER_NONE:
                $system_substitute_character_text = static::TEST_DATA_SIMPLE_TEXT4_NONE;

                break;
            case ConvertEncodingFilter::SUBSTITUTE_CHARACTER_LONG:
                $system_substitute_character_text = static::TEST_DATA_SIMPLE_TEXT4_LONG;

                break;
            case ConvertEncodingFilter::SUBSTITUTE_CHARACTER_ENTITY:
                $system_substitute_character_text = static::TEST_DATA_SIMPLE_TEXT4_ENTITY;

                break;
            default:
                $system_substitute_character    = $this->systemSubstituteCharacter;

                if (\is_int($system_substitute_character)) {
                    $system_substitute_character    = $this->int2utf8($system_substitute_character);
                }
                $system_substitute_character_text = \implode($system_substitute_character, [1, 1, 1, 1]);

                break;
        }

        $this->assertWriteStreamFilterSame($system_substitute_character_text, static::TEST_DATA_SIMPLE_TEXT4, $stream_wrapper);

        ConvertEncodingFilter::startChangeSubstituteCharacter(ConvertEncodingFilter::SUBSTITUTE_CHARACTER_NONE);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_SIMPLE_TEXT4_NONE, static::TEST_DATA_SIMPLE_TEXT4, $stream_wrapper);
        ConvertEncodingFilter::endChangeSubstituteCharacter();

        ConvertEncodingFilter::startChangeSubstituteCharacter(ConvertEncodingFilter::SUBSTITUTE_CHARACTER_LONG);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_SIMPLE_TEXT4_LONG, static::TEST_DATA_SIMPLE_TEXT4, $stream_wrapper);
        ConvertEncodingFilter::endChangeSubstituteCharacter();

        ConvertEncodingFilter::startChangeSubstituteCharacter(ConvertEncodingFilter::SUBSTITUTE_CHARACTER_ENTITY);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_SIMPLE_TEXT4_ENTITY, static::TEST_DATA_SIMPLE_TEXT4, $stream_wrapper);
        ConvertEncodingFilter::endChangeSubstituteCharacter();

        ConvertEncodingFilter::startChangeSubstituteCharacter(static::TEST_DATA_SIMPLE_TEXT4_CODE_POINT);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_SIMPLE_TEXT4_WORD, static::TEST_DATA_SIMPLE_TEXT4, $stream_wrapper);
        ConvertEncodingFilter::endChangeSubstituteCharacter();

        ConvertEncodingFilter::startChangeSubstituteCharacter();
    }

    /**
     * システムデフォルトの文字エンコーディング検出順キャッシュを取得するテスト
     *
     * @test
     */
    public function defaultDetectEncodingListCache(): void
    {
        $mb_list_encodings  = \mb_list_encodings();
        $this->assertSame(\array_combine($mb_list_encodings, $mb_list_encodings), ConvertEncodingFilter::getDefaultDetectEncodingListCache());
    }

    /**
     * memory_limitの単位をintに変換するテスト
     *
     * @test
     */
    public function adjustMemoryLimitUnit(): void
    {
        $this->assertSame(1, ConvertEncodingFilter::adjustMemoryLimitUnit(1));
        $this->assertSame(1000, ConvertEncodingFilter::adjustMemoryLimitUnit(1000));

        $this->assertSame(1024, ConvertEncodingFilter::adjustMemoryLimitUnit('1K'));
        $this->assertSame(1024, ConvertEncodingFilter::adjustMemoryLimitUnit('1k'));
        $this->assertSame(1024 * 1024, ConvertEncodingFilter::adjustMemoryLimitUnit('1M'));
        $this->assertSame(1024 * 1024, ConvertEncodingFilter::adjustMemoryLimitUnit('1m'));
        $this->assertSame(1024 * 1024 * 1024, ConvertEncodingFilter::adjustMemoryLimitUnit('1G'));
        $this->assertSame(1024 * 1024 * 1024, ConvertEncodingFilter::adjustMemoryLimitUnit('1g'));

        $this->assertSame(1000, ConvertEncodingFilter::adjustMemoryLimitUnit(1000));
        $this->assertSame(123 * 1024, ConvertEncodingFilter::adjustMemoryLimitUnit('123K'));
        $this->assertSame(456 * 1024 * 1024, ConvertEncodingFilter::adjustMemoryLimitUnit('456M'));
        $this->assertSame(789 * 1024 * 1024 * 1024, ConvertEncodingFilter::adjustMemoryLimitUnit('789G'));
    }

    /**
     * Shift_JIS遅延判定文字列バッファサイズを変更するテスト
     *
     * @test
     */
    public function sjisSeparationPositionBufferSize(): void
    {
        $this->assertSame(ConvertEncodingFilter::SJIS_CHECK_DEFERRED_BUFFER_SIZE_DEFAULT, ConvertEncodingFilter::sjisSeparationPositionBufferSize());
        $this->assertSame(ConvertEncodingFilter::SJIS_CHECK_DEFERRED_BUFFER_SIZE_DEFAULT, ConvertEncodingFilter::sjisSeparationPositionBufferSize(1024));
        $this->assertSame(1024, ConvertEncodingFilter::sjisSeparationPositionBufferSize(ConvertEncodingFilter::SJIS_CHECK_DEFERRED_BUFFER_SIZE_DEFAULT));

        $memory_limit                       = ConvertEncodingFilter::adjustMemoryLimitUnit(\ini_get('memory_limit'));

        if ($memory_limit !== -1) {
            $sjis_check_deferred_buffer_size    = $memory_limit + 1;

            try {
                ConvertEncodingFilter::sjisSeparationPositionBufferSize($sjis_check_deferred_buffer_size);

                throw new \Exception();
            } catch (\Exception $e) {
                $this->assertSame(\sprintf('現在の設定で利用できるメモリ量を超過しています。%s / %s', $sjis_check_deferred_buffer_size, $memory_limit), $e->getMessage());
            }
        } else {
            $sjis_check_deferred_buffer_size    = ConvertEncodingFilter::sjisSeparationPositionBufferSize(\PHP_INT_MAX);
            $this->assertSame(\PHP_INT_MAX, ConvertEncodingFilter::sjisSeparationPositionBufferSize($sjis_check_deferred_buffer_size));
        }

        $this->assertSame(ConvertEncodingFilter::SJIS_CHECK_DEFERRED_BUFFER_SIZE_DEFAULT, ConvertEncodingFilter::sjisSeparationPositionBufferSize());
    }

    /**
     * デフォルト時の変換元エンコーディングの自動検出順を変更するテスト
     *
     * @test
     */
    public function detectOrder(): void
    {
        $this->assertSame(ConvertEncodingFilter::DETECT_ORDER_DEFAULT, ConvertEncodingFilter::detectOrder());
        $this->assertSame(ConvertEncodingFilter::DETECT_ORDER_DEFAULT, ConvertEncodingFilter::detectOrder(\mb_detect_order()));
        $this->assertSame(\mb_detect_order(), ConvertEncodingFilter::detectOrder());

        try {
            ConvertEncodingFilter::detectOrder(['aaa']);

            throw new \Exception();
        } catch (\Exception $e) {
            $this->assertSame('システムで使用できないエンコーディングを指定されました。encoding:aaa', $e->getMessage());
        }

        $this->assertSame(\mb_detect_order(), ConvertEncodingFilter::detectOrder(ConvertEncodingFilter::DETECT_ORDER_DEFAULT));
        $this->assertSame(ConvertEncodingFilter::DETECT_ORDER_DEFAULT, ConvertEncodingFilter::detectOrder());
    }

    /**
     * Shift_JISへの変換テスト
     *
     * @test
     */
    public function convert2Sjis(): void
    {
        $expected_test_data_simple_text1    = \mb_convert_encoding(static::TEST_DATA_SIMPLE_TEXT1, 'SJIS-win', 'UTF-8');

        $stream_wrapper = [StreamFilterConvertEncodingSpec::toSjisWin()];
        $this->assertWriteStreamFilterSame($expected_test_data_simple_text1, static::TEST_DATA_SIMPLE_TEXT1, $stream_wrapper);
        $this->assertWriteStreamFilterSame($expected_test_data_simple_text1, \mb_convert_encoding(static::TEST_DATA_SIMPLE_TEXT1, 'eucJP-win', 'UTF-8'), $stream_wrapper);

        $stream_wrapper = [StreamFilterConvertEncodingSpec::toSjisWin()->fromUtf8()];
        $this->assertWriteStreamFilterSame($expected_test_data_simple_text1, static::TEST_DATA_SIMPLE_TEXT1, $stream_wrapper);
        $this->assertWriteStreamFilterNotSame($expected_test_data_simple_text1, \mb_convert_encoding(static::TEST_DATA_SIMPLE_TEXT1, 'eucJP-win', 'UTF-8'), $stream_wrapper);

        $stream_wrapper = [StreamFilterConvertEncodingSpec::toSjisWin()->fromEucJpWin()];
        $this->assertWriteStreamFilterNotSame($expected_test_data_simple_text1, static::TEST_DATA_SIMPLE_TEXT1, $stream_wrapper);
        $this->assertWriteStreamFilterSame($expected_test_data_simple_text1, \mb_convert_encoding(static::TEST_DATA_SIMPLE_TEXT1, 'eucJP-win', 'UTF-8'), $stream_wrapper);
    }

    /**
     * EUC-JPへの変換テスト
     *
     * @test
     */
    public function convert2Euc(): void
    {
        $expected_test_data_simple_text1    = \mb_convert_encoding(static::TEST_DATA_SIMPLE_TEXT1, 'eucJP-win', 'UTF-8');

        $stream_wrapper = [StreamFilterConvertEncodingSpec::toEucJpWin()];
        $this->assertWriteStreamFilterSame($expected_test_data_simple_text1, static::TEST_DATA_SIMPLE_TEXT1, $stream_wrapper);
        $this->assertWriteStreamFilterSame($expected_test_data_simple_text1, \mb_convert_encoding(static::TEST_DATA_SIMPLE_TEXT1, 'SJIS-win', 'UTF-8'), $stream_wrapper);

        $stream_wrapper = [StreamFilterConvertEncodingSpec::toEucJpWin()->fromUtf8()];
        $this->assertWriteStreamFilterSame($expected_test_data_simple_text1, static::TEST_DATA_SIMPLE_TEXT1, $stream_wrapper);
        $this->assertWriteStreamFilterNotSame($expected_test_data_simple_text1, \mb_convert_encoding(static::TEST_DATA_SIMPLE_TEXT1, 'SJIS-win', 'UTF-8'), $stream_wrapper);

        $stream_wrapper = [StreamFilterConvertEncodingSpec::toEucJpWin()->fromSjisWin()];
        $this->assertWriteStreamFilterNotSame($expected_test_data_simple_text1, static::TEST_DATA_SIMPLE_TEXT1, $stream_wrapper);
        $this->assertWriteStreamFilterSame($expected_test_data_simple_text1, \mb_convert_encoding(static::TEST_DATA_SIMPLE_TEXT1, 'SJIS-win', 'UTF-8'), $stream_wrapper);
    }

    /**
     * UTF-8への変換テスト
     *
     * @test
     */
    public function convert2UTF8(): void
    {
        $expected_test_data_simple_text1    = static::TEST_DATA_SIMPLE_TEXT1;

        $stream_wrapper = [StreamFilterConvertEncodingSpec::toUtf8()];
        $this->assertWriteStreamFilterSame($expected_test_data_simple_text1, \mb_convert_encoding(static::TEST_DATA_SIMPLE_TEXT1, 'SJIS-win', 'UTF-8'), $stream_wrapper);
        $this->assertWriteStreamFilterSame($expected_test_data_simple_text1, \mb_convert_encoding(static::TEST_DATA_SIMPLE_TEXT1, 'eucJP-win', 'UTF-8'), $stream_wrapper);

        $stream_wrapper = [StreamFilterConvertEncodingSpec::toUtf8()->fromSjisWin()];
        $this->assertWriteStreamFilterSame($expected_test_data_simple_text1, \mb_convert_encoding(static::TEST_DATA_SIMPLE_TEXT1, 'SJIS-win', 'UTF-8'), $stream_wrapper);
        $this->assertWriteStreamFilterNotSame($expected_test_data_simple_text1, \mb_convert_encoding(static::TEST_DATA_SIMPLE_TEXT1, 'eucJP-win', 'UTF-8'), $stream_wrapper);

        $stream_wrapper = [StreamFilterConvertEncodingSpec::toUtf8()->fromEucjpWin()];
        $this->assertWriteStreamFilterNotSame($expected_test_data_simple_text1, \mb_convert_encoding(static::TEST_DATA_SIMPLE_TEXT1, 'SJIS-win', 'UTF-8'), $stream_wrapper);
        $this->assertWriteStreamFilterSame($expected_test_data_simple_text1, \mb_convert_encoding(static::TEST_DATA_SIMPLE_TEXT1, 'eucJP-win', 'UTF-8'), $stream_wrapper);
    }

    /**
     * 複雑なケース1のテスト
     *
     * @test
     */
    public function complex1(): void
    {
        $stream_wrapper = [StreamFilterConvertEncodingSpec::toUtf8()->fromSjisWin()];

        $expected   = [
            ['あかさた', 'なはまや'],
        ];

        $csv_text   = 'あかさた,なはまや';
        \mb_convert_variables('SJIS-win', 'UTF-8', $csv_text);

        $stream_chunk_size  = 3;

        $this->assertCsvInputStreamFilterSame($expected, $csv_text, $stream_chunk_size, $stream_wrapper);
    }

    /**
     * 複雑なケース2のテスト
     *
     * @test
     */
    public function complex2(): void
    {
        $stream_wrapper = [StreamFilterConvertEncodingSpec::toUtf8()->fromSjisWin()];

        $expected   = [
            [self::TEST_DATA_SIMPLE_TEXT1, self::TEST_DATA_SIMPLE_TEXT1],
        ];

        $csv_text   = \sprintf('%s,%s', self::TEST_DATA_SIMPLE_TEXT1, self::TEST_DATA_SIMPLE_TEXT1);
        \mb_convert_variables('SJIS-win', 'UTF-8', $csv_text);

        $stream_chunk_size  = 3;

        $this->assertCsvInputStreamFilterSame($expected, $csv_text, $stream_chunk_size, $stream_wrapper);
    }

    /**
     * 複雑なケース3のテスト
     *
     * @test
     */
    public function complex3(): void
    {
        $stream_wrapper = [StreamFilterConvertEncodingSpec::toUtf8()->fromSjisWin()];

        $expected   = [
            [static::TEST_DATA_SIMPLE_TEXT2, static::TEST_DATA_SIMPLE_TEXT2],
        ];

        $csv_text   = \sprintf('%s,%s', self::TEST_DATA_SIMPLE_TEXT2, self::TEST_DATA_SIMPLE_TEXT2);
        \mb_convert_variables('SJIS-win', 'UTF-8', $csv_text);

        $stream_chunk_size  = 2;

        ConvertEncodingFilter::sjisSeparationPositionBufferSize(1);

        $this->assertCsvInputStreamFilterSame($expected, $csv_text, $stream_chunk_size, $stream_wrapper);
    }

    /**
     * 複雑なケース4のテスト
     *
     * @test
     */
    public function complex4(): void
    {
        $stream_wrapper = [StreamFilterConvertEncodingSpec::toUtf8()->fromSjisWin()];

        $expected   = [
            [self::TEST_DATA_SIMPLE_TEXT3, self::TEST_DATA_SIMPLE_TEXT3],
        ];

        $csv_text   = \sprintf('%s,%s', self::TEST_DATA_SIMPLE_TEXT3, self::TEST_DATA_SIMPLE_TEXT3);
        \mb_convert_variables('SJIS-win', 'UTF-8', $csv_text);

        $stream_chunk_size  = 3;

        $this->assertCsvInputStreamFilterSame($expected, $csv_text, $stream_chunk_size, $stream_wrapper);
    }

    /**
     * Setup
     */
    protected function setUp(): void
    {
        $this->systemLocale                 = ConvertEncodingFilter::startChangeLocale();
        $this->systemSubstituteCharacter    = ConvertEncodingFilter::startChangeSubstituteCharacter();

        switch (\PHP_OS_FAMILY) {
            case 'Windows':
                $locale_label   = static::LOCALE_FOR_WINDOWS;

                break;
            case 'Darwin':
                $locale_label   = static::LOCALE_FOR_MAC;

                break;
            default:
                $locale_label   = static::LOCALE_FOR_OTHER;

                break;
        }

        $this->localeList   = static::LOCALE_MAP[$locale_label];

        ConvertEncodingFilter::detectOrder(ConvertEncodingFilter::DETECT_ORDER_DEFAULT);

        StreamFilterSpec::registerConvertEncodingFilter();
    }

    /**
     * Teardown
     */
    protected function tearDown(): void
    {
        ConvertEncodingFilter::endChangeSubstituteCharacter();
        ConvertEncodingFilter::endChangeLocale();
    }
}
