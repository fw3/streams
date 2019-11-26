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

namespace fw3\streams\filters\utilitys\specs\interfaces;

/**
 * ストリームフィルタ設定を扱うインターフェースです。
 */
interface StreamFilterSpecInterface
{
    /**
     * このインスタンスを複製し返します。
     *
     * @return  \fw3\streams\filters\utilitys\specs\interfaces\StreamFilterSpecInterface 複製されたこのインスタンス
     */
    public function with();

    /**
     * チェーンフィルタ用文字列を構築して返します。
     *
     * @return  string  チェーンフィルタ用文字列
     */
    public function build() : string;

    /**
     * フィルタストリーム設定文字列を構築し返します。
     *
     * @return  string  フィルタストリーム設定文字列を構築し返します。
     */
    public function __toString() : string;

    /**
     * __invoke
     *
     * @return  string  フィルタストリーム設定文字列を構築し返します。
     */
    public function __invoke() : string;
}
