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

namespace fw3\streams\filters\utilitys\specs\entitys;

use fw3\streams\filters\ConvertLinefeedFilter;
use fw3\streams\filters\utilitys\specs\interfaces\StreamFilterSpecInterface;
use fw3\streams\filters\utilitys\specs\interfaces\StreamFilterSpecTrait;
use fw3\streams\filters\utilitys\specs\StreamFilterConvertLinefeedSpec;

/**
 * ストリームフィルタ：ConvertLinefeedSpec
 */
class StreamFilterConvertLinefeedSpecEntity implements StreamFilterSpecInterface
{
    use StreamFilterSpecTrait;

    // ==============================================
    // const
    // ==============================================
    // フィルタ名
    // ----------------------------------------------
    /**
     * @var string デフォルトフィルタ名
     */
    public const DEFAULT_FILTER_NAME    = 'convert.linefeed';

    // ----------------------------------------------
    // フィルタパラメータ
    // ----------------------------------------------
    /**
     * @var string パラメータオプション間のセパレータ
     */
    public const PARAMETER_OPTION_SEPARATOR = ':';

    // ----------------------------------------------
    // 改行コード表現の文字列表現
    // ----------------------------------------------
    /**
     * @var string 改行コード表現の文字列表現：CRLF
     */
    public const CRLF   = ConvertLinefeedFilter::STR_CRLF;

    /**
     * @var string 改行コード表現の文字列表現：CR
     */
    public const CR     = ConvertLinefeedFilter::STR_CR;

    /**
     * @var string 改行コード表現の文字列表現：LF
     */
    public const LF     = ConvertLinefeedFilter::STR_LF;

    /**
     * @var string 改行コード表現の文字列表現：ALL (変換元用全種類受け入れ設定)
     */
    public const ALL    = ConvertLinefeedFilter::STR_ALL;

    /**
     * @var string 改行コード表現の文字列表現：変換元改行コード表現のデフォルト
     */
    public const FROM_LINEFEED_DEFAULT  = self::ALL;

    /**
     * @var string デフォルトの変換後改行コード表現
     */
    public const DEFAULT_TO_LINEFEED   = self::LF;

    /**
     * @var string デフォルトの変換前改行コード表現
     */
    public const DEFAULT_FROM_LINEFEED = self::ALL;

    /**
     * @var array 文字列表現の改行から改行コード表現への変換マップ
     */
    public const LINEFEED_MAP  = ConvertLinefeedFilter::LINEFEED_MAP;

    /**
     * @var array 許可する変換元改行コード表現の文字列リスト
     */
    public const ALLOW_FROM_LINEFEED_STR_LIST    = ConvertLinefeedFilter::ALLOW_FROM_LINEFEED_STR_LIST;

    // ==============================================
    // property
    // ==============================================
    // フィルタパラメータ
    // ----------------------------------------------
    /**
     * @var string 変換後の改行コード表現
     */
    protected $toLinefeed   = self::DEFAULT_TO_LINEFEED;

    /**
     * @var string 変換前の改行コード表現
     */
    protected $fromLinefeed = self::DEFAULT_FROM_LINEFEED;

    // ==============================================
    // static method
    // ==============================================
    /**
     * ストリームフィルタスペックインスタンスを返します。
     *
     * @param  array                           $spec スペック
     *                                               [
     *                                               'to_linefeed'   => 変換後の改行コード表現
     *                                               'from_linefeed' => 変換元の改行コード表現
     *                                               ]
     * @return StreamFilterConvertLinefeedSpec このインスタンス
     */
    public static function factory(array $spec = []): StreamFilterConvertLinefeedSpecEntity
    {
        $instance   = new static();

        if (!empty($spec)) {
            if (isset($spec['to_linefeed']) || \array_key_exists('to_linefeed', $spec)) {
                $instance->toLinefeed($spec['to_linefeed']);
            }

            if (isset($spec['from_linefeed']) || \array_key_exists('from_linefeed', $spec)) {
                $instance->fromLinefeed($spec['from_linefeed']);
            }
        }

        return $instance;
    }

    // ==============================================
    // method
    // ==============================================
    /**
     * 変換後の改行コード表現を取得・設定します。
     *
     * @return string|StreamFilterConvertLinefeedSpec 変換後の改行コード表現またはこのインスタンス
     */
    public function to(?string $to_line_feed = null)
    {
        if (\func_num_args() === 0) {
            return $this->toLinefeed;
        }

        if (!isset(static::LINEFEED_MAP[$to_line_feed])) {
            throw new \Exception(\sprintf('未知の改行コード表現を指定されました。encoding:%s', $to_line_feed));
        }

        $this->toLinefeed = $to_line_feed;

        return $this;
    }

    /**
     * 変換後の改行コード表現としてCRを設定したスペックエンティティを返します。
     *
     * @return StreamFilterConvertLinefeedSpecEntity 変換後の改行コード表現としてCRを設定したスペックエンティティ
     */
    public function toCr(): StreamFilterConvertLinefeedSpecEntity
    {
        return $this->to(static::CR);
    }

    /**
     * 変換後の改行コード表現としてLFを設定したスペックエンティティを返します。
     *
     * @return StreamFilterConvertLinefeedSpecEntity 変換後の改行コード表現としてLFを設定したスペックエンティティ
     */
    public function toLf(): StreamFilterConvertLinefeedSpecEntity
    {
        return $this->to(static::LF);
    }

    /**
     * 変換後の改行コード表現としてCRLFを設定したスペックエンティティを返します。
     *
     * @return StreamFilterConvertLinefeedSpecEntity 変換後の改行コード表現としてCRLFを設定したスペックエンティティ
     */
    public function toCrLf(): StreamFilterConvertLinefeedSpecEntity
    {
        return $this->to(static::CRLF);
    }

    /**
     * 変換前の改行コード表現を取得・設定します。
     *
     * @return string|StreamFilterConvertLinefeedSpec 変換前の改行コード表現またはこのインスタンス
     */
    public function from(?string $from_line_feed = null)
    {
        if (\func_num_args() === 0) {
            return $this->fromLinefeed;
        }

        if (!isset(static::ALLOW_FROM_LINEFEED_STR_LIST[$from_line_feed])) {
            throw new \Exception(\sprintf('未知の改行コード表現を指定されました。encoding:%s', $from_line_feed));
        }

        $this->fromLinefeed = $from_line_feed;

        return $this;
    }

    /**
     * 変換前の改行コード表現としてCRを設定したスペックエンティティを返します。
     *
     * @return StreamFilterConvertLinefeedSpecEntity 変換前の改行コード表現としてCRを設定したスペックエンティティ
     */
    public function fromCr(): StreamFilterConvertLinefeedSpecEntity
    {
        return $this->from(static::CR);
    }

    /**
     * 変換前の改行コード表現としてLFを設定したスペックエンティティを返します。
     *
     * @return StreamFilterConvertLinefeedSpecEntity 変換前の改行コード表現としてLFを設定したスペックエンティティ
     */
    public function fromLf(): StreamFilterConvertLinefeedSpecEntity
    {
        return $this->from(static::LF);
    }

    /**
     * 変換前の改行コード表現としてCRLFを設定したスペックエンティティを返します。
     *
     * @return StreamFilterConvertLinefeedSpecEntity 変換前の改行コード表現としてCRLFを設定したスペックエンティティ
     */
    public function fromCrLf(): StreamFilterConvertLinefeedSpecEntity
    {
        return $this->from(static::CRLF);
    }

    /**
     * 変換前の改行コード表現としてALLを設定したスペックエンティティを返します。
     *
     * @return StreamFilterConvertLinefeedSpecEntity 変換前の改行コード表現としてALLを設定したスペックエンティティ
     */
    public function fromAll(): StreamFilterConvertLinefeedSpecEntity
    {
        return $this->from(static::ALL);
    }

    /**
     * チェーンフィルタ用文字列を構築して返します。
     *
     * @return string チェーンフィルタ用文字列
     */
    public function build(): string
    {
        return \sprintf('%s.%s%s%s', StreamFilterConvertLinefeedSpec::filterName(), $this->toLinefeed, static::PARAMETER_OPTION_SEPARATOR, $this->fromLinefeed);
    }

    /**
     * Windows用の設定を行います。
     *
     * @return StreamFilterConvertLinefeedSpecEntity このインスタンス
     */
    public function setupForWindows($from_line_feed = self::DEFAULT_FROM_LINEFEED): StreamFilterConvertLinefeedSpecEntity
    {
        return $this->toCrLf()->from($from_line_feed);
    }

    /**
     * Unix系用の設定を行います。
     *
     * @return StreamFilterConvertLinefeedSpecEntity このインスタンス
     */
    public function setupForUnix($from_line_feed = self::DEFAULT_FROM_LINEFEED): StreamFilterConvertLinefeedSpecEntity
    {
        return $this->toLf()->from($from_line_feed);
    }
}
