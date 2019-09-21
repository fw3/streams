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

namespace fw3\tests\streams\traits;

/**
 * ストリームフィルタテスト支援特性
 */
trait StreamFilterTestTrait
{
    /**
     * 与えられたストリームで書き込み時のストリームフィルタをアサーションします。
     *
     * @param   string  $expected       予想される値
     * @param   string  $value          実行する値
     * @param   array   $steram_wrapper ストリームコンテキスト
     */
    protected function assertWriteStreamFilterSame(string $expected, string $value, array $steram_wrapper) : void
    {
        $steram_wrapper['resource']   = 'php://temp';

        $write_stream   = $this->convertSteramWrapper($steram_wrapper);

        $fp     = @\fopen($write_stream, 'ab');
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
     * @param   string  $expected       予想される値
     * @param   string  $value          実行する値
     * @param   array   $steram_wrapper ストリームコンテキスト
     */
    protected function assertWriteStreamFilterNotSame(string $expected, string $value, array $steram_wrapper) : void
    {
        $steram_wrapper['resource']   = 'php://temp';

        $write_stream   = $this->convertSteramWrapper($steram_wrapper);

        $fp     = @\fopen($write_stream, 'ab');
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
     * @param   array   $expected           予想される値
     * @param   string  $csv_text           実行する値
     * @param   int     $stream_chunk_size  ストリームラッパーのチャンクサイズ
     * @param   array   $steram_wrapper     ストリームコンテキスト
     */
    protected function assertCsvInputStreamFilterSame(array $expected, string $csv_text, int $stream_chunk_size, array $steram_wrapper) : void
    {
        $steram_wrapper['resource']   = 'php://temp';

        $write_stream   = $this->convertSteramWrapper($steram_wrapper);

        $fp     = @\fopen($write_stream, 'ab');

        @\fwrite($fp, $csv_text);

        @\rewind($fp);

        if (function_exists('stream_set_chunk_size')) {
            \stream_set_chunk_size($fp, $stream_chunk_size);
        }

        if (function_exists('stream_set_read_buffer')) {
            \stream_set_read_buffer($fp, $stream_chunk_size);
        }

        $actual = [];
        for (;($row = \fgetcsv($fp, 1024)) !== FALSE;$actual[] = $row);

        @\fclose($fp);

        $this->assertSame($expected, $actual);
    }

    /**
     * 与えられたストリームでCSV出力をアサーションします。
     *
     * @param   string  $expected           予想される値
     * @param   array   $csv_data           実行する値
     * @param   int     $stream_chunk_size  ストリームラッパーのチャンクサイズ
     * @param   array   $steram_wrapper     ストリームコンテキスト
     */
    protected function assertCsvOutputStreamFilterSame(string $expected, array $csv_data, int $stream_chunk_size, array $steram_wrapper) : void
    {
        $steram_wrapper['resource']   = 'php://temp';

        $write_stream   = $this->convertSteramWrapper($steram_wrapper);

        $fp     = @\fopen($write_stream, 'ab');

        if (function_exists('stream_set_chunk_size')) {
            \stream_set_chunk_size($fp, $stream_chunk_size);
        }

        if (function_exists('stream_set_read_buffer')) {
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
     * Stream Wrapper設定を文字列表現に変換します。
     *
     * @param   array   $steram_wrapper ストリームラッパー設定
     * @return  string  ストリームラッパー設定
     */
    protected function convertSteramWrapper(array $steram_wrapper) : string
    {
        $stack  = [];
        foreach ($steram_wrapper as $key => $context) {
            $stack[]    = \sprintf('%s=%s', $key, \implode('|', (array) $context));
        }

        return \sprintf('php://filter/%s', \implode('/', $stack));
    }

    /**
     * 整数値で表現されたコードポイントをUTF-8文字に変換する。
     *
     * @param   int     $code_point UTF-8文字に変換したいコードポイント
     * @return  string  コードポイントから作成したUTF-8文字
     */
    protected function int2utf8($code_point) {
        //UTF-16コードポイント内判定
        if ($code_point < 0) {
            throw new \Exception(sprintf('%1$s is out of range UTF-16 code point (0x000000 - 0x10FFFF)', $code_point));
        }
        if (0x10FFFF < $code_point) {
            throw new \Exception(sprintf('0x%1$X is out of range UTF-16 code point (0x000000 - 0x10FFFF)', $code_point));
        }

        //サロゲートペア判定
        if (0xD800 <= $code_point && $code_point <= 0xDFFF) {
            throw new \Exception(sprintf('0x%X is in of range surrogate pair code point (0xD800 - 0xDFFF)', $code_point));
        }

        //1番目のバイトのみでchr関数が使えるケース
        if ($code_point < 0x80) {
            return \chr($code_point);
        }

        //2番目のバイトを考慮する必要があるケース
        if ($code_point < 0xA0) {
            return \chr(0xC0 | $code_point >> 6) . \chr(0x80 | $code_point & 0x3F);
        }

        //数値実体参照表記からの変換
        return \html_entity_decode('&#'. $code_point .';');
    }
}
