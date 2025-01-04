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

namespace fw3\streams\utilitys\stream_filter_event\events\traits;

/**
 * ストリームフィルタイベントインターフェース
 */
interface StreamFilterEventInterface
{
    /**
     * ストリームフィルタイベントタイプを返します。
     *
     * @return string ストリームフィルタイベントタイプ
     */
    public function getType(): string;

    /**
     * メッセージを返します。
     *
     * @return string メッセージ
     */
    public function getMessage(): string;

    /**
     * イベント発生時の値を返します。
     *
     * @return ?array イベント発生時の値
     */
    public function getValues(): ?array;

    /**
     * タイムスタンプを返します。
     *
     * @return \DateTimeImmutable タイムスタンプ
     */
    public function getTimestamp(): \DateTimeImmutable;
}
