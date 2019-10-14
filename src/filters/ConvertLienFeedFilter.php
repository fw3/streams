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

namespace fw3\streams\filters;

/**
 * 行末の改行コードを変換するストリームフィルタクラスです。
 */
class ConvertLienFeedFilter extends \php_user_filter
{
    //==============================================
    // const
    //==============================================
    /**
     * @var string  改行コードの文字列表現：CRLF
     */
    public const STR_CRLF   = 'CRLF';

    /**
     * @var string  改行コードの文字列表現：CR
     */
    public const STR_CR    = 'CR';

    /**
     * @var string  改行コードの文字列表現：LF
     */
    public const STR_LF    = 'LF';

    /**
     * @var string  改行コードの文字列表現：ALL (変換元用全種類受け入れ設定)
     */
    public const STR_ALL   = 'ALL';

    /**
     * @var string  改行コードの文字列表現：変換元改行コードのデフォルト
     */
    public const FROM_LINEFEED_DEFAULT  = self::STR_ALL;

    /**
     * @var string  改行コード：CRLF
     */
    public const CRLF   = "\r\n";

    /**
     * @var string  改行コード：CR
     */
    public const CR     = "\r";

    /**
     * @var string  改行コード：LF
     */
    public const LF     = "\n";

    /**
     * @var array   文字列表現の改行から改行コードへの変換マップ
     */
    protected const LINEFEED_MAP  = [
        self::STR_CR    => self::CR,
        self::STR_LF    => self::LF,
        self::STR_CRLF  => self::CRLF,
    ];

    /**
     * @var array   許可する変換元改行コードの文字列リスト
     */
    protected const ALLOW_FROM_LINEFEED_STR_LIST    = [
        self::STR_CR    => self::STR_CR,
        self::STR_LF    => self::STR_LF,
        self::STR_CRLF  => self::STR_CRLF,
        self::STR_ALL   => self::STR_ALL,
    ];

    //==============================================
    // property
    //==============================================
    /**
     * @var string  変換先の改行コード
     */
    protected $toLinefeed       = null;

    /**
     * @var string  変換先の改行コードの文字列表現
     */
    protected $toStrLinefeed    = null;

    /**
     * @var string  変換元の改行コードの文字列表現
     */
    protected $fromStrLinefeed  = null;

    //==============================================
    // method
    //==============================================
    /**
     * インスタンス生成時の処理
     *
     * @return  bool    instance生成に成功した場合はtrue、そうでなければfalse (falseを返した場合、フィルタの登録が失敗したものと見なされる)
     * @see \php_user_filter::onCreate()
     */
    public function onCreate()
    {
        //==============================================
        // フィルタ名フォーマット確認
        //==============================================
        if (false === $option_separate_position = \strrpos($this->filtername, '.')) {
            throw new \Exception(\sprintf('フィルタ名の指定の中にオプション区切り文字(.)がありません。filtername:%s', $this->filtername));
        }

        //==============================================
        // フィルタオプションの確定
        //==============================================
        $filter_option_part = \substr($this->filtername, $option_separate_position + 1);
        if (false === $parameter_separate_position = \strpos($filter_option_part, ':')) {
            // to linefeedがない場合
            $raw_to_linefeed    = $filter_option_part;
            $raw_from_linefeed  =static::FROM_LINEFEED_DEFAULT;
        } else {
            // to linefeed, from linefeedが共にある場合
            $raw_to_linefeed    = \substr($filter_option_part, 0, $parameter_separate_position);
            $raw_from_linefeed  = \substr($filter_option_part, $parameter_separate_position + 1);
        }

        $to_linefeed    = \strtoupper($raw_to_linefeed);
        $from_linefeed  = \strtoupper($raw_from_linefeed);

        //----------------------------------------------
        // 使用可能なエンコーディングかどうか検証
        //----------------------------------------------
        if (!isset(static::LINEFEED_MAP[$to_linefeed])) {
            throw new \Exception(\sprintf('変換先の改行コード指定が無効です。to_linefeed:%s', $raw_to_linefeed));
        }

        if (!isset(static::ALLOW_FROM_LINEFEED_STR_LIST[$from_linefeed])) {
            throw new \Exception(\sprintf('変換元の改行コード指定が無効です。from_linefeed:%s', $raw_from_linefeed));
        }

        if ($to_linefeed === $from_linefeed) {
            throw new \Exception(\sprintf('変換前後の改行コード指定が同じです。to_linefeed:%s, from_linefeed:%s', $to_linefeed, $from_linefeed));
        }

        //==============================================
        // プロパティ初期化
        //==============================================
        $this->toLinefeed       = static::LINEFEED_MAP[$to_linefeed];
        $this->toStrLinefeed    = static::ALLOW_FROM_LINEFEED_STR_LIST[$to_linefeed];
        $this->fromStrLinefeed  = static::ALLOW_FROM_LINEFEED_STR_LIST[$from_linefeed];

        //==============================================
        // 処理の終了
        //==============================================
        return true;
    }

    /**
     * フィルタ
     *
     * @param   resource    $in         元のバケットオブジェクト
     * @param   resource    $out        変更内容を適用するためのバケットオブジェクト
     * @param   int         $consumed   変更したデータ長
     * @param   bool        $closing    フィルタチェインの最後の処理かどうか
     * @return  int         処理を終えたときの状態
     *     PSFS_PASS_ON                  ：フィルタの処理が成功し、データがoutバケット群に保存された
     *     PSFS_FEED_ME                  ：フィルタの処理は成功したが、返すデータがない。ストリームあるいは一つ前のフィルタから、追加のデータが必要
     *     PSFS_ERR_FATAL (デフォルト)   ：フィルタで対処不能なエラーが発生し、処理を続行できない
     * @see \php_user_filter::filter()
     */
    public function filter($in, $out, &$consumed, $closing)
    {
        //==============================================
        // 主処理
        //==============================================
        for (;$bucket = \stream_bucket_make_writeable($in);) {
            // 変換対象文字列長
            $from_length    = 0;

            // 変換後文字列長
            $to_length      = 0;

            //----------------------------------------------
            // 末尾改行文字の検出
            //----------------------------------------------
            for ($i = $bucket->datalen;0 < $i;--$i) {
                $char = \substr($bucket->data, $i - 1, 1);
                switch ($this->fromStrLinefeed) {
                    case static::STR_CR:    // CRから変換
                        // 変換対象の文字列ではない場合は次の文字へ
                        if ($char === static::CR) {
                            // CRLFだった場合は読み飛ばす
                            if ($i < $bucket->datalen && \substr($bucket->data, $i - 1, 2) === static::CRLF) {
                                --$i;
                                break;
                            }

                            // 変換対象の文字列である場合は操作用カウンタを更新
                            ++$from_length;
                            ++$to_length;

                            continue;
                        }
                        break 2;
                    case static::STR_LF:    // LFから変換
                        if ($char === static::LF) {
                            // CRLFだった場合は読み飛ばす
                            if ($i > 1 && \substr($bucket->data, $i - 2, 2) === static::CRLF) {
                                --$i;
                                break;
                            }

                            // 変換対象の文字列である場合は操作用カウンタを更新
                            ++$from_length;
                            ++$to_length;

                            continue;
                        }
                        break 2;
                    case static::STR_CRLF:  // CRLFから変換
                        if ($i > 1 && \substr($bucket->data, $i - 2, 2) === static::CRLF) {
                            --$i;
                            ++$from_length;
                            $to_length   += 2;  // 対象がCRLFなため2を足す
                            continue;
                        }

                        break 2;
                    case static::STR_ALL:   // CRLF、CR、LFの順から変換
                        if ($i > 1 && \substr($bucket->data, $i - 2, 2) === static::CRLF) {
                            --$i;
                            ++$from_length;
                            $to_length   += 2;  // 対象がCRLFなため2を足す
                            continue;
                        }

                        if ($char === static::CR) {
                            ++$from_length;
                            ++$to_length;
                            continue;
                        }

                        if ($char === static::LF) {
                            ++$from_length;
                            ++$to_length;
                            continue;
                        }
                        break 2;
                }
            }

            // 改行対象文字列が存在するなら置き換えを行う
            if ($to_length > 0) {
                $bucket->data       = \substr($bucket->data, 0, -$to_length) . \str_repeat($this->toLinefeed, $from_length);
                $bucket->datalen    = \strlen($bucket->data);
            }
            $consumed   += $bucket->datalen;

            \stream_bucket_append($out, $bucket);
        }

        //==============================================
        // 処理の終了
        //==============================================
        return \PSFS_PASS_ON;
    }
}
