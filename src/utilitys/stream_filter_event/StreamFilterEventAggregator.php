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
use fw3\streams\utilitys\stream_filter_event\traits\StreamFilterEventAggregatorTrait;

/**
 * ストリームフィルタ実行時のイベントログを集めます。
 */
final class StreamFilterEventAggregator implements StreamFilterEventAggregatorInterface
{
    use StreamFilterEventAggregatorTrait;
}
