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

namespace Tests\streams\filters\utilitys\specs\entitys\resources;

use fw3\streams\filters\utilitys\specs\entitys\resources\FileResourceSpec;
use fw3\streams\filters\utilitys\specs\entitys\resources\PhpFdResourceSpec;
use fw3\streams\filters\utilitys\specs\entitys\resources\PhpInputResourceSpec;
use fw3\streams\filters\utilitys\specs\entitys\resources\PhpMemoryResourceSpec;
use fw3\streams\filters\utilitys\specs\entitys\resources\PhpOutputResourceSpec;
use fw3\streams\filters\utilitys\specs\entitys\resources\PhpStderrResourceSpec;
use fw3\streams\filters\utilitys\specs\entitys\resources\PhpStdinResourceSpec;
use fw3\streams\filters\utilitys\specs\entitys\resources\PhpStdoutResourceSpec;
use fw3\streams\filters\utilitys\specs\entitys\resources\PhpTempResourceSpec;
use fw3\streams\filters\utilitys\specs\entitys\resources\RawResourceSpec;
use fw3\streams\filters\utilitys\specs\entitys\resources\ZipResourceSpec;
use PHPUnit\Framework\TestCase;

/**
 * ResourceSpecのテスト
 * @internal
 */

/**
 * @internal
 */
class ResourceSpecTest extends TestCase
{
    /**
     * 各種リソーススペッククラスのテスト
     *
     * @test
     */
    public function resourceSpec(): void
    {
        $this->assertSame('', FileResourceSpec::getResourceType());
        $this->assertInstanceOf(FileResourceSpec::class, $resourceSpec = FileResourceSpec::factory('test.csv'));
        $this->assertSame('', (string) $resourceSpec);
        $this->assertSame('test.csv', $resourceSpec->build());
        $this->assertSame([], $resourceSpec->getContextOptions());
        $this->assertSame([], $resourceSpec->getContextParams());
        $this->assertSame('resource', \gettype($resourceSpec->createStreamContext()));

        $resourceSpec = FileResourceSpec::factory(new \SplFileInfo('test.csv'));
        $this->assertSame('', (string) $resourceSpec);
        $this->assertSame('test.csv', $resourceSpec->build());
        $this->assertSame([], $resourceSpec->getContextOptions());
        $this->assertSame([], $resourceSpec->getContextParams());
        $this->assertSame('resource', \gettype($resourceSpec->createStreamContext()));

        $this->assertSame('php://fd', PhpFdResourceSpec::getResourceType());
        $this->assertInstanceOf(PhpFdResourceSpec::class, $resourceSpec = PhpFdResourceSpec::factory());
        $this->assertSame('php://fd', (string) $resourceSpec);
        $this->assertSame('php://fd', $resourceSpec->build());
        $this->assertSame([], $resourceSpec->getContextOptions());
        $this->assertSame([], $resourceSpec->getContextParams());
        $this->assertSame('resource', \gettype($resourceSpec->createStreamContext()));

        $this->assertSame('php://input', PhpInputResourceSpec::getResourceType());
        $this->assertInstanceOf(PhpInputResourceSpec::class, $resourceSpec = PhpInputResourceSpec::factory());
        $this->assertSame('php://input', (string) $resourceSpec);
        $this->assertSame('php://input', $resourceSpec->build());
        $this->assertSame([], $resourceSpec->getContextOptions());
        $this->assertSame([], $resourceSpec->getContextParams());
        $this->assertSame('resource', \gettype($resourceSpec->createStreamContext()));

        $this->assertSame('php://memory', PhpMemoryResourceSpec::getResourceType());
        $this->assertInstanceOf(PhpMemoryResourceSpec::class, $resourceSpec = PhpMemoryResourceSpec::factory());
        $this->assertSame('php://memory', (string) $resourceSpec);
        $this->assertSame('php://memory', $resourceSpec->build());
        $this->assertSame([], $resourceSpec->getContextOptions());
        $this->assertSame([], $resourceSpec->getContextParams());
        $this->assertSame('resource', \gettype($resourceSpec->createStreamContext()));

        $this->assertSame('php://output', PhpOutputResourceSpec::getResourceType());
        $this->assertInstanceOf(PhpOutputResourceSpec::class, $resourceSpec = PhpOutputResourceSpec::factory());
        $this->assertSame('php://output', (string) $resourceSpec);
        $this->assertSame('php://output', $resourceSpec->build());
        $this->assertSame([], $resourceSpec->getContextOptions());
        $this->assertSame([], $resourceSpec->getContextParams());
        $this->assertSame('resource', \gettype($resourceSpec->createStreamContext()));

        $this->assertSame('php://stderr', PhpStderrResourceSpec::getResourceType());
        $this->assertInstanceOf(PhpStderrResourceSpec::class, $resourceSpec = PhpStderrResourceSpec::factory());
        $this->assertSame('php://stderr', (string) $resourceSpec);
        $this->assertSame('php://stderr', $resourceSpec->build());
        $this->assertSame([], $resourceSpec->getContextOptions());
        $this->assertSame([], $resourceSpec->getContextParams());
        $this->assertSame('resource', \gettype($resourceSpec->createStreamContext()));

        $this->assertSame('php://stdin', PhpStdinResourceSpec::getResourceType());
        $this->assertInstanceOf(PhpStdinResourceSpec::class, $resourceSpec = PhpStdinResourceSpec::factory());
        $this->assertSame('php://stdin', (string) $resourceSpec);
        $this->assertSame('php://stdin', $resourceSpec->build());
        $this->assertSame([], $resourceSpec->getContextOptions());
        $this->assertSame([], $resourceSpec->getContextParams());
        $this->assertSame('resource', \gettype($resourceSpec->createStreamContext()));

        $this->assertSame('php://stdout', PhpStdoutResourceSpec::getResourceType());
        $this->assertInstanceOf(PhpStdoutResourceSpec::class, $resourceSpec = PhpStdoutResourceSpec::factory());
        $this->assertSame('php://stdout', (string) $resourceSpec);
        $this->assertSame('php://stdout', $resourceSpec->build());
        $this->assertSame([], $resourceSpec->getContextOptions());
        $this->assertSame([], $resourceSpec->getContextParams());
        $this->assertSame('resource', \gettype($resourceSpec->createStreamContext()));

        $this->assertSame('php://temp', PhpTempResourceSpec::getResourceType());
        $this->assertInstanceOf(PhpTempResourceSpec::class, $resourceSpec = PhpTempResourceSpec::factory());
        $this->assertSame('php://temp', (string) $resourceSpec);
        $this->assertSame('php://temp', $resourceSpec->build());
        $this->assertSame([], $resourceSpec->getContextOptions());
        $this->assertSame([], $resourceSpec->getContextParams());
        $this->assertSame('resource', \gettype($resourceSpec->createStreamContext()));

        $this->assertSame('raw', RawResourceSpec::getResourceType());
        $this->assertInstanceOf(RawResourceSpec::class, $resourceSpec = RawResourceSpec::factory('zip://hoge.zip#fuga.csv'));
        $this->assertSame('raw', (string) $resourceSpec);
        $this->assertSame('zip://hoge.zip#fuga.csv', $resourceSpec->build());
        $this->assertSame([], $resourceSpec->getContextOptions());
        $this->assertSame([], $resourceSpec->getContextParams());
        $this->assertSame('resource', \gettype($resourceSpec->createStreamContext()));

        $this->assertSame('zip://', ZipResourceSpec::getResourceType());
        $this->assertInstanceOf(ZipResourceSpec::class, $resourceSpec = ZipResourceSpec::factory('hoge.zip', 'fuga.csv'));
        $this->assertSame('zip://', (string) $resourceSpec);
        $this->assertSame('zip://hoge.zip#fuga.csv', $resourceSpec->build());
        $this->assertSame([], $resourceSpec->getContextOptions());
        $this->assertSame([], $resourceSpec->getContextParams());
        $this->assertSame('resource', \gettype($resourceSpec->createStreamContext()));

        $resourceSpec = ZipResourceSpec::factory(new \SplFileInfo('hoge.zip'), 'fuga.csv');
        $this->assertSame('zip://', (string) $resourceSpec);
        $this->assertSame('zip://hoge.zip#fuga.csv', $resourceSpec->build());
        $this->assertSame([], $resourceSpec->getContextOptions());
        $this->assertSame([], $resourceSpec->getContextParams());
        $this->assertSame('resource', \gettype($resourceSpec->createStreamContext()));

        $resourceSpec = ZipResourceSpec::factory('hoge.zip', 'fuga.csv');
        $resourceSpec->setPassword('asdf');
        $this->assertSame('zip://', (string) $resourceSpec);
        $this->assertSame('zip://hoge.zip#fuga.csv', $resourceSpec->build());
        $this->assertSame([
            'zip'   => [
                'password' => 'asdf',
            ],
        ], $resourceSpec->getContextOptions());
        $this->assertSame([], $resourceSpec->getContextParams());
        $this->assertSame('resource', \gettype($resourceSpec->createStreamContext()));
    }
}
