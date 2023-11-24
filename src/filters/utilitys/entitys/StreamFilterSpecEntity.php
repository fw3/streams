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

namespace fw3\streams\filters\utilitys\entitys;

use fw3\streams\filters\utilitys\specs\entitys\resources\FileResourceSpec;
use fw3\streams\filters\utilitys\specs\entitys\resources\PhpFdResourceSpec;
use fw3\streams\filters\utilitys\specs\entitys\resources\PhpInputResourceSpec;
use fw3\streams\filters\utilitys\specs\entitys\resources\PhpMemoryResourceSpec;
use fw3\streams\filters\utilitys\specs\entitys\resources\PhpOutputResourceSpec;
use fw3\streams\filters\utilitys\specs\entitys\resources\PhpStderrResourceSpec;
use fw3\streams\filters\utilitys\specs\entitys\resources\PhpStdinResourceSpec;
use fw3\streams\filters\utilitys\specs\entitys\resources\PhpStdoutResourceSpec;
use fw3\streams\filters\utilitys\specs\entitys\resources\PhpTempResourceSpec;
use fw3\streams\filters\utilitys\specs\entitys\resources\RawResourceSpec;
use fw3\streams\filters\utilitys\specs\entitys\resources\traits\ResourceSpecInterface;
use fw3\streams\filters\utilitys\specs\entitys\resources\ZipResourceSpec;
use fw3\streams\filters\utilitys\specs\interfaces\StreamFilterSpecInterface;
use fw3\streams\filters\utilitys\StreamFilterSpec;

/**
 * ストリームフィルタ設定を扱うクラスです。
 */
class StreamFilterSpecEntity
{
    // ==============================================
    // const
    // ==============================================
    // フィルタパラメータ
    // ----------------------------------------------
    /**
     * @var string フィルタパラメータ間のセパレータ
     */
    public const PARAMETER_SEPARATOR        = '/';

    /**
     * @var string パラメータチェーン間のセパレータ
     */
    public const PARAMETER_CHAIN_SEPARATOR  = '|';

    /**
     * @var string パラメータオプション間のセパレータ
     */
    public const PARAMETER_OPTION_SEPARATOR = '/';

    // ----------------------------------------------
    // resource
    // ----------------------------------------------
    /**
     * @var string リソース名：file
     */
    public const RESOURCE_FILE          = FileResourceSpec::RESOURCE_TYPE;

    /**
     * @var string リソース名：stdin
     */
    public const RESOURCE_PHP_STDIN     = PhpStdinResourceSpec::RESOURCE_TYPE;

    /**
     * @var string リソース名：stdout
     */
    public const RESOURCE_PHP_STDOUT    = PhpStdoutResourceSpec::RESOURCE_TYPE;

    /**
     * @var string リソース名：stderr
     */
    public const RESOURCE_PHP_STDERR    = PhpStderrResourceSpec::RESOURCE_TYPE;

    /**
     * @var string リソース名：input
     */
    public const RESOURCE_PHP_INPUT     = PhpInputResourceSpec::RESOURCE_TYPE;

    /**
     * @var string リソース名：output
     */
    public const RESOURCE_PHP_OUTPUT    = PhpOutputResourceSpec::RESOURCE_TYPE;

    /**
     * @var string リソース名：fd
     */
    public const RESOURCE_PHP_FD        = PhpFdResourceSpec::RESOURCE_TYPE;

    /**
     * @var string リソース名：memory
     */
    public const RESOURCE_PHP_MEMORY    = PhpMemoryResourceSpec::RESOURCE_TYPE;

    /**
     * @var string リソース名：temp
     */
    public const RESOURCE_PHP_TEMP      = PhpTempResourceSpec::RESOURCE_TYPE;

    /**
     * @var string リソース名：生文字列
     */
    public const RESOURCE_RAW           = RawResourceSpec::RESOURCE_TYPE;

    /**
     * @var string リソース名：zip
     */
    public const RESOURCE_ZIP           = ZipResourceSpec::RESOURCE_TYPE;

    /**
     * @var リソーススペッククラスリスト
     */
    public const RESOURCE_SPEC_CLASS_LIST   = [
        self::RESOURCE_FILE         => FileResourceSpec::class,
        self::RESOURCE_PHP_STDIN    => PhpStdinResourceSpec::class,
        self::RESOURCE_PHP_STDOUT   => PhpStdoutResourceSpec::class,
        self::RESOURCE_PHP_STDERR   => PhpStderrResourceSpec::class,
        self::RESOURCE_PHP_INPUT    => PhpInputResourceSpec::class,
        self::RESOURCE_PHP_OUTPUT   => PhpOutputResourceSpec::class,
        self::RESOURCE_PHP_FD       => PhpFdResourceSpec::class,
        self::RESOURCE_PHP_MEMORY   => PhpMemoryResourceSpec::class,
        self::RESOURCE_PHP_TEMP     => PhpTempResourceSpec::class,
        self::RESOURCE_RAW          => RawResourceSpec::class,
        self::RESOURCE_ZIP          => ZipResourceSpec::class,
    ];

    // ==============================================
    // property
    // ==============================================
    // フィルタパラメータ
    // ----------------------------------------------
    /**
     * @var null|ResourceSpecInterface フィルタの対象となるリソース
     */
    protected ?ResourceSpecInterface $resource = null;

    /**
     * @var array 書き込みチェーンに適用するフィルタのリスト
     */
    protected array $write    = [];

    /**
     * @var array 読み込みチェーンに適用するフィルタのリスト
     */
    protected array $read     = [];

    /**
     * @var array 書き込みチェーン、読み込みチェーン双方に適用するフィルタのリスト
     */
    protected array $both     = [];

    // ==============================================
    // static method
    // ==============================================
    /**
     * ストリームフィルタスペックインスタンスを返します。
     *
     * @param  array            $spec スペック
     *                                [
     *                                'resource'  => フィルタの対象となるストリーム
     *                                'write'     => 書き込みチェーンに適用するフィルタのリスト
     *                                'read'      => 読み込みチェーンに適用するフィルタのリスト
     *                                'both'      => 書き込みチェーン、読み込みチェーン双方に適用するフィルタのリスト
     *                                ]
     * @return StreamFilterSpec このインスタンス
     */
    public static function factory(array $spec = []): StreamFilterSpecEntity
    {
        $instance   = new static();

        if (!empty($spec)) {
            if (isset($spec['resource']) || \array_key_exists('resource', $spec)) {
                if (\is_array($spec['resource'])) {
                    \call_user_func_array([$instance, 'resource'], $spec['resource']);
                } else {
                    $instance->resource($spec['resource']);
                }
            }

            if (isset($spec['write']) || \array_key_exists('write', $spec)) {
                $instance->write($spec['write']);
            }

            if (isset($spec['read']) || \array_key_exists('read', $spec)) {
                $instance->read($spec['read']);
            }

            if (isset($spec['both']) || \array_key_exists('both', $spec)) {
                $instance->both($spec['both']);
            }
        }

        return $instance;
    }

    // ==============================================
    // method
    // ==============================================
    /**
     * constructor
     */
    protected function __construct()
    {
    }

    /**
     * このインスタンスを複製し返します。
     *
     * @return StreamFilterSpecEntity 複製されたこのインスタンス
     */
    public function with(): StreamFilterSpecEntity
    {
        return clone $this;
    }

    /**
     * フィルタの対象となるリソースを取得・設定します。
     *
     * @param  null|string|\SplFileInfo|ResourceSpecInterface $resource フィルタの対象となるリソースタイプ
     * @param  mixed                                          ...$value オプション引数
     * @return static|StreamFilterSpec                        フィルタの対象となるリソースまたはこのインスタンス
     */
    public function resource($resource = null, ...$value)
    {
        if ($resource === null && \func_num_args() === 0) {
            return $this->resource;
        }

        if ($resource instanceof ResourceSpecInterface) {
            $this->resource = $resource;

            return $this;
        }

        if (isset(static::RESOURCE_SPEC_CLASS_LIST[$resource])) {
            $this->resource = static::RESOURCE_SPEC_CLASS_LIST[$resource]::factory(...$value);

            return $this;
        }

        if (!(
            \str_starts_with($resource, 'http://')
            || \str_starts_with($resource, 'https://')
            || \str_starts_with($resource, 'ftp://')
            || \str_starts_with($resource, 'ftps://')
        ) && \str_contains($resource, '://')) {
            $this->resource = RawResourceSpec::factory($resource);

            return $this;
        }

        $this->resource = FileResourceSpec::factory($resource);

        return $this;
    }

    /**
     * フィルタの対象となるファイルを設定したストリームフィルタスペックエンティティを返します。
     *
     * @param  string|\SplFileInfo    $file ファイル
     * @return StreamFilterSpecEntity フィルタの対象となるファイルを設定したストリームフィルタスペックエンティティ
     */
    public function resourceFile($file): StreamFilterSpecEntity
    {
        return $this->resource(FileResourceSpec::factory($file));
    }

    /**
     * フィルタの対象となるzip://stdinストリームを設定したストリームフィルタスペックエンティティを返します。
     *
     * @param  string|\SplFileInfo    $zip_file        ZIPファイル
     * @param  string                 $path_in_archive ZIPファイル内ファイルパス
     * @return StreamFilterSpecEntity フィルタの対象となるzip://ストリームを設定したストリームフィルタスペックエンティティ
     */
    public function resourceZip($zip_file, string $path_in_archive): StreamFilterSpecEntity
    {
        return $this->resource(ZipResourceSpec::factory($zip_file, $path_in_archive));
    }

    /**
     * フィルタの対象となるphp://stdinストリームを設定したストリームフィルタスペックエンティティを返します。
     *
     * @return StreamFilterSpecEntity フィルタの対象となるphp://stdinストリームを設定したストリームフィルタスペックエンティティ
     */
    public function resourceStdin(): StreamFilterSpecEntity
    {
        return $this->resource(PhpStdinResourceSpec::factory());
    }

    /**
     * フィルタの対象となるphp://stdoutストリームを設定したストリームフィルタスペックエンティティを返します。
     *
     * @return StreamFilterSpecEntity フィルタの対象となるphp://stdoutストリームを設定したストリームフィルタスペックエンティティ
     */
    public function resourceStdout(): StreamFilterSpecEntity
    {
        return $this->resource(PhpStdoutResourceSpec::factory());
    }

    /**
     * フィルタの対象となるphp://inputストリームを設定したストリームフィルタスペックエンティティを返します。
     *
     * @return StreamFilterSpecEntity フィルタの対象となるphp://inputストリームを設定したストリームフィルタスペックエンティティ
     */
    public function resourceInput(): StreamFilterSpecEntity
    {
        return $this->resource(PhpInputResourceSpec::factory());
    }

    /**
     * フィルタの対象となるphp://outputストリームを設定したストリームフィルタスペックエンティティを返します。
     *
     * @return StreamFilterSpecEntity フィルタの対象となるphp://outputストリームを設定したストリームフィルタスペックエンティティ
     */
    public function resourceOutput(): StreamFilterSpecEntity
    {
        return $this->resource(PhpOutputResourceSpec::factory());
    }

    /**
     * フィルタの対象となるphp://fdストリームを設定したストリームフィルタスペックエンティティを返します。
     *
     * @return StreamFilterSpecEntity フィルタの対象となるphp://fdストリームを設定したストリームフィルタスペックエンティティ
     */
    public function resourceFd(): StreamFilterSpecEntity
    {
        return $this->resource(PhpFdResourceSpec::factory());
    }

    /**
     * フィルタの対象となるphp://memoryストリームを設定したストリームフィルタスペックエンティティを返します。
     *
     * @return StreamFilterSpecEntity フィルタの対象となるphp://memoryストリームを設定したストリームフィルタスペックエンティティ
     */
    public function resourceMemory(): StreamFilterSpecEntity
    {
        return $this->resource(PhpMemoryResourceSpec::factory());
    }

    /**
     * フィルタの対象となるphp://tempストリームを設定したストリームフィルタスペックエンティティを返します。
     *
     * @return StreamFilterSpecEntity フィルタの対象となるphp://tempストリームを設定したストリームフィルタスペックエンティティ
     */
    public function resourceTemp(): StreamFilterSpecEntity
    {
        return $this->resource(PhpTempResourceSpec::factory());
    }

    /**
     * フィルタの対象となるストリーム文字列を設定したストリームフィルタスペックエンティティを返します。
     *
     * @return StreamFilterSpecEntity フィルタの対象となるストリーム文字列を設定したストリームフィルタスペックエンティティ
     */
    public function resourceRaw($resource): StreamFilterSpecEntity
    {
        return $this->resource(RawResourceSpec::factory($resource));
    }

    /**
     * 書き込みチェーンに適用するフィルタのリストを取得・設定します。
     *
     * @param  null|array             $write 書き込みチェーンに適用するフィルタのリスト
     * @return array|StreamFilterSpec 書き込みチェーンに適用するフィルタのリストまたはこのインスタンス
     */
    public function write(?array $write = null)
    {
        if (\func_num_args() === 0) {
            return $this->write;
        }
        $this->write    = [];

        foreach ($write as $filter) {
            if ($filter instanceof StreamFilterSpecInterface) {
                $this->appendWriteChain($filter);
            } else {
                $this->appendWriteChain(...(array) $filter);
            }
        }

        return $this;
    }

    /**
     * 読み込みチェーンに適用するフィルタのリストを取得・設定します。
     *
     * @param  null|array             $read 読み込みチェーンに適用するフィルタのリスト
     * @return array|StreamFilterSpec 読み込みチェーンに適用するフィルタのリストまたはこのインスタンス
     */
    public function read(?array $read = null)
    {
        if (\func_num_args() === 0) {
            return $this->read;
        }
        $this->read = [];

        foreach ($read as $filter) {
            if ($filter instanceof StreamFilterSpecInterface) {
                $this->appendReadChain($filter);
            } else {
                $this->appendReadChain(...(array) $filter);
            }
        }

        return $this;
    }

    /**
     * 書き込みチェーン、読み込みチェーン双方に適用するフィルタのリストを取得・設定します。
     *
     * @param  null|array             $both 書き込みチェーン、読み込みチェーン双方に適用するフィルタのリスト
     * @return array|StreamFilterSpec 書き込みチェーン、読み込みチェーン双方に適用するフィルタのリストまたはこのインスタンス
     */
    public function both(?array $both = null)
    {
        if (\func_num_args() === 0) {
            return $this->both;
        }
        $this->both = [];

        foreach ($both as $filter) {
            if ($filter instanceof StreamFilterSpecInterface) {
                $this->appendBothChain($filter);
            } else {
                $this->appendBothChain(...(array) $filter);
            }
        }

        return $this;
    }

    /**
     * 書き込みチェーンに適用するフィルタを追加します。
     *
     * @param  StreamFilterSpecInterface|string $filter                     書き込みストリームフィルタ名
     * @param  array                            $filter_parameters          ストリームフィルタに追加するパラメータ
     * @param  string                           $filter_parameter_separator ストリームフィルタに追加するパラメータオプションのセパレータ
     * @return StreamFilterSpec                 このインスタンス
     */
    public function appendWriteChain($filter, array $filter_parameters = [], string $filter_parameter_separator = self::PARAMETER_OPTION_SEPARATOR): StreamFilterSpecEntity
    {
        $this->write[]  = [$filter, $filter_parameters, $filter_parameter_separator];

        return $this;
    }

    /**
     * 読み込みチェーンに適用するフィルタを追加します。
     *
     * @param  StreamFilterSpecInterface|string $filter                     読み込みストリームフィルタ名
     * @param  array                            $filter_parameters          ストリームフィルタに追加するパラメータ
     * @param  string                           $filter_parameter_separator ストリームフィルタに追加するパラメータオプションのセパレータ
     * @return StreamFilterSpec                 このインスタンス
     */
    public function appendReadChain($filter, array $filter_parameters = [], string $filter_parameter_separator = self::PARAMETER_OPTION_SEPARATOR): StreamFilterSpecEntity
    {
        $this->read[]  = [$filter, $filter_parameters, $filter_parameter_separator];

        return $this;
    }

    /**
     * 書き込みチェーン、読み込みチェーン双方に適用するフィルタを追加します。
     *
     * @param  StreamFilterSpecInterface|string $filter                     書き込みチェーン、読み込みチェーン双方に適用するフィルタ名
     * @param  array                            $filter_parameters          ストリームフィルタに追加するパラメータ
     * @param  string                           $filter_parameter_separator ストリームフィルタに追加するパラメータオプションのセパレータ
     * @return StreamFilterSpec                 このインスタンス
     */
    public function appendBothChain($filter, array $filter_parameters = [], string $filter_parameter_separator = self::PARAMETER_OPTION_SEPARATOR): StreamFilterSpecEntity
    {
        $this->both[]  = [$filter, $filter_parameters, $filter_parameter_separator];

        return $this;
    }

    /**
     * 書き込みチェーンフィルタ文字列を構築し返します。
     *
     * @return string 書き込みチェーンフィルタ文字列
     */
    public function buildWriteFilter(): string
    {
        $filters    = [];

        foreach ($this->write as $filter_set) {
            $filter = $filter_set[0];

            if ($filter instanceof StreamFilterSpecInterface) {
                $filter = $filter->build();
            } else {
                $parameter_option_separator = $filter_set[2]         ?? static::PARAMETER_OPTION_SEPARATOR;
                $filter_parameters          = (array) $filter_set[1] ?? [];

                if (!empty($filter_parameters)) {
                    $filter = \sprintf('%s%s%s', $filter[0], $parameter_option_separator, \implode($parameter_option_separator, $filter_parameters));
                } else {
                    $filter = $filter[0];
                }
            }

            $filters[]  = \str_replace('/', '%2F', $filter);
        }

        if (empty($filters)) {
            return '';
        }

        return \sprintf('write=%s', \implode(static::PARAMETER_CHAIN_SEPARATOR, $filters));
    }

    /**
     * 読み込みチェーンフィルタ文字列を構築し返します。
     *
     * @return string 読み込みチェーンフィルタ文字列
     */
    public function buildReadFilter(): string
    {
        $filters    = [];

        foreach ($this->read as $filter_set) {
            $filter = $filter_set[0];

            if ($filter instanceof StreamFilterSpecInterface) {
                $filter = $filter->build();
            } else {
                $parameter_option_separator = $filter_set[2]         ?? static::PARAMETER_OPTION_SEPARATOR;
                $filter_parameters          = (array) $filter_set[1] ?? [];

                if (!empty($filter_parameters)) {
                    $filter = \sprintf('%s%s%s', $filter[0], $parameter_option_separator, \implode($parameter_option_separator, $filter_parameters));
                } else {
                    $filter = $filter[0];
                }
            }

            $filters[]  = \str_replace('/', '%2F', $filter);
        }

        if (empty($filters)) {
            return '';
        }

        return \sprintf('read=%s', \implode(static::PARAMETER_CHAIN_SEPARATOR, $filters));
    }

    /**
     * 書き込みチェーン、読み込みチェーン双方に適用するフィルタ文字列を構築し返します。
     *
     * @return string 書き込みチェーン、読み込みチェーン双方に適用するフィルタ文字列
     */
    public function buildBothFilter(): string
    {
        $filters    = [];

        foreach ($this->both as $filter_set) {
            $filter = $filter_set[0];

            if ($filter instanceof StreamFilterSpecInterface) {
                $filter = $filter->build();
            } else {
                $parameter_option_separator = $filter_set[2]         ?? static::PARAMETER_OPTION_SEPARATOR;
                $filter_parameters          = (array) $filter_set[1] ?? [];

                if (!empty($filter_parameters)) {
                    $filter = \sprintf('%s%s%s', $filter[0], $parameter_option_separator, \implode($parameter_option_separator, $filter_parameters));
                } else {
                    $filter = $filter[0];
                }
            }

            $filters[]  = \str_replace('/', '%2F', $filter);
        }

        if (empty($filters)) {
            return '';
        }

        return \sprintf('%s', \implode(static::PARAMETER_CHAIN_SEPARATOR, $filters));
    }

    /**
     * リソース文字列を構築し返します。
     *
     * @return string リソース文字列
     */
    public function buildResource(): string
    {
        if ($this->resource === null) {
            return '';
        }

        return \sprintf('resource=%s', $this->resource->build());
    }

    /**
     * フィルタストリーム設定文字列を構築し返します。
     *
     * @return string フィルタストリーム設定文字列を構築し返します
     */
    public function build(): string
    {
        $parameters = [
            'php://filter',
        ];

        if ('' !== ($write_filter = $this->buildWriteFilter())) {
            $parameters[]   = $write_filter;
        }

        if ('' !== ($raed_filter = $this->buildReadFilter())) {
            $parameters[]   = $raed_filter;
        }

        if ('' !== ($both_filter = $this->buildBothFilter())) {
            $parameters[]   = $both_filter;
        }

        if (\count($parameters) === 1) {
            return $this->resource->build();
        }

        if ('' !== ($resource = $this->buildResource())) {
            $parameters[]   = $resource;
        }

        return \implode(static::PARAMETER_SEPARATOR, $parameters);
    }

    /**
     * フィルタストリーム設定文字列を構築し返します。
     *
     * @return string フィルタストリーム設定文字列を構築し返します
     */
    public function __toString(): string
    {
        return $this->build();
    }

    /**
     * __invoke
     *
     * @return string フィルタストリーム設定文字列を構築し返します
     */
    public function __invoke(): string
    {
        return $this->build();
    }
}
