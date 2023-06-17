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

$root_dir  = \dirname(__DIR__);

return PhpCsFixer\Finder::create()
->in($root_dir)         // 読み込み対象ディレクトリ
->notPath(\array_merge( // 除外対象ファイルパス
    include \sprintf('%s/.php-cs-fixer/.php-cs-fixer.not_path.php', $root_dir),
))
->exclude(\array_merge( // 除外対象ディレクトリ
    include \sprintf('%s/.php-cs-fixer/.php-cs-fixer.exclude.php', $root_dir),
));
