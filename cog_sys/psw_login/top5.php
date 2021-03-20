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
    <div>

      <p>会員データ</p>
      <button><a href="../buf/monitor_kaiin_in.php" style="text-decoration:none;">会員入力</a></button>
      <button><a href="../monitor/monitor_kaiin_list.php" style="text-decoration:none;">会員一覧</a></button>
    </div>
  <hr>
  <button style="margin-top:10px;"><a href="./my_data_update.php" style="text-decoration:none;">マイデータ</a></button>
<br>
<hr>
<div>
<p style="margin-top:10px;">注文</p>

  <button><a href="./page_fee_en.php" style="text-decoration:none;">注文・請求関係(のみ・その他注文・注文履歴・締日指定請求明細）</a></button><br>
  <button style="margin-top:20px;"><a href="./page_fee_en.php" style="text-decoration:none;">請求関係(締日指定請求金額・入金履歴・退会)</a></button><br>
</div> 
 <hr>
  <p style="margin-top:10px;">定期</p>
      <button><a href="../teiki/teiki_out.php" style="text-decoration:none;">定期数</a></button>
      <br>
      <hr>
  <p style="margin-top:30px;">その他</p>
  <button><a href="./page_fee_en.php" style="text-decoration:none;">メンバー関係(退会)</a></button><br>
  <button style="margin-top:20px;"><a href="./info_monitor.php" style="text-decoration:none;">お知らせ</a></button><br>
  <button style="margin-top:20px;"><a href="./logout.php" style="text-decoration:none;">ログアウト</a></button>
  </div>

  

  </div>

</body>
</html>
