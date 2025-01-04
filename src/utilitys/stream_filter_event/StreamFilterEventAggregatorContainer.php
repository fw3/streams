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

namespace fw3\streams\utilitys\stream_filter_event;

use fw3\streams\utilitys\stream_filter_event\traits\StreamFilterEventAggregatorInterface;

/**
 * ストリームフィルタ実行時のイベントログアグリゲータコンテナ
 */
final class StreamFilterEventAggregatorContainer
{
    /**
     * @var string|StreamFilterEventAggregatorInterface ストリームフィルタ実行時のイベントログアグリゲータ
     */
    private static $streamFilterEventAggregator = StreamFilterEventAggregator::class;

    /**
     * ストリームフィルタ実行時のイベントログアグリゲータを設定します。
     *
     * @param  string|StreamFilterEventAggregatorInterface $streamFilterEventAggregator ストリームフィルタ実行時のイベントログアグリゲータ
     * @return string                                      このクラスパス
     */
    public static function set($streamFilterEventAggregator): string
    {
        if (\is_string($streamFilterEventAggregator)) {
            if (\is_a($streamFilterEventAggregator, StreamFilterEventAggregatorInterface::class, true)) {
                throw new \InvalidArgumentException(\sprintf('イベントログアグリゲータが`%s`を実装していないか、クラスがロードされていません。class_path:`%s`', StreamFilterEventAggregatorInterface::class, $streamFilterEventAggregator));
            }
        } else {
            if ($streamFilterEventAggregator instanceof StreamFilterEventAggregatorInterface) {
                throw new \InvalidArgumentException(\sprintf('イベントログアグリゲータが`%s`を実装していません。class_path:`%s`', StreamFilterEventAggregatorInterface::class, \get_class($streamFilterEventAggregator)));
            }
        }

        self::$streamFilterEventAggregator  = $streamFilterEventAggregator;

        return self::class;
    }

    /**
     * ストリームフィルタ実行時のイベントログアグリゲータを返します。
     *
     * @return StreamFilterEventAggregatorInterface ストリームフィルタ実行時のイベントログアグリゲータ
     */
    public static function get(): StreamFilterEventAggregatorInterface
    {
        if (\is_string(self::$streamFilterEventAggregator)) {
            $class_path                         = self::$streamFilterEventAggregator;
            self::$streamFilterEventAggregator  = new $class_path();
        }

        return self::$streamFilterEventAggregator;
    }
}
