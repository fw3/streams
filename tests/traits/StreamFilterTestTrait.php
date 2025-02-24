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

namespace fw3\tests\streams\traits;

use fw3\streams\filters\utilitys\StreamFilterSpec;
use fw3\tests\streams\test_utilitys\FgetCsvPolyfill;

/**
 * ストリームフィルタテスト支援特性
 */
trait StreamFilterTestTrait
{
    /**
     * 与えられたストリームで書き込み時のストリームフィルタをアサーションします。
     *
     * @param string       $expected     予想される値
     * @param string       $value        実行する値
     * @param array|string $stream_specs ストリームスペック
     */
    protected function assertWriteStreamFilterSame(string $expected, string $value, $stream_specs): void
    {
        if (\is_array($stream_specs)) {
            $stream_specs   = StreamFilterSpec::resourceTemp()->write($stream_specs)->build();
        }

        $fp     = @\fopen($stream_specs, 'ab');
        @\fwrite($fp, $value);

        \fseek($fp, 0, \SEEK_END);
        $length = \ftell($fp);

        @\rewind($fp);

        $actual = @\fread($fp, $length);

        @\fclose($fp);

        $this->assertSame($expected, $actual);
    }

    /**
     * 与えられたストリームで書き込み時のストリームフィルタが異なる結果になる事をアサーションします。
     *
     * @param string       $expected     予想される値
     * @param string       $value        実行する値
     * @param array|string $stream_specs ストリームスペック
     */
    protected function assertWriteStreamFilterNotSame(string $expected, string $value, $stream_specs): void
    {
        if (\is_array($stream_specs)) {
            $stream_specs   = StreamFilterSpec::resourceTemp()->write($stream_specs)->build();
        }

        $fp     = @\fopen($stream_specs, 'ab');
        @\fwrite($fp, $value);

        \fseek($fp, 0, \SEEK_END);
        $length = \ftell($fp);

        @\rewind($fp);

        $actual = @\fread($fp, $length);

        @\fclose($fp);

        $this->assertNotSame($expected, $actual);
    }

    /**
     * 与えられたストリームでCSV入力をアサーションします。
     *
     * @param array        $expected          予想される値
     * @param string       $csv_text          実行する値
     * @param int          $stream_chunk_size ストリームラッパーのチャンクサイズ
     * @param string|array $stream_specs      ストリームスペック
     */
    protected function assertCsvInputStreamFilterSame(array $expected, string $csv_text, int $stream_chunk_size, $stream_specs): void
    {
        if (\is_array($stream_specs)) {
            $stream_specs   = StreamFilterSpec::resourceTemp()->read($stream_specs)->build();
        }

        $fp     = @\fopen($stream_specs, 'ab');

        @\fwrite($fp, $csv_text);

        @\rewind($fp);

        if (\function_exists('stream_set_chunk_size')) {
            \stream_set_chunk_size($fp, $stream_chunk_size);
        }

        if (\function_exists('stream_set_read_buffer')) {
            \stream_set_read_buffer($fp, $stream_chunk_size);
        }

        $actual = [];

        for (;($row = \fgetcsv($fp, 1024, ',', '"', FgetCsvPolyfill::FGETCSV_ESCAPE)) !== false;$actual[] = $row);

        @\fclose($fp);

        $this->assertSame($expected, $actual);
    }

    /**
     * 与えられたストリームでCSV出力をアサーションします。
     *
     * @param string       $expected          予想される値
     * @param array        $csv_data          実行する値
     * @param int          $stream_chunk_size ストリームラッパーのチャンクサイズ
     * @param string|array $stream_specs      ストリームスペック
     */
    protected function assertCsvOutputStreamFilterSame(string $expected, array $csv_data, int $stream_chunk_size, $stream_specs): void
    {
        if (\is_array($stream_specs)) {
            $stream_specs   = StreamFilterSpec::resourceTemp()->write($stream_specs)->build();
        }

        $fp     = @\fopen($stream_specs, 'ab');

        if (\function_exists('stream_set_chunk_size')) {
            \stream_set_chunk_size($fp, $stream_chunk_size);
        }

        if (\function_exists('stream_set_read_buffer')) {
            \stream_set_read_buffer($fp, $stream_chunk_size);
        }

        foreach ($csv_data as $data) {
            @\fputcsv($fp, $data);
        }

        @\rewind($fp);

        $actual = '';

        while ($row = \fread($fp, 1024)) {
            $actual .= $row;
        }

        @\fclose($fp);

        $this->assertSame($expected, $actual);
    }

    /**
     * 整数値で表現されたコードポイントをUTF-8文字に変換する。
     *
     * @param  int    $code_point UTF-8文字に変換したいコードポイント
     * @return string コードポイントから作成したUTF-8文字
     */
    protected function int2utf8(int $code_point): string
    {
        // UTF-16コードポイント内判定
        if ($code_point < 0) {
            throw new \Exception(\sprintf('%1$s is out of range UTF-16 code point (0x000000 - 0x10FFFF)', $code_point));
        }

        if (0x10FFFF < $code_point) {
            throw new \Exception(\sprintf('0x%1$X is out of range UTF-16 code point (0x000000 - 0x10FFFF)', $code_point));
        }

        // サロゲートペア判定
        if (0xD800 <= $code_point && $code_point <= 0xDFFF) {
            throw new \Exception(\sprintf('0x%X is in of range surrogate pair code point (0xD800 - 0xDFFF)', $code_point));
        }

        // 1番目のバイトのみでchr関数が使えるケース
        if ($code_point < 0x80) {
            return \chr($code_point);
        }

        // 2番目のバイトを考慮する必要があるケース
        if ($code_point < 0xA0) {
            return \chr(0xC0 | $code_point >> 6) . \chr(0x80 | $code_point & 0x3F);
        }

        // 数値実体参照表記からの変換
        return \html_entity_decode('&#' . $code_point . ';');
    }
}
