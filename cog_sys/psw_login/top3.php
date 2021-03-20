<?php
//遠隔地会員専用保護者用
require_once('auth.php');
ob_start();
session_start();
require_once('../common_function.php');


$id=$_SESSION['auth']['id'];
$mcode=$_SESSION['auth']['mcode'];
$mname=$_SESSION['auth']['mname'];
$role=$_SESSION['auth']['role'];
// var_dump($mcode);
// var_dump($role);


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
  <h2 style="font-size:20px"><?php echo 'MCD:' . ' ' . $mcode . ' ' . $mname . ' さんのページ' . PHP_EOL;?></h2>
  </div>
  <div id="wrap">
  <div id="content">
    <hr>
      <dl>
      <dt>会員データ</dt>
      <dd><button ><a href="../buf/monitor_kaiin_in.php" style="text-decoration:none;">新規ファミリー会員入力</a></button></dd>
      <dd><button><a href="../monitor/monitor_kaiin_list.php" style="text-decoration:none;">ファミリー会員一覧</a></button></dd>
      <button><a href="./my_data_update.php" style="text-decoration:none;">マイデータ</a></button>
    </dl>
<br><hr>
<dl>
<dt>注文入力</dt>

  <p><button><a href="./page_fee_en.php" style="text-decoration:none;">注文・請求関係(のみ・その他注文・注文履歴・締日指定請求明細）</a></button></p>
  <button><a href="./page_fee_en.php" style="text-decoration:none;">請求関係(締日指定請求金額・入金履歴・退会)</a></button>

  <br><hr>
  <dl>
    <dt style="margin-top:30px">定期</dt>
      <p><button><a href="../teiki/teiki_out.php" style="text-decoration:none;">定期契約商品明細</a></button></p>
      <br>
  </dl>
  <dl><dt>その他</dt>
  </dl>
  <p><button><a href="./page_fee_en.php" style="text-decoration:none;">退会</a></button></p><br>
  <p><button><a href="./info_kaiin.php" style="text-decoration:none;">お知らせ</a></button></p><br>
  <hr>
  <p><button><a href="./logout.php" style="text-decoration:none;">ログアウト</a></button></p>
  </div>

  

  </div>

</body>
</html>
