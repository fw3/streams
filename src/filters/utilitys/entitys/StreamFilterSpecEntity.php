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

namespace fw3\streams\filters\utilitys\entitys;

use fw3\streams\filters\utilitys\specs\interfaces\StreamFilterSpecInterface;

/**
 * ストリームフィルタ設定を扱うクラスです。
 */
class StreamFilterSpecEntity
{
    //==============================================
    // const
    //==============================================
    // フィルタパラメータ
    //----------------------------------------------
    /**
     * @var string  フィルタパラメータ間のセパレータ
     */
    public const PARAMETER_SEPARATOR        = '/';

    /**
     * @var string  パラメータチェーン間のセパレータ
     */
    public const PARAMETER_CHAIN_SEPARATOR  = '|';

    /**
     * @var string  パラメータオプション間のセパレータ
     */
    public const PARAMETER_OPTION_SEPARATOR = '/';

    //----------------------------------------------
    // resource
    //----------------------------------------------
    /**
     * @var string  リソース名：stdin
     */
    public const RESOURCE_PHP_STDIN     = 'php://stdin';

    /**
     * @var string  リソース名：strout
     */
    public const RESOURCE_PHP_STDOUT    = 'php://stdout';

    /**
     * @var string  リソース名：strerr
     */
    public const RESOURCE_PHP_STDERR    = 'php://stderr';

    /**
     * @var string  リソース名：input
     */
    public const RESOURCE_PHP_INPUT     = 'php://input';

    /**
     * @var string  リソース名：output
     */
    public const RESOURCE_PHP_OUTPUT    = 'php://output';

    /**
     * @var string  リソース名：fd
     */
    public const RESOURCE_PHP_FD        = 'php://fd';

    /**
     * @var string  リソース名：memory
     */
    public const RESOURCE_PHP_MEMORY    = 'php://memory';

    /**
     * @var string  リソース名：temp
     */
    public const RESOURCE_PHP_TEMP      = 'php://temp';

    //==============================================
    // property
    //==============================================
    // フィルタパラメータ
    //----------------------------------------------
    /**
     * @var string|\SplFileInfo|\SplFileObject  フィルタの対象となるストリーム
     */
    protected $resource = null;

    /**
     * @var array   書き込みチェーンに適用するフィルタのリスト
     */
    protected $write    = [];

    /**
     * @var array   読み込みチェーンに適用するフィルタのリスト
     */
    protected $read     = [];

    /**
     * @var array   書き込みチェーン、読み込みチェーン双方に適用するフィルタのリスト
     */
    protected $both     = [];

    //==============================================
    // static method
    //==============================================
    /**
     * ストリームフィルタスペックインスタンスを返します。
     *
     * @param   array   $spec   スペック
     *  [
     *      'resource'  => フィルタの対象となるストリーム
     *      'write'     => 書き込みチェーンに適用するフィルタのリスト
     *      'read'      => 読み込みチェーンに適用するフィルタのリスト
     *      'both'      => 書き込みチェーン、読み込みチェーン双方に適用するフィルタのリスト
     *  ]
     * @return  \fw3\streams\filters\utilitys\StreamFilterSpec このインスタンス
     */
    public static function factory(array $spec = []) : \fw3\streams\filters\utilitys\entitys\StreamFilterSpecEntity
    {
        $instance   = new static();

        if (!empty($spec)) {
            if (isset($spec['resource']) || array_key_exists('resource', $spec)) {
                $instance->resource($spec['resource']);
            }

            if (isset($spec['write']) || array_key_exists('write', $spec)) {
                $instance->write($spec['write']);
            }

            if (isset($spec['read']) || array_key_exists('read', $spec)) {
                $instance->read($spec['read']);
            }

            if (isset($spec['both']) || array_key_exists('both', $spec)) {
                $instance->both($spec['both']);
            }
        }

        return $instance;
    }

    //==============================================
    // method
    //==============================================
    /**
     * constructor
     */
    protected function __construct()
    {
    }

    /**
     * このインスタンスを複製し返します。
     *
     * @return  \fw3\streams\filters\utilitys\StreamFilterSpec   複製されたこのインスタンス
     */
    public function with()
    {
        return clone $this;
    }

    /**
     * フィルタの対象となるストリームを取得・設定します。
     *
     * @param   null|string|\SplFileInfo|\SplFileObject $resource   フィルタの対象となるストリーム
     * @return  string|\SplFileInfo|\SplFileObject|\fw3\streams\filters\utilitys\StreamFilterSpec  フィルタの対象となるストリームまたはこのインスタンス
     */
    public function resource($resource = null)
    {
        if (func_num_args() === 0) {
            return $resource;
        }
        $this->resource = $resource;
        return $this;
    }

    /**
     * フィルタの対象となるphp://stdinストリームを設定したストリームフィルタスペックエンティティを返します。
     *
     * @return  \fw3\streams\filters\utilitys\entitys\StreamFilterSpecEntity フィルタの対象となるphp://stdinストリームを設定したストリームフィルタスペックエンティティ
     */
    public function resourceStdin() : \fw3\streams\filters\utilitys\entitys\StreamFilterSpecEntity
    {
        return $this->resource(static::RESOURCE_PHP_STDIN);
    }

    /**
     * フィルタの対象となるphp://stdoutストリームを設定したストリームフィルタスペックエンティティを返します。
     *
     * @return  \fw3\streams\filters\utilitys\entitys\StreamFilterSpecEntity フィルタの対象となるphp://stdoutストリームを設定したストリームフィルタスペックエンティティ
     */
    public function resourceStdout() : \fw3\streams\filters\utilitys\entitys\StreamFilterSpecEntity
    {
        return $this->resource(static::RESOURCE_PHP_STDOUT);
    }

    /**
     * フィルタの対象となるphp://inputストリームを設定したストリームフィルタスペックエンティティを返します。
     *
     * @return  \fw3\streams\filters\utilitys\entitys\StreamFilterSpecEntity フィルタの対象となるphp://inputストリームを設定したストリームフィルタスペックエンティティ
     */
    public function resourceInput() : \fw3\streams\filters\utilitys\entitys\StreamFilterSpecEntity
    {
        return $this->resource(static::RESOURCE_PHP_INPUT);
    }

    /**
     * フィルタの対象となるphp://outputストリームを設定したストリームフィルタスペックエンティティを返します。
     *
     * @return  \fw3\streams\filters\utilitys\entitys\StreamFilterSpecEntity フィルタの対象となるphp://outputストリームを設定したストリームフィルタスペックエンティティ
     */
    public function resourceOutput() : \fw3\streams\filters\utilitys\entitys\StreamFilterSpecEntity
    {
        return $this->resource(static::RESOURCE_PHP_OUTPUT);
    }

    /**
     * フィルタの対象となるphp://fdストリームを設定したストリームフィルタスペックエンティティを返します。
     *
     * @return  \fw3\streams\filters\utilitys\entitys\StreamFilterSpecEntity フィルタの対象となるphp://fdストリームを設定したストリームフィルタスペックエンティティ
     */
    public function resourceFd() : \fw3\streams\filters\utilitys\entitys\StreamFilterSpecEntity
    {
        return $this->resource(static::RESOURCE_PHP_FD);
    }

    /**
     * フィルタの対象となるphp://memoryストリームを設定したストリームフィルタスペックエンティティを返します。
     *
     * @return  \fw3\streams\filters\utilitys\entitys\StreamFilterSpecEntity フィルタの対象となるphp://memoryストリームを設定したストリームフィルタスペックエンティティ
     */
    public function resourceMemory() : \fw3\streams\filters\utilitys\entitys\StreamFilterSpecEntity
    {
        return $this->resource(static::RESOURCE_PHP_MEMORY);
    }

    /**
     * フィルタの対象となるphp://tempストリームを設定したストリームフィルタスペックエンティティを返します。
     *
     * @return  \fw3\streams\filters\utilitys\entitys\StreamFilterSpecEntity フィルタの対象となるphp://tempストリームを設定したストリームフィルタスペックエンティティ
     */
    public function resourceTemp() : \fw3\streams\filters\utilitys\entitys\StreamFilterSpecEntity
    {
        return $this->resource(static::RESOURCE_PHP_TEMP);
    }

    /**
     * 書き込みチェーンに適用するフィルタのリストを取得・設定します。
     *
     * @param   null|array  $write  書き込みチェーンに適用するフィルタのリスト
     * @return  array|\fw3\streams\filters\utilitys\StreamFilterSpec  書き込みチェーンに適用するフィルタのリストまたはこのインスタンス
     */
    public function write($write = null)
    {
        if (func_num_args() === 0) {
            return $write;
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
     * @param   null|array  $read   読み込みチェーンに適用するフィルタのリスト
     * @return  array|\fw3\streams\filters\utilitys\StreamFilterSpec  読み込みチェーンに適用するフィルタのリストまたはこのインスタンス
     */
    public function read($read = null)
    {
        if (func_num_args() === 0) {
            return $read;
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
     * @param   null|array  $both   書き込みチェーン、読み込みチェーン双方に適用するフィルタのリスト
     * @return  array|\fw3\streams\filters\utilitys\StreamFilterSpec  書き込みチェーン、読み込みチェーン双方に適用するフィルタのリストまたはこのインスタンス
     */
    public function both($both = null)
    {
        if (func_num_args() === 0) {
            return $both;
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
     * @param   \fw3\streams\filters\utilitys\specs\interfaces\StreamFilterSpecInterface|string  $filter 書き込みストリームフィルタ名
     * @param   array   $filter_parameters          ストリームフィルタに追加するパラメータ
     * @param   string  $filter_parameter_separator ストリームフィルタに追加するパラメータオプションのセパレータ
     * @return  \fw3\streams\filters\utilitys\StreamFilterSpec このインスタンス
     */
    public function appendWriteChain($filter, array $filter_parameters = [], string $filter_parameter_separator = self::PARAMETER_OPTION_SEPARATOR) : \fw3\streams\filters\utilitys\entitys\StreamFilterSpecEntity
    {
        $this->write[]  = [$filter, $filter_parameters, $filter_parameter_separator];
        return $this;
    }

    /**
     * 読み込みチェーンに適用するフィルタを追加します。
     *
     * @param   \fw3\streams\filters\utilitys\specs\interfaces\StreamFilterSpecInterface|string  $filter 読み込みストリームフィルタ名
     * @param   array   $filter_parameters          ストリームフィルタに追加するパラメータ
     * @param   string  $filter_parameter_separator ストリームフィルタに追加するパラメータオプションのセパレータ
     * @return  \fw3\streams\filters\utilitys\StreamFilterSpec このインスタンス
     */
    public function appendReadChain($filter, array $filter_parameters = [], string $filter_parameter_separator = self::PARAMETER_OPTION_SEPARATOR) : \fw3\streams\filters\utilitys\entitys\StreamFilterSpecEntity
    {
        $this->read[]  = [$filter, $filter_parameters, $filter_parameter_separator];
        return $this;
    }

    /**
     * 書き込みチェーン、読み込みチェーン双方に適用するフィルタを追加します。
     *
     * @param   \fw3\streams\filters\utilitys\specs\interfaces\StreamFilterSpecInterface|string  $filter 書き込みチェーン、読み込みチェーン双方に適用するフィルタ名。
     * @param   array   $filter_parameters          ストリームフィルタに追加するパラメータ
     * @param   string  $filter_parameter_separator ストリームフィルタに追加するパラメータオプションのセパレータ
     * @return  \fw3\streams\filters\utilitys\StreamFilterSpec このインスタンス
     */
    public function appendBothChain($filter, array $filter_parameters = [], string $filter_parameter_separator = self::PARAMETER_OPTION_SEPARATOR) : \fw3\streams\filters\utilitys\entitys\StreamFilterSpecEntity
    {
        $this->both[]  = [$filter, $filter_parameters, $filter_parameter_separator];
        return $this;
    }

    /**
     * 書き込みチェーンフィルタ文字列を構築し返します。
     *
     * @return  string  書き込みチェーンフィルタ文字列
     */
    public function buildWriteFilter() : string
    {
        $filters    = [];

        foreach ($this->write as $filter_set) {
            $filter = $filter_set[0];
            if ($filter instanceof StreamFilterSpecInterface) {
                $filter = $filter->build();
            } else {
                $parameter_option_separator = $filter_set[2] ?? static::PARAMETER_OPTION_SEPARATOR;
                $filter_parameters  = (array) $filter_set[1] ?? [];

                if (!empty($filter_parameters)) {
                    $filter = sprintf('%s%s%s', $filter[0], $parameter_option_separator, implode($parameter_option_separator, $filter_parameters));
                } else {
                    $filter = $filter[0];
                }
            }

            $filters[]  = str_replace('/', '%2F', $filter);
        }

        if (empty($filters)) {
            return '';
        }

        return sprintf('write=%s', implode(static::PARAMETER_CHAIN_SEPARATOR, $filters));
    }

    /**
     * 読み込みチェーンフィルタ文字列を構築し返します。
     *
     * @return  string  読み込みチェーンフィルタ文字列
     */
    public function buildReadFilter() : string
    {
        $filters    = [];

        foreach ($this->read as $filter_set) {
            $filter = $filter_set[0];
            if ($filter instanceof StreamFilterSpecInterface) {
                $filter = $filter->build();
            } else {
                $parameter_option_separator = $filter_set[2] ?? static::PARAMETER_OPTION_SEPARATOR;
                $filter_parameters  = (array) $filter_set[1] ?? [];

                if (!empty($filter_parameters)) {
                    $filter = sprintf('%s%s%s', $filter[0], $parameter_option_separator, implode($parameter_option_separator, $filter_parameters));
                } else {
                    $filter = $filter[0];
                }
            }

            $filters[]  = str_replace('/', '%2F', $filter);
        }

        if (empty($filters)) {
            return '';
        }

        return sprintf('read=%s', implode(static::PARAMETER_CHAIN_SEPARATOR, $filters));
    }

    /**
     * 書き込みチェーン、読み込みチェーン双方に適用するフィルタ文字列を構築し返します。
     *
     * @return  string  書き込みチェーン、読み込みチェーン双方に適用するフィルタ文字列
     */
    public function buildBothFilter() : string
    {
        $filters    = [];

        foreach ($this->both as $filter_set) {
            $filter = $filter_set[0];
            if ($filter instanceof StreamFilterSpecInterface) {
                $filter = $filter->build();
            } else {
                $parameter_option_separator = $filter_set[2] ?? static::PARAMETER_OPTION_SEPARATOR;
                $filter_parameters  = (array) $filter_set[1] ?? [];

                if (!empty($filter_parameters)) {
                    $filter = sprintf('%s%s%s', $filter[0], $parameter_option_separator, implode($parameter_option_separator, $filter_parameters));
                } else {
                    $filter = $filter[0];
                }
            }

            $filters[]  = str_replace('/', '%2F', $filter);
        }

        if (empty($filters)) {
            return '';
        }

        return sprintf('%s', implode(static::PARAMETER_CHAIN_SEPARATOR, $filters));
    }

    /**
     * リソース文字列を構築し返します。
     *
     * @return  string  リソース文字列
     */
    public function buildResource() : string
    {
        if ($this->resource === null) {
            return '';
        }
        return sprintf('resource=%s', (string) $this->resource);
    }

    /**
     * フィルタストリーム設定文字列を構築し返します。
     *
     * @return  string  フィルタストリーム設定文字列を構築し返します。
     */
    public function build() : string
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

        if ('' !== ($resource = $this->buildResource())) {
            $parameters[]   = $resource;
        }

        return implode(static::PARAMETER_SEPARATOR, $parameters);
    }

    /**
     * フィルタストリーム設定文字列を構築し返します。
     *
     * @return  string  フィルタストリーム設定文字列を構築し返します。
     */
    public function __toString() : string
    {
        return $this->build();
    }

    /**
     * __invoke
     *
     * @return  string  フィルタストリーム設定文字列を構築し返します。
     */
    public function __invoke() : string
    {
        return $this->build();
    }
}
