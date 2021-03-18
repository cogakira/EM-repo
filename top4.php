<?php
//一般モニター専用　会員管理と注文管理
// 認証処理のinclude
require_once('auth.php');
ob_start();
session_start();

$id=$_SESSION['auth']['id'];
$mcode=$_SESSION['auth']['mcode'];
$mname=$_SESSION['auth']['mname'];
$role=$_SESSION['auth']['role'];

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
  <h1>マイページ</h1>
  <div id="head">
  <h2 style="font-size:20px"><?php echo $mcode . ' ' . $mname . ' さんのページ' . PHP_EOL;?></h2>
  </div>
  <div id="wrap">
  <div id="content">
    <hr>
      <p>会員データ入力</p>
      <p>&raquo;<a href="../buf/monitor_kaiin_in.php">会員入力</a></p>
      <p>&raquo;<a href="../monitor/monitor_kaiin_list.php">会員一覧</a></p>
  <br>
  <p><a href="./my_data_update.php">マイデータ</a></p>
<br>
<p>注文入力</p>

  <p><a href="./page_fee_en.php">注文・請求関係(のみ・その他注文・注文履歴・締日指定請求明細）</a></p><br>
  <p><a href="./page_fee_en.php">請求関係(締日指定請求金額・入金履歴・退会)</a></p><br>
  <p>定期</p>
      <p><a href="../teiki/teiki_out.php">定期数</a></p>
      <br>
  <p>その他</p>
  <p><a href="./page_fee_en.php">メンバー関係(退会)</a></p><br>
  <p><a href="./info_monitor.php">お知らせ</a></p><br>
  <p><a href="./logout.php">ログアウト</a></p>
  </div>

  

  </div>

</body>
</html>
