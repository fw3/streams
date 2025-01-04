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

namespace fw3\streams\utilitys\stream_filter_event\traits;

use fw3\streams\utilitys\stream_filter_event\events\traits\StreamFilterEventInterface;

/**
 * ストリームフィルタ実行時のイベントログアグリゲータインターフェース。
 */
interface StreamFilterEventAggregatorInterface
{
    /**
     * イベントを追加します。
     *
     * @param  StreamFilterEventInterface $event ストリームフィルタイベント
     * @return このインスタンス
     */
    public function addEvent(StreamFilterEventInterface $event): StreamFilterEventAggregatorInterface;

    /**
     * 収集されたイベントを取得する
     *
     * @return StreamFilterEventInterface[] ストリームフィルタイベント
     */
    public function getEvents(): array;

    /**
     * 特定のタイプのイベントを取得する
     *
     * @return StreamFilterEventInterface[] ストリームフィルタイベント
     */
    public function getEventsByType(string $type): array;

    /**
     * イベントが存在するかどうかを返します。
     *
     * @return bool イベントが存在するかどうか
     */
    public function hasEvents(): bool;

    /**
     * イベントをクリアします。
     *
     * @return self このインスタンス
     */
    public function clearEvents(): StreamFilterEventAggregatorInterface;
}
