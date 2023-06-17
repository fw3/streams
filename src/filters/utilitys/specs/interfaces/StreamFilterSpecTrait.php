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

namespace fw3\streams\filters\utilitys\specs\interfaces;

/**
 * ストリームフィルタ設定特性です。
 */
trait StreamFilterSpecTrait
{
    /**
     * constructor
     */
    protected function __construct()
    {
    }

    /**
     * このインスタンスを複製し返します。
     *
     * @return StreamFilterSpecInterface 複製されたこのインスタンス
     */
    public function with(): StreamFilterSpecInterface
    {
        return clone $this;
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
