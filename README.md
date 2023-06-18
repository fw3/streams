# Flywheel3 Stream library

Rapid Development FrameworkであるFlywheel3 のストリーム処理ライブラリです。

対象となるPHPのバージョンは7.2.0以上です。

過去バージョンであるPHP 5.3.3以上の環境では [fw3-for-old/streams](https://github.com/fw3-for-old/streams) を使用してください。

お手軽簡単、今すぐに利用したい方は [導入方法](#導入方法) を参照し、ライブラリを導入後、 [応用：初期化設定もライブラリに任せた実装](#応用：初期化設定もライブラリに任せた実装) にある実装を試してみてください。


## 導入方法

`composer require fw3/streams`としてインストールできます。

[Packagist](https://packagist.org/packages/fw3/streams)

## 主な機能

### Stream Filter

#### エンコーディング変換ストリームフィルタ：ConvertEncodingFilter

ファイルへの入出力時にエンコーディングの変換を行うフィルタです。

組み込みのロカール変更処理と代替文字設定処理を利用する事で確実かつ、期待通りの出力を得る事ができるようになります。

また同梱のSpecクラス群を用いることで直感的かつ簡単、安全に設定を行うことができます。

```php
<?php

use fw3\streams\filters\ConvertEncodingFilter;
use fw3\streams\filters\utilitys\StreamFilterSpec;
use fw3\streams\filters\utilitys\specs\StreamFilterConvertEncodingSpec;

// 必須：実行時のロカールと代替文字設定を先行して設定します
ConvertEncodingFilter::startChangeLocale();

// お好みで：変換不能文字があった場合の代替文字設定
ConvertEncodingFilter::startChangeSubstituteCharacter();

// `convert.encoding.*`としてフィルタ登録
StreamFilterSpec::registerConvertEncodingFilter();

//==============================================
// 書き込み
//==============================================
// フィルタの設定：`php://filter/write=convert.encoding.SJIS-win:UTF-8/resource=path to csv file`として構築
$spec   = StreamFilterSpec::resource($path_to_csv_file)->write([
    StreamFilterConvertEncodingSpec::toSjisWin()->fromUtf8(),
]);

// CP932としてCSV書き込みを行う（\SplFileObjectでも使用できます。）
$fp     = \fopen($spec->build(), 'wb');
foreach ($rows as $row) {
    \fputcsv($fp, $row);
}
\fclose($fp);

//==============================================
// 読み込み
//==============================================
// フィルタの設定：`php://filter/read=convert.encoding.UTF-8:SJIS-win/resource=path to csv file`として構築
$spec   = StreamFilterSpec::resource($path_to_csv_file)->read([
    StreamFilterConvertEncodingSpec::fromUtf8()->toSjisWin(),
]);

// UTF-8としてCSV読み込みを行う（\SplFileObjectでも使用できます。）
$rows   = [];
$fp     = \fopen($spec->build(), 'rb');
for (;($row = \fgetcsv($fp, 1024)) !== FALSE;$rows[] = $row);
\fclose($fp);

// ロカールと代替文字設定を元に戻します
ConvertEncodingFilter::endChangeSubstituteCharacter();
ConvertEncodingFilter::endChangeLocale();
```

#### 行末改行コード変換ストリームフィルタ：ConvertLinefeedFilter

ファイルへの入出力時に**行末の**改行コードの変換を行うフィルタです。

システムやシステムの設定によらず、fgetcsvなどの処理で期待通りの改行コードを行末に付与する事ができます。

```php
<?php

use fw3\streams\filters\utilitys\StreamFilterSpec;
use fw3\streams\filters\utilitys\specs\StreamFilterConvertLinefeedSpec;

// `convert.linefeed.*`としてフィルタ登録
StreamFilterSpec::registerConvertLinefeedFilter();

//==============================================
// 書き込み
//==============================================
// フィルタの設定：`php://filter/write=convert.linefeed.CRLF:ALL/resource=path to csv file`として構築
$spec   = StreamFilterSpec::resource($path_to_csv_file)->write([
    StreamFilterConvertLinefeedSpec::toCrLf()->fromAll(),
]);

// 行末の改行コードをCRLFとしてCSV書き込みを行う（\SplFileObjectでも使用できます。）
$fp     = \fopen($spec->build(), 'wb');
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
use fw3\streams\filters\utilitys\StreamFilterSpec;
use fw3\streams\filters\utilitys\specs\StreamFilterConvertEncodingSpec;
use fw3\streams\filters\utilitys\specs\StreamFilterConvertLinefeedSpec;

//==============================================
// 設定
//==============================================
$path_to_csv    = '';   // CSVファイルのパスを設定して下さい

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
// 引数を使用することでお好きなフィルタ名を設定することができます。
//
// StreamFilterSpec::registerConvertEncodingFilter(StreamFilterConvertEncodingSpec::DEFAULT_FILTER_NAME);
// StreamFilterSpec::registerConvertLinefeedFilter(StreamFilterConvertLinefeedSpec::DEFAULT_FILTER_NAME);
//----------------------------------------------
StreamFilterSpec::registerConvertEncodingFilter();
StreamFilterSpec::registerConvertLinefeedFilter();

//==============================================
// 書き込み
//==============================================
// フィルタの設定
$spec   = StreamFilterSpec::resource($path_to_csv)->write([
    StreamFilterConvertEncodingSpec::toSjisWin()->fromUtf8(),
    StreamFilterConvertLinefeedSpec::toCrLf()->fromAll(),
]);

// CP932、行末の改行コードCRLFとしてCSV書き込みを行う（\SplFileObjectでも使用できます。）
$fp     = \fopen($spec->build(), 'wb');
foreach ($rows as $row) {
    \fputcsv($fp, $row);
}
\fclose($fp);

//==============================================
// 読み込み
//==============================================
// フィルタの設定
$spec   = StreamFilterSpec::resource($path_to_csv)->read([
    StreamFilterConvertEncodingSpec::toUtf8()->fromSjisWin(),
]);

// UTF-8としてCSV読み込みを行う（\SplFileObjectでも使用できます。）
$rows   = [];
$fp     = \fopen($spec->build(), 'rb');
for (;($row = \fgetcsv($fp, 1024)) !== FALSE;$rows[] = $row);
\fclose($fp);

//==============================================
// ロカールと代替文字設定を元に戻します
//==============================================
ConvertEncodingFilter::endChangeSubstituteCharacter();
ConvertEncodingFilter::endChangeLocale();
```

#### 応用：HTTP経由でのCSVダウンロード

次のようにするとエクセルでも無難に読み込めるCSVファイルのダウンロードを容易に実現できます。

```
<?php

use fw3\streams\filters\ConvertEncodingFilter;
use fw3\streams\filters\utilitys\StreamFilterSpec;
use fw3\streams\filters\utilitys\specs\StreamFilterConvertEncodingSpec;
use fw3\streams\filters\utilitys\specs\StreamFilterConvertLinefeedSpec;

//==============================================
// 設定
//==============================================
// 必須：実行時のロカールと代替文字設定を先行して設定します
ConvertEncodingFilter::startChangeLocale();

// お好みで：変換不能文字があった場合の代替文字設定
ConvertEncodingFilter::startChangeSubstituteCharacter();

// フィルタ登録
StreamFilterSpec::registerConvertEncodingFilter();
StreamFilterSpec::registerConvertLinefeedFilter();

//==============================================
// 例：PDOで取得したデータをそのままCSVとしてDLさせてみる
//==============================================
// フィルタの設定
$spec   = StreamFilterSpec::resourceOutput()->write([
    StreamFilterConvertEncodingSpec::toSjisWin()->fromUtf8(),
    StreamFilterConvertLinefeedSpec::toCrLf()->fromAll(),
]);

// 仮のDB処理：実際のDB処理に置き換えてください
$pdo    = new \PDO('spec to dsn');
$pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
$stmt   = $pdo->prepare('SELECT * FROM table');
$stmt->execute();

// 仮のHTTP Response Header
\header('Content-Type: application/octet-stream');
\header('Content-Disposition: attachment; filename=fw3-sample.csv');

// CP932、行末の改行コードCRLFとしてCSV書き込みを行う（\SplFileObjectでも使用できます。）
$fp     = \fopen($spec->build(), 'wb');
foreach ($stmt as $row) {
    \fputcsv($fp, $row);
}
\fclose($fp);

//==============================================
// ロカールと代替文字設定を元に戻します
//==============================================
ConvertEncodingFilter::endChangeSubstituteCharacter();
ConvertEncodingFilter::endChangeLocale();
```

#### 応用：初期化設定もライブラリに任せた実装

フィルタ登録やロカールと代替文字の設定と実行後のリストアなど、ボイラープレートとなりがちな処理をライブラリに任せて実行することもできます。

##### 無難なCSV入出力

```php
<?php

use fw3\streams\filters\utilitys\StreamFilterSpec;
use fw3\streams\filters\utilitys\specs\StreamFilterConvertEncodingSpec;
use fw3\streams\filters\utilitys\specs\StreamFilterConvertLinefeedSpec;

//==============================================
// 設定
//==============================================
$rows   = [[]]; // データ

$path_to_csv    = '';   // CSVファイルのパスを設定して下さい

//----------------------------------------------
// 一括即時実行
//----------------------------------------------
// フィルタ登録、ロカールと代替文字の設定と実行後のリストアも包括して実行します。
// コールバックの実行中に例外が発生してもロカールと代替文字のリストアは実行されます。
//----------------------------------------------
$result = StreamFilterSpec::decorateForCsv(function () use ($path_to_csv, $rows) {
    //==============================================
    // 書き込み
    //==============================================
    // フィルタの設定
    $spec   = StreamFilterSpec::resource($path_to_csv)->write([
        StreamFilterConvertEncodingSpec::toSjisWin()->fromUtf8(),
        StreamFilterConvertLinefeedSpec::toCrLf()->fromAll(),
    ]);

    // CP932、行末の改行コードCRLFとしてCSV書き込みを行う（\SplFileObjectでも使用できます。）
    $fp     = \fopen($spec->build(), 'r+b');
    foreach ($rows as $row) {
        \fputcsv($fp, $row);
    }
    \fclose($fp);

    //==============================================
    // 読み込み
    //==============================================
    // フィルタの設定
    $spec   = StreamFilterSpec::resource($path_to_csv)->read([
        StreamFilterConvertEncodingSpec::toUtf8()->fromSjisWin(),
    ]);

    // UTF-8としてCSV読み込みを行う（\SplFileObjectでも使用できます。）
    $rows   = [];
    $fp     = \fopen($spec->build(), 'r+b');
    for (;($row = \fgetcsv($fp, 1024)) !== FALSE;$rows[] = $row);
    \fclose($fp);

    return $rows;
});
```


##### 無難なCSV入出力：`\SplFileObject`を利用した例

```php
<?php

use fw3\streams\filters\utilitys\StreamFilterSpec;
use fw3\streams\filters\utilitys\specs\StreamFilterConvertEncodingSpec;
use fw3\streams\filters\utilitys\specs\StreamFilterConvertLinefeedSpec;

//==============================================
// 設定
//==============================================
$rows   = [[]]; // データ

$path_to_csv    = '';   // CSVファイルのパスを設定して下さい

//----------------------------------------------
// 一括即時実行
//----------------------------------------------
// フィルタ登録、ロカールと代替文字の設定と実行後のリストアも包括して実行します。
// コールバックの実行中に例外が発生してもロカールと代替文字のリストアは実行されます。
//----------------------------------------------
$result = StreamFilterSpec::decorateForCsv(function () use ($path_to_csv, $rows) {
    //==============================================
    // 書き込み
    //==============================================
    // フィルタの設定
    $spec   = StreamFilterSpec::resource($path_to_csv)->write([
        StreamFilterConvertEncodingSpec::toSjisWin()->fromUtf8(),
        StreamFilterConvertLinefeedSpec::toCrLf()->fromAll(),
    ]);

    // CP932、行末の改行コードCRLFとしてCSV書き込みを行う
    $csvFile    = new \SplFileObject($spec->build(), 'r+b');
    foreach ($csvFile as $row) {
        $file->fputcsv($row);
    }

    //==============================================
    // 読み込み
    //==============================================
    // フィルタの設定
    $spec   = StreamFilterSpec::resource($path_to_csv)->read([
        StreamFilterConvertEncodingSpec::toUtf8()->fromSjisWin(),
    ]);

    // UTF-8としてCSV読み込みを行う（\SplFileObjectでも使用できます。）
    $rows       = [];
    $csvFile    = new \SplFileObject($spec->build(), 'r+b');
    $csvFile->setFlags(\SplFileObject::READ_CSV);
    foreach ($csvFile as $row) {
        $rows[] = $row;
    }

    return $rows;
});
```

##### HTTP経由でのCSVダウンロード

```php
<?php

use fw3\streams\filters\utilitys\StreamFilterSpec;
use fw3\streams\filters\utilitys\specs\StreamFilterConvertEncodingSpec;
use fw3\streams\filters\utilitys\specs\StreamFilterConvertLinefeedSpec;

//----------------------------------------------
// 一括即時実行
//----------------------------------------------
// フィルタ登録、ロカールと代替文字の設定と実行後のリストアも包括して実行します。
// コールバックの実行中に例外が発生してもロカールと代替文字のリストアは実行されます。
//----------------------------------------------
StreamFilterSpec::decorateForCsv(function () {
    //==============================================
    // 例：PDOで取得したデータをそのままCSVとしてDLさせてみる
    //==============================================
    // フィルタの設定
    $spec   = StreamFilterSpec::resourceOutput()->write([
        StreamFilterConvertEncodingSpec::toSjisWin()->fromUtf8(),
        StreamFilterConvertLinefeedSpec::toCrLf()->fromAll(),
    ]);

    // 仮のDB処理：実際のDB処理に置き換えてください
    $pdo    = new \PDO('spec to dsn');
    $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
    $stmt   = $pdo->prepare('SELECT * FROM table');
    $stmt->execute();

    // 仮のHTTP Response Header
    \header('Content-Type: application/octet-stream');
    \header('Content-Disposition: attachment; filename=fw3-sample.csv');

    // CP932、行末の改行コードCRLFとしてCSV書き込みを行う（\SplFileObjectでも使用できます。）
    $fp     = \fopen($spec->build(), 'wb');
    foreach ($stmt as $row) {
        \fputcsv($fp, $row);
    }
    \fclose($fp);
});
```
