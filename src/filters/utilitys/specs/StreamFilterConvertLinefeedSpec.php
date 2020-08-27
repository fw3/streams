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

use fw3\streams\filters\utilitys\specs\entitys\StreamFilterConvertLinefeedSpecEntity;

/**
 * ストリームフィルタ：ConvertLinefeedSpec
 */
abstract class StreamFilterConvertLinefeedSpec
{
    //==============================================
    // const
    //==============================================
    // フィルタ名
    //----------------------------------------------
    /**
     * @var string  デフォルトフィルタ名
     */
    public const DEFAULT_FILTER_NAME    = StreamFilterConvertLinefeedSpecEntity::DEFAULT_FILTER_NAME;

    //----------------------------------------------
    // フィルタパラメータ
    //----------------------------------------------
    /**
     * @var string  パラメータオプション間のセパレータ
     */
    public const PARAMETER_OPTION_SEPARATOR = StreamFilterConvertLinefeedSpecEntity::PARAMETER_OPTION_SEPARATOR;

    //----------------------------------------------
    // 改行コード表現の文字列表現
    //----------------------------------------------
    /**
     * @var string  改行コード表現の文字列表現：CRLF
     */
    public const CRLF   = StreamFilterConvertLinefeedSpecEntity::CRLF;

    /**
     * @var string  改行コード表現の文字列表現：CR
     */
    public const CR     = StreamFilterConvertLinefeedSpecEntity::CR;

    /**
     * @var string  改行コード表現の文字列表現：LF
     */
    public const LF     = StreamFilterConvertLinefeedSpecEntity::LF;

    /**
     * @var string  改行コード表現の文字列表現：ALL (変換元用全種類受け入れ設定)
     */
    public const ALL    = StreamFilterConvertLinefeedSpecEntity::ALL;

    /**
     * @var string  改行コード表現の文字列表現：変換元改行コード表現のデフォルト
     */
    public const FROM_LINEFEED_DEFAULT  = StreamFilterConvertLinefeedSpecEntity::ALL;

    /**
     * @var string  デフォルトの変換後改行コード表現
     */
    public const DEFAULT_TO_LINEFEED   = StreamFilterConvertLinefeedSpecEntity::LF;

    /**
     * @var string  デフォルトの変換前改行コード表現
     */
    public const DEFAULT_FROM_LINEFEED = StreamFilterConvertLinefeedSpecEntity::ALL;

    /**
     * @var array   文字列表現の改行から改行コード表現への変換マップ
     */
    public const LINEFEED_MAP  = StreamFilterConvertLinefeedSpecEntity::LINEFEED_MAP;

    /**
     * @var array   許可する変換元改行コード表現の文字列リスト
     */
    public const ALLOW_FROM_LINEFEED_STR_LIST    = StreamFilterConvertLinefeedSpecEntity::ALLOW_FROM_LINEFEED_STR_LIST;

    //==============================================
    // static property
    //==============================================
    // フィルタ名
    //----------------------------------------------
    /**
     * @var string  フィルタ名
     * @staticvar
     */
    protected static $filterName    = self::DEFAULT_FILTER_NAME;

    //==============================================
    // static method
    //==============================================
    /**
     * ストリームフィルタスペックインスタンスを返します。
     *
     * @param   array   $spec   スペック
     *  [
     *      'to_linefeed'   => 変換後の改行コード表現
     *      'from_linefeed' => 変換元の改行コード表現
     *  ]
     * @return  \fw3\streams\filters\utilitys\specs\entitys\StreamFilterConvertLinefeedSpecEntity   スペックエンティティ
     */
    public static function factory(array $spec = []) : \fw3\streams\filters\utilitys\specs\entitys\StreamFilterConvertLinefeedSpecEntity
    {
        return StreamFilterConvertLinefeedSpecEntity::factory($spec);
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
     * 変換後の改行コード表現を設定したスペックエンティティを返します。
     *
     * @param   string  $to_linefeed    変換後の改行コード表現
     * @return  \fw3\streams\filters\utilitys\specs\entitys\StreamFilterConvertLinefeedSpecEntity   変換後の改行コード表現を設定したスペックエンティティ
     */
    public static function to(string $to_linefeed) : StreamFilterConvertLinefeedSpecEntity
    {
        return static::factory()->to($to_linefeed);
    }

    /**
     * 変換後の改行コード表現としてCRを設定したスペックエンティティを返します。
     *
     * @return  \fw3\streams\filters\utilitys\specs\entitys\StreamFilterConvertLinefeedSpecEntity   変換後の改行コード表現としてCRを設定したスペックエンティティ
     */
    public static function toCr() : StreamFilterConvertLinefeedSpecEntity
    {
        return static::factory()->toCr();
    }

    /**
     * 変換後の改行コード表現としてLFを設定したスペックエンティティを返します。
     *
     * @return  \fw3\streams\filters\utilitys\specs\entitys\StreamFilterConvertLinefeedSpecEntity   変換後の改行コード表現としてLFを設定したスペックエンティティ
     */
    public static function toLf() : StreamFilterConvertLinefeedSpecEntity
    {
        return static::factory()->toLf();
    }

    /**
     * 変換後の改行コード表現としてCRLFを設定したスペックエンティティを返します。
     *
     * @return  \fw3\streams\filters\utilitys\specs\entitys\StreamFilterConvertLinefeedSpecEntity   変換後の改行コード表現としてCRLFを設定したスペックエンティティ
     */
    public static function toCrLf() : StreamFilterConvertLinefeedSpecEntity
    {
        return static::factory()->toCrLf();
    }

    /**
     * 変換前の改行コード表現を設定したスペックエンティティを返します。
     *
     * @param   string  $from_linefeed  変換前の改行コード表現
     * @return  \fw3\streams\filters\utilitys\specs\entitys\StreamFilterConvertLinefeedSpecEntity   変換前の改行コード表現を設定したスペックエンティティ
     */
    public static function from(string $from_linefeed) : StreamFilterConvertLinefeedSpecEntity
    {
        return static::factory()->from($from_linefeed);
    }

    /**
     * 変換前の改行コード表現としてCRを設定したスペックエンティティを返します。
     *
     * @return  \fw3\streams\filters\utilitys\specs\entitys\StreamFilterConvertLinefeedSpecEntity   変換前の改行コード表現としてCRを設定したスペックエンティティ
     */
    public static function fromCr() : StreamFilterConvertLinefeedSpecEntity
    {
        return static::factory()->fromCr();
    }

    /**
     * 変換前の改行コード表現としてLFを設定したスペックエンティティを返します。
     *
     * @return  \fw3\streams\filters\utilitys\specs\entitys\StreamFilterConvertLinefeedSpecEntity   変換前の改行コード表現としてLFを設定したスペックエンティティ
     */
    public static function fromLf() : StreamFilterConvertLinefeedSpecEntity
    {
        return static::factory()->fromLf();
    }

    /**
     * 変換前の改行コード表現としてCRLFを設定したスペックエンティティを返します。
     *
     * @return  \fw3\streams\filters\utilitys\specs\entitys\StreamFilterConvertLinefeedSpecEntity   変換前の改行コード表現としてCRLFを設定したスペックエンティティ
     */
    public static function fromCrLf() : StreamFilterConvertLinefeedSpecEntity
    {
        return static::factory()->fromCrLf();
    }

    /**
     * 変換前の改行コード表現としてALLを設定したスペックエンティティを返します。
     *
     * @return  \fw3\streams\filters\utilitys\specs\entitys\StreamFilterConvertLinefeedSpecEntity   変換前の改行コード表現としてALLを設定したスペックエンティティ
     */
    public static function fromAll() : StreamFilterConvertLinefeedSpecEntity
    {
        return static::factory()->fromAll();
    }

    /**
     * Windows用の設定を行ったスペックエンティティを返します。
     *
     * @param   string  $from_linefeed  変換前改行コード表現文字
     * @return  \fw3\streams\filters\utilitys\specs\entitys\StreamFilterConvertLinefeedSpecEntity   Windows用の設定を行ったスペックエンティティ
     */
    public static function setupForWindows($from_linefeed = self::DEFAULT_FROM_LINEFEED)
    {
        return static::factory()->setupForWindows($from_linefeed);
    }

    /**
     * Unix系用の設定を行ったスペックエンティティを返します。
     *
     * @param   string  $from_linefeed  変換前改行コード表現文字
     * @return  \fw3\streams\filters\utilitys\specs\entitys\StreamFilterConvertLinefeedSpecEntity   Unix系用の設定を行ったスペックエンティティ
     */
    public static function setupForUnix($from_linefeed = self::DEFAULT_FROM_LINEFEED)
    {
        return static::factory()->setupForUnix($from_linefeed);
    }
}
