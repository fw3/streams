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

namespace Tests\streams\filters\utilitys;

use PHPUnit\Framework\TestCase;
use fw3\streams\filters\utilitys\StreamFilterSpec;
use fw3\streams\filters\utilitys\specs\StreamFilterConvertEncodingSpec;
use fw3\streams\filters\utilitys\specs\StreamFilterConvertLinefeedSpec;

/**
 * ストリームフィルタスペックのテスト
 */
class StreamFilterSpecTest extends TestCase
{
    /**
     * Setup
     */
    protected function setUp() : void
    {
    }

    /**
     * Specインスタンスのテスト
     */
    public function testSpec() : void
    {
        $streamFilterSpec                   = StreamFilterSpec::factory();
        $streamFilterConvertEncodingSpec    = StreamFilterConvertEncodingSpec::factory();
        $streamFilterConvertLinefeedSpec    = StreamFilterConvertLinefeedSpec::factory();

        $this->assertEquals($streamFilterSpec->with(), $streamFilterSpec);
        $this->assertNotSame($streamFilterSpec->with(), $streamFilterSpec);

        $this->assertSame($streamFilterSpec->with()->appendWriteChain($streamFilterConvertEncodingSpec->with()->setupForSjisOut())->build(), 'php://filter/write=convert.encoding.SJIS-win:default');
        $this->assertSame($streamFilterSpec->with()->appendWriteChain($streamFilterConvertEncodingSpec->with()->setupForEucjpOut())->build(), 'php://filter/write=convert.encoding.eucJP-win:default');
        $this->assertSame($streamFilterSpec->with()->appendWriteChain($streamFilterConvertEncodingSpec->with()->setupForUtf8Out())->build(), 'php://filter/write=convert.encoding.UTF-8:default');

        $this->assertSame($streamFilterSpec->with()->appendWriteChain($streamFilterConvertLinefeedSpec->with()->setupForUnix())->build(), 'php://filter/write=convert.linefeed.LF:ALL');
        $this->assertSame($streamFilterSpec->with()->appendWriteChain($streamFilterConvertLinefeedSpec->with()->setupForWindows())->build(), 'php://filter/write=convert.linefeed.CRLF:ALL');
    }
}
