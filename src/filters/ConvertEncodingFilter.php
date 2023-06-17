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

namespace fw3\streams\filters;

/**
 * エンコーディングを変換するストリームフィルタクラスです。
 */
class ConvertEncodingFilter extends \php_user_filter
{
    // ==============================================
    // const
    // ==============================================
    // ロカール設定関連
    // ----------------------------------------------
    /**
     * @var string LOCALE：ロカール情報を取得するためのもの
     */
    public const LOCALE_FOR_GETTER  = '0';

    /**
     * @var string LOCALE：デフォルトの作業用ロカール
     */
    public const LOCALE_FOR_DEFAULT = 'ja_JP.utf8';

    /**
     * @var string LOCALE：Windows環境用の作業用ロカール
     *
     * ！！注意！！
     * PHP7以上ではこのロカールを使用すると正常にfgetcsvを使用できなくなる。
     * "C"ロカールを使用すること。
     */
    public const LOCALE_FOR_WINDOWS_DEFAULT = 'Japanese_Japan.932';

    /**
     * @var string LOCALE：Windows環境用の代替作業用ロカール
     *
     * ！！注意！！
     * PHP5.3.6以下でこのロカールを使用すると正常にfgetcsvを使用できなくなる。
     * "Japanese_Japan.932"ロカールを使用すること。
     */
    public const LOCALE_FOR_WINDOWS_ALTERNATIVE = 'C';

    /**
     * @var string LOCALE：mac環境用のUTF-8代替ロカール
     */
    public const LOCALE_FOR_MAC_UTF_8_ALTERNATIVE = 'ja_JP.UTF-8';

    // ----------------------------------------------
    // エンコーディング変換設定
    // ----------------------------------------------
    /**
     * @var string 変換元のエンコーディング：省略された場合のデフォルト値 （detectOrderスタティックプロパティの値を使用する）
     */
    public const FROM_ENCODING_DEFAULT      = 'default';

    /**
     * @var string 変換元のエンコーディング：auto
     */
    public const FROM_ENCODING_AUTO         = 'auto';

    /**
     * @var string 日本語処理系で多用するエンコーディング：UTF-8
     */
    public const ENCODING_NAME_UTF8         = 'UTF-8';

    /**
     * @var string 日本語処理系で多用するエンコーディング：Shift_JIS（Windows-31J）
     */
    public const ENCODING_NAME_SJIS_WIN     = 'SJIS-win';

    /**
     * @var string 日本語処理系で多用するエンコーディング：CP932（Shift_JIS（Windows-31J））
     *             PHP8.1での誤った修正によりPHP8.1時点ではSJIS-winではなくCP932を利用する必要がある。
     */
    public const ENCODING_NAME_CP932   = 'CP932';

    /**
     * @var string 日本語処理系で多用するエンコーディング：EUC-JP（Windows-31JのEUC-JP互換表現）
     */
    public const ENCODING_NAME_EUCJP_WIN    = 'eucJP-win';

    /**
     * @var array mb_*関数においてShift_JISと見なす文字エンコーディング
     */
    public const CONSIDER_SHIFT_JIS_ENCODING_NAMES  = [
        'SJIS',
        'SJIS-win',
        'CP932',
        'SJIS-mac',
        'MacJapanese',
        'SJIS-Mobile#DOCOMO',
        'SJIS-DOCOMO',
        'SJIS-Mobile#KDDI',
        'SJIS-KDDI',
        'SJIS-Mobile#SOFTBANK',
        'SJIS-SOFTBANK',
    ];

    /**
     * @var array デフォルトでの文字エンコーディング検出順序
     */
    public const DETECT_ORDER_DEFAULT   = [
        'eucJP-win',
        'SJIS-win',
        'JIS',
        'ISO-2022-JP',
        'UTF-8',
        'ASCII',
    ];

    /**
     * @var array 変換元文字列に対してエンコーディング検出を行う変換元エンコーディングマップ
     */
    public const DETECT_FROM_ENCODING_MAP   = [
        self::FROM_ENCODING_DEFAULT => self::FROM_ENCODING_DEFAULT,
        self::FROM_ENCODING_AUTO    => self::FROM_ENCODING_AUTO,
    ];

    // ----------------------------------------------
    // Shift_JIS特化設定
    // ----------------------------------------------
    /**
     * @var int デフォルトのShift_JIS遅延判定文字列バッファサイズ
     *          streamのデフォルトチャンクサイズ (8192) の3倍としている
     */
    public const SJIS_CHECK_DEFERRED_BUFFER_SIZE_DEFAULT    = 24576;

    // ----------------------------------------------
    // 入力文字エンコーディングが無効、または出力文字エンコーディングに文字コードが存在しない場合の代替文字
    // ----------------------------------------------
    /**
     * @var string 文字コードが無効または存在しない場合のデフォルト代替文字設定
     */
    public const SUBSTITUTE_CHARACTER_DEFAULT   = self::SUBSTITUTE_CHARACTER_ENTITY;

    /**
     * @var string 文字コードが無効または存在しない場合の代替文字設定：出力しない
     */
    public const SUBSTITUTE_CHARACTER_NONE      = 'none';

    /**
     * @var string 文字コードが無効または存在しない場合の代替文字設定：文字コードの値を出力する (例: U+3000、JIS+7E7E)
     */
    public const SUBSTITUTE_CHARACTER_LONG      = 'long';

    /**
     * @var string 文字コードが無効または存在しない場合の代替文字設定：文字エンティティを出力する (例: &#x200;)
     */
    public const SUBSTITUTE_CHARACTER_ENTITY    = 'entity';

    /**
     * @var int 文字コードが無効または存在しない場合の代替文字の有効な最大コードポイント
     */
    public const SUBSTITUTE_CHARACTER_MAX_CODE_POINT    = 65534;

    /**
     * @var array 文字コードが無効または存在しない場合の代替文字設定マップ
     */
    public const SUBSTITUTE_CHARACTER_MAP   = [
        self::SUBSTITUTE_CHARACTER_NONE     => self::SUBSTITUTE_CHARACTER_NONE,
        self::SUBSTITUTE_CHARACTER_LONG     => self::SUBSTITUTE_CHARACTER_LONG,
        self::SUBSTITUTE_CHARACTER_ENTITY   => self::SUBSTITUTE_CHARACTER_ENTITY,
    ];

    // ==============================================
    // static property
    // ==============================================
    // ロカール設定関連
    // ----------------------------------------------
    /**
     * @var array 変更中のロカールスタック
     * @static
     */
    protected static array $localeStack  = [];

    // ----------------------------------------------
    // エンコーディング変換設定
    // ----------------------------------------------
    /**
     * @var null|array 現在の実行環境で使用可能な文字エンコーディングセットキャッシュ
     * @static
     */
    protected static ?array $availableEncodingNameCache = null;

    /**
     * @var null|array 文字エンコーディング検出順
     * @static
     */
    protected static ?array $detectOrder    = null;

    /**
     * @var null|array システムデフォルトの文字エンコーディング検出順キャッシュ
     * @static
     */
    protected static ?array $mbListEncodings    = null;

    // ----------------------------------------------
    // Shift_JIS特化設定
    // ----------------------------------------------
    /**
     * @var int Shift_JIS遅延判定文字列バッファサイズ
     * @staitc
     */
    protected static int $sjisCheckDeferredBufferSize   = self::SJIS_CHECK_DEFERRED_BUFFER_SIZE_DEFAULT;

    // ----------------------------------------------
    // 入力文字エンコーディングが無効、または出力文字エンコーディングに文字コードが存在しない場合の代替文字
    // ----------------------------------------------
    /**
     * @var array 変更中の文字コードが無効または存在しない場合の代替文字スタック
     * @static
     */
    protected static array $substituteCharacterStack  = [];

    // ==============================================
    // property
    // ==============================================
    /**
     * @var string Shift_JIS遅延判定文字列バッファ
     */
    protected string $sjisCheckDeferredBuffer  = '';

    /**
     * @var null|string 変換先のエンコーディング
     */
    protected ?string $toEncoding   = null;

    /**
     * @var null|string 変換元のエンコーディング
     */
    protected ?string $fromEncoding = null;

    /**
     * @var bool 変換元のエンコーディングがShift_JISの場合はtrue
     */
    protected bool $isFromEncodingSjis       = false;

    /**
     * @var bool 変換元文字列に対してエンコーディング検出を行う場合はtrue
     */
    protected bool $isDetectFromEncoding     = false;

    // ==============================================
    // static method
    // ==============================================
    /**
     * マルチバイト処理を扱うにあたって安全なロカールを返します。
     *
     * @return string マルチバイト処理を扱うにあたって安全なロカール
     */
    public static function getSafeLocale(): string
    {
        // 同一プロセス中に変化する事はありえないため関数内キャッシュとする
        static $locale = null;

        if ($locale === null) {
            // mac環境は"ja_JP.UTF-8"を使用する
            if (\PHP_OS_FAMILY === 'Darwin') {
                return $locale = static::LOCALE_FOR_MAC_UTF_8_ALTERNATIVE;
            }

            // 残るWindows環境以外は"ja_JP.UTF-8"を使用する
            if (\PHP_OS_FAMILY !== 'Windows') {
                return $locale = static::LOCALE_FOR_DEFAULT;
            }

            // Windows環境かつPHP7.0.0"未満"の場合は"Japanese_Japan.932"を使用する
            if (\version_compare(\PHP_VERSION, '7.0.0', 'lt')) {
                return $locale = static::LOCALE_FOR_WINDOWS_DEFAULT;
            }

            // Windows環境かつPHP7.0.0"以上"の場合は"C"を使用する
            return $locale = static::LOCALE_FOR_WINDOWS_ALTERNATIVE;
        }

        return $locale;
    }

    /**
     * 作業開始前にロカールを変更します。
     *
     * @param  string $locale 強制的に適用したいロカール
     * @return string 変更前のロカール
     */
    public static function startChangeLocale(?string $locale = null): string
    {
        $before_locale          = \setlocale(\LC_ALL, static::LOCALE_FOR_GETTER);

        $locale = $locale === null ? static::getSafeLocale() : $locale;

        if (false === \setlocale(\LC_ALL, $locale)) {
            throw new \Exception(\sprintf('システムで使用できないロカールを指定されました。locale:%s', $locale));
        }

        static::$localeStack[]  = $before_locale;

        return $before_locale;
    }

    /**
     * 現在のロカールスタックを返します。
     *
     * @return array 現在のロカールスタック
     */
    public static function getLocaleStack(): array
    {
        return static::$localeStack;
    }

    /**
     * 現在のロカールを返します。
     *
     * @return string 現在のロカール
     */
    public static function currentLocale(): string
    {
        return \setlocale(\LC_ALL, static::LOCALE_FOR_GETTER);
    }

    /**
     * 作業終了後にロカールを元に戻します。
     *
     * @param  bool       $reset ロカールを初期状態に戻し、スタックを破棄します
     * @throws \Exception ロカールスタックが空の場合
     * @return string     変更前のロカール
     */
    public static function endChangeLocale(bool $reset = false): string
    {
        if (empty(static::$localeStack)) {
            throw new \Exception('ロカールスタックが空です。');
        }

        $before_locale  = \setlocale(\LC_ALL, static::LOCALE_FOR_GETTER);

        if ($reset) {
            \reset(static::$localeStack);
            $locale                 = \current(static::$localeStack);
            static::$localeStack    = [];
        } else {
            $locale = \array_pop(static::$localeStack);
        }

        \setlocale(\LC_ALL, $locale);

        return $before_locale;
    }

    /**
     * 作業開始前に文字コードが無効または存在しない場合の代替文字を変更します。
     *
     * @param  int|string $substitute_character 強制的に適用したい文字コードが無効または存在しない場合の代替文字
     * @throws \Exception 使用できない代替文字設定を与えられた場合
     * @return int|string 変更前の文字コードが無効または存在しない場合の代替文字
     */
    public static function startChangeSubstituteCharacter($substitute_character = null)
    {
        $substitute_character   = $substitute_character === null ? static::SUBSTITUTE_CHARACTER_DEFAULT : $substitute_character;

        if (\is_int($substitute_character)) {
            if (!(0 < $substitute_character && $substitute_character <= static::SUBSTITUTE_CHARACTER_MAX_CODE_POINT)) {
                throw new \Exception(\sprintf('使用できない代替文字を与えられました。substitute_character:U+%04X', $substitute_character));
            }
        } else {
            if (!isset(static::SUBSTITUTE_CHARACTER_MAP[$substitute_character])) {
                throw new \Exception(\sprintf('使用できない代替文字設定を与えられました。substitute_character:%s', $substitute_character));
            }
            $substitute_character = static::SUBSTITUTE_CHARACTER_MAP[$substitute_character];
        }

        $before_substitute_character        = \mb_substitute_character();

        if (false === \mb_substitute_character($substitute_character)) {
            throw new \Exception(\sprintf('使用できない代替文字を与えられました。substitute_character:%s', $substitute_character));
        }

        static::$substituteCharacterStack[] = $before_substitute_character;

        return $before_substitute_character;
    }

    /**
     * 現在の文字コードが無効または存在しない場合の代替文字スタックを返します。
     *
     * @return array 現在の文字コードが無効または存在しない場合の代替文字スタック
     */
    public static function getSubstituteCharacterStack(): array
    {
        return static::$substituteCharacterStack;
    }

    /**
     * 現在の文字コードが無効または存在しない場合の代替文字を返します。
     *
     * @return int|string 現在の文字コードが無効または存在しない場合の代替文字
     */
    public static function currentSubstituteCharacter()
    {
        return \mb_substitute_character();
    }

    /**
     * 作業終了後に文字コードが無効または存在しない場合の代替文字を元に戻します。
     *
     * @param  bool       $reset 文字コードが無効または存在しない場合の代替文字スタックの最上層の値を使います
     * @throws \Exception 文字コードが無効または存在しない場合の代替文字スタックが空の場合
     * @return int|string 変更前の文字コードが無効または存在しない場合の代替文字
     */
    public static function endChangeSubstituteCharacter(bool $reset = false)
    {
        if (empty(static::$substituteCharacterStack)) {
            throw new \Exception('代替文字コードスタックが空です。');
        }

        $before_substitute_character    = \mb_substitute_character();

        if ($reset) {
            \reset(static::$substituteCharacterStack);
            $substitute_character               = \current(static::$substituteCharacterStack);
            static::$substituteCharacterStack   = [];
        } else {
            $substitute_character    = \array_pop(static::$substituteCharacterStack);
        }

        \mb_substitute_character($substitute_character);

        return $before_substitute_character;
    }

    /**
     * システムデフォルトの文字エンコーディング検出順キャッシュを取得します。
     *
     * @return array システムデフォルトの文字エンコーディング検出順キャッシュ
     */
    public static function getDefaultDetectEncodingListCache(): array
    {
        if (static::$mbListEncodings === null) {
            $mb_list_encodings          = \mb_list_encodings();
            static::$mbListEncodings    = \array_combine($mb_list_encodings, $mb_list_encodings);
        }

        return static::$mbListEncodings;
    }

    /**
     * memory_limitの単位をintに変換します。
     *
     * @param  int|string $memory_limit バイト値
     * @return int        バイト値
     */
    public static function adjustMemoryLimitUnit($memory_limit): int
    {
        $memory_limit   = (string) $memory_limit;

        if ($memory_limit === '-1') {
            return -1;
        }

        $unit_size      = 1;

        switch (\strtolower(\substr($memory_limit, -1))) {
            case 'g':
                $unit_size *= 1024;
                // no break
            case 'm':
                $unit_size *= 1024;
                // no break
            case 'k':
                $unit_size *= 1024;

                return (int) \substr($memory_limit, 0, -1) * $unit_size;
        }

        return (int) $memory_limit;
    }

    /**
     * Shift_JIS遅延判定文字列バッファサイズを変更・取得します。
     *
     * @param  null|int $sjis_check_deferred_buffer_size Shift_JIS遅延判定文字列バッファサイズ
     * @return int      変更前のShift_JIS遅延判定文字列バッファサイズまたは現在のShift_JIS遅延判定文字列バッファサイズ
     */
    public static function sjisSeparationPositionBufferSize(?int $sjis_check_deferred_buffer_size = null): int
    {
        if (\func_num_args() === 0) {
            return static::$sjisCheckDeferredBufferSize;
        }

        $sjis_check_deferred_buffer_size    = static::adjustMemoryLimitUnit($sjis_check_deferred_buffer_size);
        $memory_limit                       = static::adjustMemoryLimitUnit(\ini_get('memory_limit'));

        if ($memory_limit !== -1 && $sjis_check_deferred_buffer_size >= $memory_limit) {
            throw new \Exception(\sprintf('現在の設定で利用できるメモリ量を超過しています。%s / %s', $sjis_check_deferred_buffer_size, $memory_limit));
        }

        $before_sjis_check_deferred_buffer_size = static::$sjisCheckDeferredBufferSize;
        static::$sjisCheckDeferredBufferSize    = $sjis_check_deferred_buffer_size;

        return $before_sjis_check_deferred_buffer_size;
    }

    /**
     * デフォルト時の変換元エンコーディングの自動検出順を変更・取得します。
     *
     * ！！注意！！
     * PHP8.1での誤った修正により`SJIS-win`は削除されました。
     * 過去実装でも極力そのまま動作させるために、内部的にはCP932を設定したものとみなし、処理を続行させます。
     *
     * @param  array $detect_order デフォルト時の変換元エンコーディングの自動検出順
     * @return array 変更前のデフォルト時の変換元エンコーディングの自動検出順
     */
    public static function detectOrder(?array $detect_order = null): array
    {
        if (static::$detectOrder === null) {
            static::$detectOrder    = static::DETECT_ORDER_DEFAULT;
        }

        if (\func_num_args() === 0) {
            return static::$detectOrder;
        }

        $default_detect_encoding_list   = static::getDefaultDetectEncodingListCache();

        if (\version_compare(\PHP_VERSION, '8.1')) {
            foreach ((array) $detect_order as $detect_encoding) {
                if ($detect_encoding === static::ENCODING_NAME_SJIS_WIN) {
                    $detect_encoding    = static::ENCODING_NAME_CP932;
                }

                if (!isset($default_detect_encoding_list[$detect_encoding])) {
                    throw new \Exception(\sprintf('システムで使用できないエンコーディングを指定されました。encoding:%s', $detect_encoding));
                }
            }
        } else {
            foreach ((array) $detect_order as $detect_encoding) {
                if (!isset($default_detect_encoding_list[$detect_encoding])) {
                    throw new \Exception(\sprintf('システムで使用できないエンコーディングを指定されました。encoding:%s', $detect_encoding));
                }
            }
        }

        $before_detect_order    = static::$detectOrder;
        static::$detectOrder    = $detect_order;

        return $before_detect_order;
    }

    // ==============================================
    // method
    // ==============================================
    /**
     * インスタンス生成時の処理
     *
     * ！！注意！！
     * PHP8.1での誤った修正により`SJIS-win`は削除されました。
     * 過去実装でも極力そのまま動作させるために、内部的にはCP932を設定したものとみなし、処理を続行させます。
     *
     * @return bool instance生成に成功した場合はtrue、そうでなければfalse (falseを返した場合、フィルタの登録が失敗したものと見なされる)
     * @see \php_user_filter::onCreate()
     */
    public function onCreate(): bool
    {
        // ==============================================
        // フィルタ名フォーマット確認
        // ==============================================
        if (false === $option_separate_position = \strrpos($this->filtername, '.')) {
            throw new \Exception(\sprintf('フィルタ名の指定の中にオプション区切り文字(.)がありません。filtername:%s', $this->filtername));
        }

        // ==============================================
        // 現在の実行環境で使用可能な文字エンコーディングセットキャッシュの作成
        // ==============================================
        if (null === static::$availableEncodingNameCache) {
            $available_to_encodings     = static::getDefaultDetectEncodingListCache();

            if (isset($available_to_encodings['auto'])) {
                unset($available_to_encodings['auto']);
            }

            $available_from_encodings                                   = static::getDefaultDetectEncodingListCache();
            $available_from_encodings[static::FROM_ENCODING_AUTO]       = static::FROM_ENCODING_AUTO;
            $available_from_encodings[static::FROM_ENCODING_DEFAULT]    = static::FROM_ENCODING_DEFAULT;

            static::$availableEncodingNameCache = [
                'to'    => $available_to_encodings,
                'from'  => $available_from_encodings,
            ];
        }

        // ==============================================
        // フィルタオプションの確定
        // ==============================================
        $filter_option_part = \substr($this->filtername, $option_separate_position + 1);

        if (false === $parameter_separate_position = \strpos($filter_option_part, ':')) {
            // to encodingがない場合
            $to_encoding    = $filter_option_part;
            $from_encoding  = static::FROM_ENCODING_DEFAULT;
        } else {
            // to encoding, from encodingが共にある場合
            $to_encoding    = \substr($filter_option_part, 0, $parameter_separate_position);
            $from_encoding  = \substr($filter_option_part, $parameter_separate_position + 1);
        }

        if (\version_compare(\PHP_VERSION, '8.1')) {
            if ($to_encoding === static::ENCODING_NAME_SJIS_WIN) {
                $to_encoding    = static::ENCODING_NAME_CP932;
            }

            if ($from_encoding === static::ENCODING_NAME_SJIS_WIN) {
                $from_encoding  = static::ENCODING_NAME_CP932;
            }
        }

        // ----------------------------------------------
        // 使用可能なエンコーディングかどうか検証
        // ----------------------------------------------
        if (!isset(static::$availableEncodingNameCache['to'][$to_encoding])) {
            throw new \Exception(\sprintf('変換先のエンコーディング名が無効です。to_encoding:%s', $to_encoding));
        }

        if (!isset(static::$availableEncodingNameCache['from'][$from_encoding])) {
            throw new \Exception(\sprintf('変換元のエンコーディング名が無効です。from_encoding:%s', $from_encoding));
        }

        if (static::$availableEncodingNameCache['to'][$to_encoding] === static::$availableEncodingNameCache['from'][$from_encoding]) {
            throw new \Exception(\sprintf('変換前後のエンコーディング名が同じです。to_encoding:%s, from_encoding:%s', static::$availableEncodingNameCache['to'][$to_encoding], static::$availableEncodingNameCache['from'][$from_encoding]));
        }

        // ==============================================
        // プロパティ初期化
        // ==============================================
        $this->toEncoding   = static::$availableEncodingNameCache['to'][$to_encoding];
        $this->fromEncoding = static::$availableEncodingNameCache['from'][$from_encoding];

        $this->isFromEncodingSjis       = \in_array($this->fromEncoding, static::CONSIDER_SHIFT_JIS_ENCODING_NAMES, true);
        $this->isDetectFromEncoding     = isset(static::DETECT_FROM_ENCODING_MAP[$this->fromEncoding]);

        if ($this->isDetectFromEncoding) {
            static::$detectOrder  = $this->fromEncoding === static::FROM_ENCODING_DEFAULT ? (empty(static::$detectOrder) ? static::DETECT_ORDER_DEFAULT : static::$detectOrder) : \mb_detect_order();
        }

        // ==============================================
        // 処理の終了
        // ==============================================
        return true;
    }

    /**
     * フィルタ
     *
     * @param  resource $in       元のバケットオブジェクト
     * @param  resource $out      変更内容を適用するためのバケットオブジェクト
     * @param           $consumed 変更したデータ長
     * @param  bool     $closing  フィルタチェインの最後の処理かどうか
     * @return int      処理を終えたときの状態
     *                  \PSFS_PASS_ON                 ：フィルタの処理が成功し、データがoutバケット群に保存された
     *                  \PSFS_FEED_ME                 ：フィルタの処理は成功したが、返すデータがない。ストリームあるいは一つ前のフィルタから、追加のデータが必要
     *                  \PSFS_ERR_FATAL (デフォルト)  ：フィルタで対処不能なエラーが発生し、処理を続行できない
     * @see \php_user_filter::filter()
     * @memo 引数$consumedの型はPHP Versionによって定義があるとエラーになるケースがあるため、doc comment上でも定義しない。
     *       これはPHP CS Fixerのphpdoc_to_param_typeルールでメソッド定義に自動適用される事を防ぐためでもある。
     */
    public function filter($in, $out, &$consumed, bool $closing): int
    {
        // ==============================================
        // 初期化
        // ==============================================
        // デフォルトは追加データ取得
        $status = \PSFS_FEED_ME;

        $from_encoding          = $this->fromEncoding;
        $is_from_encoding_sjis  = $this->isFromEncodingSjis;

        $sjis_check_deferred_buffer = '';

        // ==============================================
        // 主処理
        // ==============================================
        for (;$bucket = \stream_bucket_make_writeable($in);) {
            $data = $bucket->data;

            // ----------------------------------------------
            // 変換元文字列に対してエンコーディング検出が有効な場合はエンコーディング検出を行う
            // ----------------------------------------------
            if ($this->isDetectFromEncoding) {
                $from_encoding = \mb_detect_encoding($data, static::$detectOrder, true);

                if ($from_encoding === false) {
                    throw new \Exception(\sprintf('文字エンコーディング検出に失敗しました。対象:%s', $data));
                }

                $is_from_encoding_sjis  = \in_array($from_encoding, static::CONSIDER_SHIFT_JIS_ENCODING_NAMES, true);
            }

            // ----------------------------------------------
            // 対象文字列の抽出
            // ----------------------------------------------
            if ($is_from_encoding_sjis) {
                // 変換元文字列がShift_JIS系の場合は妥当な判定を得られるまで文字列を減らす
                for (
                    $data = $this->sjisCheckDeferredBuffer . $data;
                    $data !== '' && !\mb_check_encoding($data, $from_encoding);
                    $sjis_check_deferred_buffer = \substr($data, -1) . $sjis_check_deferred_buffer, $data = \substr($data, 0, -1)
                );
            }

            // ----------------------------------------------
            // エンコーディング変換
            // ----------------------------------------------
            if ($data !== '') {
                if ($this->toEncoding !== $from_encoding) {
                    $bucket->data   = \mb_convert_encoding($data, $this->toEncoding, $from_encoding);
                }

                $bucket->datalen    = \strlen($bucket->data);
                $consumed           += $bucket->datalen;
                \stream_bucket_append($out, $bucket);

                $status     = \PSFS_PASS_ON;
            }

            // ----------------------------------------------
            // 変換元文字列に対してエンコーディング検出が有効な場合はエンコーディング情報を元に戻す
            // ----------------------------------------------
            if ($this->isDetectFromEncoding) {
                $from_encoding          = $this->fromEncoding;
                $is_from_encoding_sjis  = false;
            }
        }

        // ==============================================
        // Shift_JIS遅延判定文字列バッファのサイズ検証
        // ==============================================
        if ($is_from_encoding_sjis) {
            if (static::$sjisCheckDeferredBufferSize < $sjis_check_deferred_buffer_size = \strlen($sjis_check_deferred_buffer)) {
                throw new \Exception(\sprintf('設定されたShift_JIS遅延判定文字列バッファサイズを超過しました。%s / %s', $sjis_check_deferred_buffer_size, static::$sjisCheckDeferredBufferSize));
            }

            $this->sjisCheckDeferredBuffer  = $sjis_check_deferred_buffer;
        }

        // ==============================================
        // 処理の終了
        // ==============================================
        return $status;
    }
}
