<?php
//仮会員
// 認証処理のinclude
require_once('auth.php');
ob_start();
session_start();

$id=$_SESSION['auth']['id'];
$mname=$_SESSION['auth']['mname'];
$role=$_SESSION['auth']['role'];

?>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<link rel="stylesheet" href="style.css" />
  <title>login</title>
  <style type="text/css">
    .error { color: red; }
  </style>
</head>

<body>
<body>
<div id="wrap">
  <div id="head">
  <h1>仮会員</h1>
  </div> 
  <div id="content">
  <?php echo $mname . ' さんのページ' . PHP_EOL;?>
  <br>
  <a href="./my_data_kai.php">ニックネーム,メールアドレス確認</a>
<br>
  <!-- <a href="./page_fee_en.php">有料Page</a><br> -->
  <a href="../mente/tai.php">退会</a>
<br>
  <a href="./logout.php">ログアウト</a>
</div>
</div>
</body>
</html>
