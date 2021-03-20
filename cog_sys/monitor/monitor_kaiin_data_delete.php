<?php

/*
 * (管理画面想定)１件のform情報の削除
 */
// セッションの開始
error_reporting(E_ALL & ~E_NOTICE);

ob_start();
session_start();
$role=$_SESSION['auth']['role'];

// 共通関数のinclude
require_once('../common_function.php');

// XXX 管理画面であれば、本来はこのあたり(ないしもっと手前)で認証処理を行う

// パラメタを受け取る
// XXX エラーチェックは get_test_form() 関数側でやっているのでここではオミット
$id = (string)@$_POST['id'];
// 確認
// var_dump($id);
// CSRFチェック
if (false === is_csrf_token_admin()) {
    // 「CSRFトークンエラー」であることをセッションに格納しておく
    $_SESSION['output_buffer']["error_csrf"]  = true;

    // 編集ページに遷移する
    header('Location: ./monitor_kaiin_list.php');
    exit;
}
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
$sqldel = 'DELETE FROM teiki WHERE id = :id;';
$predel = $dbh->prepare($sqldel);

// 値のバインド
$predel->bindValue(':id', $id, PDO::PARAM_INT);

// SQLの実行
$rdel = $predel->execute();
if (false === $rdel) {
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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<link rel="stylesheet" href="../styles/style.css" />
  <title>home</title>
  <style type="text/css">
    .error { color: red; }
  </style>
</head>
<body>
<div id="head">
  <h1>削除しました。</h1>
</div>
<hr>
  <div id="wrap">
  <div id="contetnt">
  <button><a href="./monitor_kaiin_list.php" style="text-decoration:none;font-size:20px;">会員一覧に戻る</a></button><br>
  
          <div style="margin-top:20px">
  <button style="color:white;font-size:15px;text-decoration:none;">
          <?php if($role === 2) : ?>
          <a style="text-decoration:none;" href="../psw_login/top2.php">
          <?php elseif($role === 3) : ?>
          <a style="text-decoration:none;" href="../psw_login/top3.php">
          <?php elseif($role === 4) :?>
          <a style="text-decoration:none;" href="../psw_login/top4.php">
          <?php else : ?>
          <a style="text-decoration:none;" href="../psw_login/top5.php">
          <?php endif; ?>
          マイページに戻る</a></button>
          </div>
  
  </div>
</body>
</html>
