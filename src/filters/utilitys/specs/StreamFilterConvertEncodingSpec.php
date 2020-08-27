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

namespace fw3\streams\filters\utilitys\specs;

use fw3\streams\filters\utilitys\specs\entitys\StreamFilterConvertEncodingSpecEntity;

/**
 * ストリームフィルタ：ConvertEncodingFilterSpec
 */
abstract class StreamFilterConvertEncodingSpec
{
    //==============================================
    // const
    //==============================================
    // フィルタ名
    //----------------------------------------------
    /**
     * @var string  デフォルトフィルタ名
     */
    public const DEFAULT_FILTER_NAME    = StreamFilterConvertEncodingSpecEntity::DEFAULT_FILTER_NAME;

    //----------------------------------------------
    // フィルタパラメータ
    //----------------------------------------------
    /**
     * @var string  パラメータオプション間のセパレータ
     */
    public const PARAMETER_OPTION_SEPARATOR = StreamFilterConvertEncodingSpecEntity::PARAMETER_OPTION_SEPARATOR;

    //----------------------------------------------
    // Encoding
    //----------------------------------------------
    /**
     * @var string  変換元のエンコーディング：省略された場合のデフォルト値 （より精度の高い文字エンコーディング判定を行う）
     */
    public const FROM_ENCODING_DEFAULT  = StreamFilterConvertEncodingSpecEntity::FROM_ENCODING_DEFAULT;

    /**
     * @var string  変換元のエンコーディング：auto
     */
    public const FROM_ENCODING_AUTO     = StreamFilterConvertEncodingSpecEntity::FROM_ENCODING_AUTO;

    /**
     * @var array   変換元文字列に対してエンコーディング検出を行う変換元エンコーディングマップ
     */
    public const DETECT_FROM_ENCODING_MAP   = StreamFilterConvertEncodingSpecEntity::DETECT_FROM_ENCODING_MAP;

    /**
     * @var string  日本語処理系で多用するエンコーディング：UTF-8
     */
    public const ENCODING_NAME_UTF8         = StreamFilterConvertEncodingSpecEntity::ENCODING_NAME_UTF8;

    /**
     * @var string  日本語処理系で多用するエンコーディング：Shift_JIS（Windows-31J）
     */
    public const ENCODING_NAME_SJIS_WIN     = StreamFilterConvertEncodingSpecEntity::ENCODING_NAME_SJIS_WIN;

    /**
     * @var string  日本語処理系で多用するエンコーディング：EUC-JP（Windows-31JのEUC-JP互換表現）
     */
    public const ENCODING_NAME_EUCJP_WIN    = StreamFilterConvertEncodingSpecEntity::ENCODING_NAME_EUCJP_WIN;

    /**
     * @var string  デフォルトの変換後文字エンコーディング
     */
    public const DEFAULT_TO_ENCODING    = StreamFilterConvertEncodingSpecEntity::ENCODING_NAME_UTF8;

    /**
     * @var string  デフォルトの変換前文字エンコーディング
     */
    public const DEFAULT_FROM_ENCODING  = StreamFilterConvertEncodingSpecEntity::FROM_ENCODING_DEFAULT;

    //==============================================
    // static property
    //==============================================
    // フィルタ名
    //----------------------------------------------
    /**
     * @var string  フィルタ名
     * @staticvar
     */
    protected static $filterName    = StreamFilterConvertEncodingSpecEntity::DEFAULT_FILTER_NAME;

    //==============================================
    // static method
    //==============================================
    /**
     * ストリームフィルタスペックインスタンスを返します。
     *
     * @param   array   $spec   スペック
     *  [
     *      'to_encoding'   => 変換後のエンコーディング
     *      'from_encoding' => 変換元のエンコーディング
     *  ]
     * @return  \fw3\streams\filters\utilitys\specs\entitys\StreamFilterConvertEncodingSpecEntity    このインスタンス
     */
    public static function factory(?array $spec = []) : \fw3\streams\filters\utilitys\specs\entitys\StreamFilterConvertEncodingSpecEntity
    {
        return StreamFilterConvertEncodingSpecEntity::factory($spec);
    }

    /**
     * フィルタ名を取得・設定します。
     *
     * @param   string  $filter_name    フィルタ名
     * @return  string  フィルタ名またはこのクラスパス
     */
    public static function filterName(?string $filter_name = null)
    {
        if (\func_num_args() === 0) {
            return static::$filterName;
        }

        static::$filterName = $filter_name;
        return static::class;
    }

    /**
     * \stream_filter_register設定用フィルタ名を返します。
     *
     * @return  string  \stream_filter_register設定用フィルタ名
     */
    public static function registerFilterName()
    {
        return \sprintf('%s.*', static::filterName());
    }

    /**
     * 現在のフィルタ名のストリームフィルタが登録されているかを返します。
     *
     * @return  bool    現在のフィルタ名のストリームフィルタが登録されている場合はtrue、そうでない場合はfalse
     */
    public static function registeredFilterName()
    {
        return in_array(static::registerFilterName(), stream_get_filters(), true);
    }

    //==============================================
    // method
    //==============================================
    /**
     * 変換後の文字エンコーディングを設定したスペックエンティティを返します。
     *
     * @param   string  $to_encoding    変換後の文字エンコーディング
     * @return  \fw3\streams\filters\utilitys\specs\entitys\StreamFilterConvertEncodingSpecEntity    変換後の文字エンコーディングを設定したスペックエンティティ
     */
    public static function to(string $to_encoding)
    {
        return static::factory()->to($to_encoding);
    }

    /**
     * 変換後の文字エンコーディングをUTF8として設定したスペックエンティティを返します。
     *
     * @return  \fw3\streams\filters\utilitys\specs\entitys\StreamFilterConvertEncodingSpecEntity    変換後の文字エンコーディングをUTF8として設定したスペックエンティティ
     */
    public static function toUtf8()
    {
        return static::factory()->toUtf8();
    }

    /**
     * 変換後の文字エンコーディングをSJIS-winとして設定したスペックエンティティを返します。
     *
     * @return  \fw3\streams\filters\utilitys\specs\entitys\StreamFilterConvertEncodingSpecEntity    変換後の文字エンコーディングをSJIS-winとして設定したスペックエンティティ
     */
    public static function toSjisWin()
    {
        return static::factory()->toSjisWin();
    }

    /**
     * 変換後の文字エンコーディングをeucJP-winとして設定したスペックエンティティを返します。
     *
     * @return  \fw3\streams\filters\utilitys\specs\entitys\StreamFilterConvertEncodingSpecEntity    変換後の文字エンコーディングをeucJP-winとして設定したスペックエンティティ
     */
    public static function toEucJpWin()
    {
        return static::factory()->toEucJpWin();
    }

    /**
     * 変換前の文字エンコーディングを設定したスペックエンティティを返します。
     *
     * @param   string  $from_encoding  変換前の文字エンコーディング
     * @return  \fw3\streams\filters\utilitys\specs\entitys\StreamFilterConvertEncodingSpecEntity    変換前の文字エンコーディングを設定したスペックエンティティ
     */
    public static function from(string $from_encoding)
    {
        return static::factory()->from($from_encoding);
    }

    /**
     * 変換前の文字エンコーディングをUTF8として設定したスペックエンティティを返します。
     *
     * @return  \fw3\streams\filters\utilitys\specs\entitys\StreamFilterConvertEncodingSpecEntity    変換前の文字エンコーディングをUTF8として設定したスペックエンティティ
     */
    public static function fromUtf8()
    {
        return static::factory()->fromUtf8();
    }

    /**
     * 変換前の文字エンコーディングをSJIS-winとして設定したスペックエンティティを返します。
     *
     * @return  \fw3\streams\filters\utilitys\specs\entitys\StreamFilterConvertEncodingSpecEntity    変換前の文字エンコーディングをSJIS-winとして設定したスペックエンティティ
     */
    public static function fromSjisWin()
    {
        return static::factory()->fromSjisWin();
    }

    /**
     * 変換前の文字エンコーディングをeucJP-winとして設定したスペックエンティティを返します。
     *
     * @return  \fw3\streams\filters\utilitys\specs\entitys\StreamFilterConvertEncodingSpecEntity    変換前の文字エンコーディングをeucJP-winとして設定したスペックエンティティ
     */
    public static function fromEucjpWin()
    {
        return static::factory()->fromEucjpWin();
    }

    /**
     * 変換前の文字エンコーディングをdefaultとして設定したスペックエンティティを返します。
     *
     * @return  \fw3\streams\filters\utilitys\specs\entitys\StreamFilterConvertEncodingSpecEntity    変換前の文字エンコーディングをdefaultとして設定したスペックエンティティ
     */
    public static function fromDefault()
    {
        return static::factory()->fromDefault();
    }

    /**
     * 変換前の文字エンコーディングをautoとして設定したスペックエンティティを返します。
     *
     * @return  \fw3\streams\filters\utilitys\specs\entitys\StreamFilterConvertEncodingSpecEntity    変換前の文字エンコーディングをautoとして設定したスペックエンティティ
     */
    public static function fromAuto()
    {
        return static::factory()->fromAuto();
    }

    /**
     * Shift_JIS出力用の設定を行ったスペックエンティティを返します。
     *
     * @param   string  $from_encoding  変換前文字列エンコーディング
     * @return  \fw3\streams\filters\utilitys\specs\entitys\StreamFilterConvertEncodingSpecEntity    Shift_JIS出力用の設定を行ったスペックエンティティ
     */
    public static function setupForSjisOut($from_encoding = self::DEFAULT_FROM_ENCODING)
    {
        return static::factory()->setupForSjisOut($from_encoding);
    }

    /**
     * EUC-JP出力用の設定を行ったスペックエンティティを返します。
     *
     * @param   string  $from_encoding  変換前文字列エンコーディング
     * @return  \fw3\streams\filters\utilitys\specs\entitys\StreamFilterConvertEncodingSpecEntity    EUC-JP出力用の設定を行ったスペックエンティティ
     */
    public static function setupForEucjpOut($from_encoding = self::DEFAULT_FROM_ENCODING)
    {
        return static::factory()->setupForEucjpOut($from_encoding);
    }

    /**
     * UTF-8出力用の設定を行ったスペックエンティティを返します。
     *
     * @param   string  $from_encoding  変換前文字列エンコーディング
     * @return  \fw3\streams\filters\utilitys\specs\entitys\StreamFilterConvertEncodingSpecEntity    UTF-8出力用の設定を行ったスペックエンティティ
     */
    public static function setupForUtf8Out($from_encoding = self::DEFAULT_FROM_ENCODING)
    {
        return static::factory()->setupForUtf8Out($from_encoding);
    }
}
