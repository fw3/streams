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

namespace fw3\streams\utilitys\stream_filter_event\events;

use fw3\streams\utilitys\stream_filter_event\events\traits\StreamFilterEventInterface;
use fw3\streams\utilitys\stream_filter_event\events\traits\StreamFilterEventTrait;

/**
 * エンコーディング変換イベント
 */
final class ConvertEncodingEvent implements StreamFilterEventInterface
{
    use StreamFilterEventTrait;

    /**
     * コンストラクタ
     *
     * @param string $message メッセージ
     */
    public function __construct(
        string $message,
        ?array $values
    ) {
        $this->message  = $message;
        $this->values   = $values;
    }
}
