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
 * リソーススペックエンティティ：Zip
 */
class ZipResourceSpec implements ResourceSpecInterface
{
    use ResourceSpecTrait;

    /**
     * @var string リソースタイプ
     */
    public const RESOURCE_TYPE  = 'zip://';

    /**
     * @var string|\SplFileInfo ZIPファイルパス
     */
    protected $zipFilePath;

    /**
     * @var string ZIPファイル内ファイルパス
     */
    protected string $pathInArchive;

    /**
     * @var null|string|\Stringable 暗号化解除パスワード
     */
    protected $password;

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
     * @param  string|\SplFileInfo $zip_file_path   ZIPファイルパス
     * @param  string              $path_in_archive ZIPファイル内ファイルパス
     * @return self                このインスタンス
     */
    public static function factory($zip_file_path, string $path_in_archive): self
    {
        return new self($zip_file_path, $path_in_archive);
    }

    /**
     * constructor
     *
     * @param string|\SplFileInfo $zip_file_path   ZIPファイルパス
     * @param string              $path_in_archive ZIPファイル内ファイルパス
     */
    protected function __construct($zip_file_path, string $path_in_archive)
    {
        $this->zipFilePath      = $zip_file_path;
        $this->pathInArchive    = $path_in_archive;
    }

    /**
     * 暗号化解除パスワードを設定します。
     *
     * @param  null|string|\Stringable $password 暗号化解除パスワード
     * @return self                    このインスタンス
     */
    public function setPassword($password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * リソース文字列を構築して返します。
     *
     * @return string リソース文字列
     */
    public function build(): string
    {
        return \sprintf(
            '%s%s#%s',
            self::RESOURCE_TYPE,
            $this->zipFilePath instanceof \SplFileInfo ? $this->zipFilePath->getPathname() : $this->zipFilePath,
            $this->pathInArchive,
        );
    }

    /**
     * コンテキストオプションを返します。
     *
     * @return array コンテキストオプション
     */
    public function getContextOptions(): array
    {
        if ($this->password !== null) {
            return [
                'zip' => [
                    'password' => (string) $this->password,
                ],
            ];
        }

        return [];
    }
}
