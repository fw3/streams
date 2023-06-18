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

namespace fw3\streams\filters\utilitys\specs\entitys\resources\traits;

/**
 * リソーススペックエンティティ特性
 */
trait ResourceSpecTrait
{
    /**
     * リソースタイプを返します。
     *
     * @return string リソースタイプ
     */
    abstract public static function getResourceType(): string;

    /**
     * リソース文字列を構築して返します。
     *
     * @return string リソース文字列
     */
    abstract public function build(): string;

    /**
     * コンテキストオプションを返します。
     *
     * @return array コンテキストオプション
     */
    public function getContextOptions(): array
    {
        return [];
    }

    /**
     * コンテキストパラメータを返します。
     *
     * @return array コンテキストパラメータ
     */
    public function getContextParams(): array
    {
        return [];
    }

    /**
     * ストリームコンテキストを作成して返します。
     *
     * @param  array    $add_context_options 追加で指定したいコンテキストオプション
     * @param  array    $add_context_params  追加で指定したいコンテキストパラメータ
     * @return resource ストリームコンテキスト
     */
    public function createStreamContext(array $add_context_options = [], array $add_context_params = [])
    {
        return \stream_context_create(
            \array_merge(
                $this->getContextOptions(),
                $add_context_options,
            ),
            \array_merge(
                $add_context_params,
                $this->getContextParams(),
            ),
        );
    }

    /**
     * オブジェクトの文字列表現を返します。
     *
     * @return string オブジェクトの文字列表現
     */
    public function __toString(): string
    {
        return self::getResourceType();
    }
}
