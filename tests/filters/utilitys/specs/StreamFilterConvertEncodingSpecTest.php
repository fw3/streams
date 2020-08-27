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

namespace Tests\streams\filters\utilitys\specs;

use PHPUnit\Framework\TestCase;
use fw3\streams\filters\utilitys\StreamFilterSpec;
use fw3\streams\filters\utilitys\specs\StreamFilterConvertEncodingSpec;

/**
 * ストリームフィルタ：ConvertEncodingFilterSpecのテスト
 */
class StreamFilterConvertEncodingSpecTest extends TestCase
{
    /**
     * 現在のフィルタ名のストリームフィルタが登録されているかのテスト
     *
     * @runInSeparateProcess
     */
    public function testRegisteredFilterName()
    {
        $this->assertFalse(StreamFilterConvertEncodingSpec::registeredFilterName());

        StreamFilterSpec::registerConvertEncodingFilter();

        $this->assertTrue(StreamFilterConvertEncodingSpec::registeredFilterName());

        StreamFilterConvertEncodingSpec::filterName('test.convert.encoding');
        $this->assertFalse(StreamFilterConvertEncodingSpec::registeredFilterName());
    }
}
