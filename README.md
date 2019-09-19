# Flywheel3 Stream library

Rapid Development FrameworkであるFlywheel3 のストリーム処理ライブラリです。

対象となるPHPのバージョンは7.2.0以上です。

## 導入方法

`composer require fw3/streams`としてインストールできます。

[Packagist](https://packagist.org/packages/fw3/streams)

## 主な機能

### Stream Filter

#### エンコーディング変換ストリームフィルタ：ConvertEncodingFiltermd

ファイルへの入出力時にエンコーディングの変換を行うフィルタです。

組み込みのロカール変更処理と代替文字設定処理を利用する事で確実かつ、期待通りの出力を得る事ができるようになります。

```php
<?php

use fw3\streams\filters\ConvertEncodingFilter;

// 必須：実行時のロカールと代替文字設定を先行して設定します
ConvertEncodingFilter::startChangeLocale();

// お好みで：変換不能文字があった場合の代替文字設定
ConvertEncodingFilter::startChangeSubstituteCharacter();

// フィルタ登録
\stream_filter_register('convert.encoding.*', ConvertEncodingFilter::class);

//==============================================
// 書き込み
//==============================================
// エンコーディングの設定
$from_encoding  = 'UTF-8';
$to_encoding    = 'SJIS-win';

// フィルタの設定
$spec   = \sprintf('php://filter/write=encoding.%s:%s/resource=%s', $to_encoding, $from_encoding, $path_to_csv_file);

// CP932としてCSV書き込みを行う（\SplFileObjectでも使用できます。）
$fp     = \fopen($spec, 'wb');
foreach ($rows as $row) {
    \fputcsv($fp, $row);
}
\fclose($fp);

//==============================================
// 読み込み
//==============================================
// エンコーディングの設定
$from_encoding  = 'SJIS-win';
$to_encoding    = 'UTF-8';

// フィルタの設定
$spec   = \sprintf('php://filter/read=encoding.%s:%s/resource=%s', $to_encoding, $from_encoding, $path_to_csv_file);

// UTF-8としてCSV読み込みを行う（\SplFileObjectでも使用できます。）
$rows   = [];
$fp     = \fopen($spec, 'rb');
for (;($row = \fgetcsv($fp, 1024)) !== FALSE;$rows[] = $row);
\fclose($fp);

// ロカールと代替文字設定を元に戻します
ConvertEncodingFilter::endChangeSubstituteCharacter();
ConvertEncodingFilter::endChangeLocale();
```

#### 行末改行コード変換ストリームフィルタ：ConvertLienFeedFiltermd

ファイルへの入出力時に**行末の**改行コードの変換を行うフィルタです。

システムやシステムの設定によらず、fgetcsvなどの処理で期待通りの改行コードを行末に付与する事ができます。

```php
<?php

use fw3\streams\filters\ConvertLienFeedFilter;

// フィルタ登録
\stream_filter_register('line_feed.*', ConvertLienFeedFilter::class);

//==============================================
// 書き込み
//==============================================
// 改行コードの設定：いかなる改行コードもCRLFにして出力する
$from_linefeed  = ConvertLienFeedFilter::STR_ALL;
$to_linefeed    = ConvertLienFeedFilter::STR_CRLF;

// フィルタの設定
$spec   = \sprintf('php://filter/write=line_feed.%s:%s/resource=%s', $to_linefeed, $from_linefeed, $path_to_csv_file);

// 行末の改行コードをCRLFとしてCSV書き込みを行う（\SplFileObjectでも使用できます。）
$fp     = \fopen($spec, 'wb');
foreach ($rows as $row) {
    \fputcsv($fp, $row);
}
\fclose($fp);
```

#### 応用：無難なCSV入出力

次のようにするとエクセルでも無難に読み込めるCSVファイルの入出力を行えるようになります。

```php
<?php

use fw3\streams\filters\ConvertEncodingFilter;
use fw3\streams\filters\ConvertLienFeedFilter;

//==============================================
// 設定
//==============================================
$path_to_csv    = '';   // CSVファイルのパスを設定して下さい

$system_encoding    = 'UTF-8';
$file_encoding      = 'SJIS-win';

//----------------------------------------------
// 必須：実行時のロカールと代替文字設定を先行して設定します
//----------------------------------------------
// Windowsや一部Unix系OS環境下ではシステムデフォルトのロカールを使用すると、fgetcsv関数が期待した通りに動作しなくなります
// 引数なしでConvertEncodingFilter::startChangeLocale();を実行した場合、OSやPHPのバージョンを加味した上で無難なロカール設定を行います
// 引数を与える事で任意のロカールに設定する事も出来ます
// 設定前のロカールはスタックされるため、「ConvertEncodingFilter::endChangeLocale();」で元に戻すことができます。
//----------------------------------------------
ConvertEncodingFilter::startChangeLocale();

//----------------------------------------------
// お好みで：変換不能文字があった場合の代替文字設定
//----------------------------------------------
// これは「艗」などのエンコーディング変換ができない文字を代替する文字の設定です
// デフォルトではエンティティ(&#x8257;)に変換されます
// 引数を与える事で固定の代替文字やユニコード(U+8257)、または消去するなどの挙動の変更が可能です
// 設定前の代替文字はスタックされるため、「ConvertEncodingFilter::endChangeSubstituteCharacter();」で元に戻すことができます
//----------------------------------------------
ConvertEncodingFilter::startChangeSubstituteCharacter();

//----------------------------------------------
// フィルタ登録
//----------------------------------------------
// フィルタ名は末尾が「.*」となっていればいかなる名前でも設定できます
//----------------------------------------------
\stream_filter_register('convert.encoding.*', ConvertEncodingFilter::class);
\stream_filter_register('line_feed.*', ConvertLienFeedFilter::class);

//==============================================
// 書き込み
//==============================================
// エンコーディングの設定
$to_encoding    = $file_encoding;
$from_encoding  = $system_encoding;

// 改行コードの設定：いかなる改行コードもCRLFにして出力する
$to_linefeed    = ConvertLienFeedFilter::STR_CRLF;
$from_linefeed  = ConvertLienFeedFilter::STR_ALL;

// フィルタの設定
$spec   = \sprintf(
    'php://filter/write=encoding.%s:%s%2Fline_feed.%s:%s/resource=%s',
    $to_encoding,
    $from_encoding,
    $to_linefeed,
    $from_linefeed,
    $path_to_csv_file
);

// CP932、行末の改行コードCRLFとしてCSV書き込みを行う（\SplFileObjectでも使用できます。）
$fp     = \fopen($spec, 'wb');
foreach ($rows as $row) {
    \fputcsv($fp, $row);
}
\fclose($fp);

//==============================================
// 読み込み
//==============================================
// エンコーディングの設定
$to_encoding    = $system_encoding;
$from_encoding  = $file_encoding;

// フィルタの設定
$spec   = \sprintf(
    'php://filter/read=encoding.%s:%s/resource=%s',
    $to_encoding,
    $from_encoding,
    $path_to_csv_file
);

// UTF-8としてCSV読み込みを行う（\SplFileObjectでも使用できます。）
$rows   = [];
$fp     = \fopen($spec, 'rb');
for (;($row = \fgetcsv($fp, 1024)) !== FALSE;$rows[] = $row);
\fclose($fp);

// ロカールと代替文字設定を元に戻します
ConvertEncodingFilter::endChangeSubstituteCharacter();
ConvertEncodingFilter::endChangeLocale();
```
