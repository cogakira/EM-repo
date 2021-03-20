<?php

/*
 * (管理画面想定)１件のform情報の削除
 */
// セッションの開始
ob_start();
session_start();

// 共通関数のinclude
require_once('../common_function.php');

$id=$_SESSION['auth']['id'];

// 確認
// DBハンドルの取得
$dbh = get_dbh();

// INSERT文の作成と発行
// ------------------------------
// 準備された文(プリペアドステートメント)の用意
$sql = 'DELETE FROM mdata WHERE id = :id;';
$pre = $dbh->prepare($sql);

// 値のバインド
$pre->bindValue(':id', $id, PDO::PARAM_INT);

// SQLの実行
$r = $pre->execute();
if (false === $r) {
    // XXX 本当はもう少し丁寧なエラーページを出力する
    echo 'システムでエラーが起きました';
    exit;
}

// 正常に終了したので、セッション内の「出力用情報」を削除する
unset($_SESSION['output_buffer']);

?>
<!DOCTYPE HTML>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title style='text-align:center'>退会（削除)</title>
  <style type="text/css">
    .error { color: red; }
  </style>
</head>

<body>
  削除しました。<br>
  <br>
  <a href="https://www.cogoc.net">ホームへ</a>
</body>
</html>
