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

namespace fw3\streams\filters\utilitys\specs\entitys\resources;

use fw3\streams\filters\utilitys\specs\entitys\resources\traits\ResourceSpecInterface;
use fw3\streams\filters\utilitys\specs\entitys\resources\traits\ResourceSpecTrait;

/**
 * リソーススペックエンティティ：リソース文字列
 */
class RawResourceSpec implements ResourceSpecInterface
{
    use ResourceSpecTrait;

    /**
     * @var string リソースタイプ
     */
    public const RESOURCE_TYPE  = 'raw';

    /**
     * @var string リソース文字列
     */
    protected $resource;

    /**
     * リソースタイプを返します。
     *
     * @return string リソースタイプ
     */
    public static function getResourceType(): string
    {
        return self::RESOURCE_TYPE;
    }

    /**
     * factory
     *
     * @return self このインスタンス
     */
    public static function factory($resource): self
    {
        return new self($resource);
    }

    /**
     * constructor
     */
    protected function __construct($resource)
    {
        $this->resource = $resource;
    }

    /**
     * リソース文字列を構築して返します。
     *
     * @return string リソース文字列
     */
    public function build(): string
    {
        return $this->resource;
    }
}
