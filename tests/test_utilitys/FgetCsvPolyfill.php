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

namespace fw3\tests\streams\test_utilitys;

\define('FGETCSV_ESCAPE', \version_compare(\PHP_VERSION, '7.4.0', 'lt') ? "\\" : '');

/**
 * fgetcsvのPHPのバージョン差を吸収するためのクラス
 */
final class FgetCsvPolyfill{
    /**
     * @var string fgetcsv用エスケープ PHP7.4.0を境に切り替える必要がある
     */
    public const FGETCSV_ESCAPE = FGETCSV_ESCAPE;
}
