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

namespace fw3\streams\filters\utilitys;

use fw3\streams\filters\ConvertEncodingFilter;
use fw3\streams\filters\ConvertLinefeedFilter;
use fw3\streams\filters\utilitys\entitys\StreamFilterSpecEntity;
use fw3\streams\filters\utilitys\specs\StreamFilterConvertEncodingSpec;
use fw3\streams\filters\utilitys\specs\StreamFilterConvertLinefeedSpec;

/**
 * ストリームフィルタ設定を扱うクラスです。
 */
abstract class StreamFilterSpec
{
    // ==============================================
    // const
    // ==============================================
    // フィルタパラメータ
    // ----------------------------------------------
    /**
     * @var string フィルタパラメータ間のセパレータ
     */
    public const PARAMETER_SEPARATOR        = StreamFilterSpecEntity::PARAMETER_SEPARATOR;

    /**
     * @var string パラメータチェーン間のセパレータ
     */
    public const PARAMETER_CHAIN_SEPARATOR  = StreamFilterSpecEntity::PARAMETER_CHAIN_SEPARATOR;

    /**
     * @var string パラメータオプション間のセパレータ
     */
    public const PARAMETER_OPTION_SEPARATOR = StreamFilterSpecEntity::PARAMETER_OPTION_SEPARATOR;

    // ----------------------------------------------
    // resource
    // ----------------------------------------------
    /**
     * @var string リソース名：stdin
     */
    public const RESOURCE_PHP_STDIN     = StreamFilterSpecEntity::RESOURCE_PHP_STDIN;

    /**
     * @var string リソース名：strout
     */
    public const RESOURCE_PHP_STDOUT    = StreamFilterSpecEntity::RESOURCE_PHP_STDOUT;

    /**
     * @var string リソース名：strerr
     */
    public const RESOURCE_PHP_STDERR    = StreamFilterSpecEntity::RESOURCE_PHP_STDERR;

    /**
     * @var string リソース名：input
     */
    public const RESOURCE_PHP_INPUT     = StreamFilterSpecEntity::RESOURCE_PHP_INPUT;

    /**
     * @var string リソース名：output
     */
    public const RESOURCE_PHP_OUTPUT    = StreamFilterSpecEntity::RESOURCE_PHP_OUTPUT;

    /**
     * @var string リソース名：fd
     */
    public const RESOURCE_PHP_FD        = StreamFilterSpecEntity::RESOURCE_PHP_FD;

    /**
     * @var string リソース名：memory
     */
    public const RESOURCE_PHP_MEMORY    = StreamFilterSpecEntity::RESOURCE_PHP_MEMORY;

    /**
     * @var string リソース名：temp
     */
    public const RESOURCE_PHP_TEMP      = StreamFilterSpecEntity::RESOURCE_PHP_TEMP;

    // ==============================================
    // static method
    // ==============================================
    /**
     * ストリームフィルタスペックエンティティを返します。
     *
     * @param  array                  $spec スペック
     *                                      [
     *                                      'resource'  => フィルタの対象となるストリーム
     *                                      'write'     => 書き込みチェーンに適用するフィルタのリスト
     *                                      'read'      => 読み込みチェーンに適用するフィルタのリスト
     *                                      'both'      => 書き込みチェーン、読み込みチェーン双方に適用するフィルタのリスト
     *                                      ]
     * @return StreamFilterSpecEntity ストリームフィルタスペックエンティティ
     */
    public static function factory(?array $spec = []): StreamFilterSpecEntity
    {
        return StreamFilterSpecEntity::factory($spec);
    }

    /**
     * エンコーディング変換ストリームフィルタセットアッパー
     *
     * @param string $filter_name 登録するフィルタ名
     */
    public static function registerConvertEncodingFilter(string $filter_name = StreamFilterConvertEncodingSpec::DEFAULT_FILTER_NAME): void
    {
        StreamFilterConvertEncodingSpec::filterName($filter_name);
        \stream_filter_register(StreamFilterConvertEncodingSpec::registerFilterName(), ConvertEncodingFilter::class);
    }

    /**
     * 改行コード変換ストリームフィルタセットアッパー
     *
     * @param string $filter_name 登録するフィルタ名
     */
    public static function registerConvertLinefeedFilter(string $filter_name = StreamFilterConvertLinefeedSpec::DEFAULT_FILTER_NAME): void
    {
        StreamFilterConvertLinefeedSpec::filterName($filter_name);
        \stream_filter_register(StreamFilterConvertLinefeedSpec::registerFilterName(), ConvertLinefeedFilter::class);
    }

    /**
     * 指定された名前のストリームフィルタが登録されているか返します。
     *
     * @param  string $filter_name ストリームフィルタ名 登録時のストリームフィルタ名に`.*`がある場合、`.*`まで含めて指定する必要があります
     * @return bool   ストリームフィルタが登録されている場合はtrue、そうでない場合はfalse
     */
    public static function registeredStreamFilterName(string $filter_name): bool
    {
        return \in_array($filter_name, \stream_get_filters(), true);
    }

    /**
     * フィルタの対象となるストリームを設定したストリームフィルタスペックエンティティを返します。
     *
     * @param  null|string|\SplFileInfo|ResourceSpecInterface $resource フィルタの対象となるリソースタイプ
     * @return StreamFilterSpecEntity                         フィルタの対象となるストリームを設定したストリームフィルタスペックエンティティ
     */
    public static function resource($resource): StreamFilterSpecEntity
    {
        return static::factory()->resource($resource);
    }

    /**
     * フィルタの対象となるファイルを設定したストリームフィルタスペックエンティティを返します。
     *
     * @param  string|\SplFileInfo    $file ファイル
     * @return StreamFilterSpecEntity フィルタの対象となるファイルを設定したストリームフィルタスペックエンティティ
     */
    public static function resourceFile($file): StreamFilterSpecEntity
    {
        return static::factory()->resourceFile($file);
    }

    /**
     * フィルタの対象となるzip://stdinストリームを設定したストリームフィルタスペックエンティティを返します。
     *
     * @param  string|\SplFileInfo    $zip_file        ZIPファイル
     * @param  string                 $path_in_archive ZIPファイル内ファイルパス
     * @return StreamFilterSpecEntity フィルタの対象となるzip://ストリームを設定したストリームフィルタスペックエンティティ
     */
    public static function resourceZip($zip_file, string $path_in_archive): StreamFilterSpecEntity
    {
        return static::factory()->resourceZip($zip_file, $path_in_archive);
    }

    /**
     * フィルタの対象となるphp://stdinストリームを設定したストリームフィルタスペックエンティティを返します。
     *
     * @return StreamFilterSpecEntity フィルタの対象となるphp://stdinストリームを設定したストリームフィルタスペックエンティティ
     */
    public static function resourceStdin(): StreamFilterSpecEntity
    {
        return static::factory()->resourceStdin();
    }

    /**
     * フィルタの対象となるphp://stdoutストリームを設定したストリームフィルタスペックエンティティを返します。
     *
     * @return StreamFilterSpecEntity フィルタの対象となるphp://stdoutストリームを設定したストリームフィルタスペックエンティティ
     */
    public static function resourceStdout(): StreamFilterSpecEntity
    {
        return static::factory()->resourceStdout();
    }

    /**
     * フィルタの対象となるphp://inputストリームを設定したストリームフィルタスペックエンティティを返します。
     *
     * @return StreamFilterSpecEntity フィルタの対象となるphp://inputストリームを設定したストリームフィルタスペックエンティティ
     */
    public static function resourceInput(): StreamFilterSpecEntity
    {
        return static::factory()->resourceInput();
    }

    /**
     * フィルタの対象となるphp://outputストリームを設定したストリームフィルタスペックエンティティを返します。
     *
     * @return StreamFilterSpecEntity フィルタの対象となるphp://outputストリームを設定したストリームフィルタスペックエンティティ
     */
    public static function resourceOutput(): StreamFilterSpecEntity
    {
        return static::factory()->resourceOutput();
    }

    /**
     * フィルタの対象となるphp://fdストリームを設定したストリームフィルタスペックエンティティを返します。
     *
     * @return StreamFilterSpecEntity フィルタの対象となるphp://fdストリームを設定したストリームフィルタスペックエンティティ
     */
    public static function resourceFd(): StreamFilterSpecEntity
    {
        return static::factory()->resourceFd();
    }

    /**
     * フィルタの対象となるphp://memoryストリームを設定したストリームフィルタスペックエンティティを返します。
     *
     * @return StreamFilterSpecEntity フィルタの対象となるphp://memoryストリームを設定したストリームフィルタスペックエンティティ
     */
    public static function resourceMemory(): StreamFilterSpecEntity
    {
        return static::factory()->resourceMemory();
    }

    /**
     * フィルタの対象となるphp://tempストリームを設定したストリームフィルタスペックエンティティを返します。
     *
     * @return StreamFilterSpecEntity フィルタの対象となるphp://tempストリームを設定したストリームフィルタスペックエンティティ
     */
    public static function resourceTemp(): StreamFilterSpecEntity
    {
        return static::factory()->resourceTemp();
    }

    /**
     * フィルタの対象となるストリーム文字列を設定したストリームフィルタスペックエンティティを返します。
     *
     * @return StreamFilterSpecEntity フィルタの対象となるストリーム文字列を設定したストリームフィルタスペックエンティティ
     */
    public static function resourceRaw($resource): StreamFilterSpecEntity
    {
        return static::factory()->resourceRaw($resource);
    }

    /**
     * 書き込みチェーンに適用するフィルタのリストを設定したストリームフィルタスペックエンティティを返します。
     *
     * @param  array                  $write 書き込みチェーンに適用するフィルタのリスト
     * @return StreamFilterSpecEntity 書き込みチェーンに適用するフィルタのリストを設定したストリームフィルタスペックエンティティ
     */
    public static function write(array $write): StreamFilterSpecEntity
    {
        return static::factory()->write($write);
    }

    /**
     * 読み込みチェーンに適用するフィルタのリストを設定したストリームフィルタスペックエンティティを返します。
     *
     * @param  array                  $read 読み込みチェーンに適用するフィルタのリスト
     * @return StreamFilterSpecEntity 読み込みチェーンに適用するフィルタのリストを設定したストリームフィルタスペックエンティティ
     */
    public static function read(array $read): StreamFilterSpecEntity
    {
        return static::factory()->read($read);
    }

    /**
     * 書き込みチェーン、読み込みチェーン双方に適用するフィルタのリストを設定したストリームフィルタスペックエンティティを返します。
     *
     * @param  array                  $both 書き込みチェーン、読み込みチェーン双方に適用するフィルタのリスト
     * @return StreamFilterSpecEntity 書き込みチェーン、読み込みチェーン双方に適用するフィルタのリストを設定したストリームフィルタスペックエンティティ
     */
    public static function both(array $both): StreamFilterSpecEntity
    {
        return static::factory()->both($both);
    }

    /**
     * 書き込みチェーンに適用するフィルタを追加したストリームフィルタスペックエンティティを返します。
     *
     * @param  StreamFilterSpecInterface|string $filter                     書き込みストリームフィルタ名
     * @param  array                            $filter_parameters          ストリームフィルタに追加するパラメータ
     * @param  string                           $filter_parameter_separator ストリームフィルタに追加するパラメータオプションのセパレータ
     * @return StreamFilterSpecEntity           書き込みチェーンに適用するフィルタを追加したストリームフィルタスペックエンティティ
     */
    public static function appendWriteChain($filter, array $filter_parameters = [], string $filter_parameter_separator = self::PARAMETER_OPTION_SEPARATOR): StreamFilterSpecEntity
    {
        return static::factory()->appendWriteChain($filter, $filter_parameters, $filter_parameter_separator);
    }

    /**
     * 読み込みチェーンに適用するフィルタを追加したストリームフィルタスペックエンティティを返します。
     *
     * @param  StreamFilterSpecInterface|string $filter                     読み込みストリームフィルタ名
     * @param  array                            $filter_parameters          ストリームフィルタに追加するパラメータ
     * @param  string                           $filter_parameter_separator ストリームフィルタに追加するパラメータオプションのセパレータ
     * @return StreamFilterSpecEntity           読み込みチェーンに適用するフィルタを追加したストリームフィルタスペックエンティティ
     */
    public static function appendReadChain($filter, array $filter_parameters = [], string $filter_parameter_separator = self::PARAMETER_OPTION_SEPARATOR): StreamFilterSpecEntity
    {
        return static::factory()->appendReadChain($filter, $filter_parameters, $filter_parameter_separator);
    }

    /**
     * 書き込みチェーン、読み込みチェーン双方に適用するフィルタを追加したストリームフィルタスペックエンティティを返します。
     *
     * @param  StreamFilterSpecInterface|string $filter                     書き込みチェーン、読み込みチェーン双方に適用するフィルタ名
     * @param  array                            $filter_parameters          ストリームフィルタに追加するパラメータ
     * @param  string                           $filter_parameter_separator ストリームフィルタに追加するパラメータオプションのセパレータ
     * @return StreamFilterSpecEntity           書き込みチェーン、読み込みチェーン双方に適用するフィルタを追加したストリームフィルタスペックエンティティ
     */
    public static function appendBothChain($filter, array $filter_parameters = [], string $filter_parameter_separator = self::PARAMETER_OPTION_SEPARATOR): StreamFilterSpecEntity
    {
        return static::factory()->appendBothChain($filter, $filter_parameters, $filter_parameter_separator);
    }

    // ----------------------------------------------
    // decorators
    // ----------------------------------------------
    /**
     * CSV入出力を行うにあたって必要な事前・事後処理を行い、$callbackで指定された処理を行います。
     *
     * ！！注意！！
     * このメソッドは実行時にconvert encoding filterやconvert lien_feed filterの登録が行われていなかった場合に、フィルタの登録を行います。
     * フィルタ名をデフォルトから変更したい場合、このメソッドを呼び出す前に、次のメソッドでフィルタ名を変更してください。
     * - StreamFilterConvertEncodingSpec::filterName()
     * - StreamFilterConvertLinefeedSpec::filterName()
     *
     * @param  callable    $callback             実際の処理
     * @param  null|string $locale               強制的に適用したいロカール
     * @param  null|array  $detect_order         エンコーディング検出順
     * @param  null|string $substitute_character 文字コードが無効または存在しない場合の代替文字
     * @return mixed       $callbackの返り値
     */
    public static function decorateForCsv(callable $callback, ?string $substitute_character = null, ?array $detect_order = null, ?string $locale = null): mixed
    {
        // ロカールと代替文字設定を設定
        ConvertEncodingFilter::startChangeLocale($locale);
        ConvertEncodingFilter::startChangeSubstituteCharacter($substitute_character);

        // フィルタ登録がない場合は登録
        if (!StreamFilterConvertEncodingSpec::registeredFilterName()) {
            StreamFilterSpec::registerConvertEncodingFilter();
        }

        if (!StreamFilterConvertLinefeedSpec::registeredFilterName()) {
            StreamFilterSpec::registerConvertLinefeedFilter();
        }

        $start_detect_order = ConvertEncodingFilter::detectOrder();
        ConvertEncodingFilter::detectOrder(null === $detect_order || empty($detect_order) ? ConvertEncodingFilter::DETECT_ORDER_DEFAULT : $detect_order);

        // 実行
        try {
            $result = $callback();
        } catch (\Exception $e) {
            throw $e;
        } finally {
            // ロカールと代替文字設定を元に戻します
            ConvertEncodingFilter::endChangeSubstituteCharacter();
            ConvertEncodingFilter::endChangeLocale();
            ConvertEncodingFilter::detectOrder($start_detect_order);
        }

        // 処理の終了
        return $result;
    }
}
