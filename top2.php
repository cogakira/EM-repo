<?php
//モニター管理による会員　管理によるモニター番号必須 遠隔地会員
// 認証処理のinclude
require_once('auth.php');
ob_start();
session_start();

$id=$_SESSION['auth']['id'];

$dbh = get_dbh();
$sqlhogo = 'select * from mdata where id = :id;';
$hogo = $dbh->prepare($sqlhogo);
$hogo->bindValue(':id',$id,PDO::PARAM_INT);
$rhogo = $hogo->execute();
$hogolst=$hogo->fetch();
$_SESSION['auth']['mname'] = $hogolst['mname'];
$mcode=$_SESSION['auth']['mcode'];
$mname=$_SESSION['auth']['mname'];
$_SESSION['auth']['role'] = $hogolst['role'];
$role=$_SESSION['auth']['role'];


// var_dump($hogolst['mcode']);
// var_dump($hogolst['mname']);
// var_dump($mname);
// var_dump($hogolst['role']);
if($hogolst['mcode'] === 0){
  $error_mcode = 0;
}


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
  <h2 style="font-size:20px">
  <?php if($role === 2) :?>
  <?php echo 'ID:' . ' ' . $id . ' ' . $mname . ' さんのページ' . PHP_EOL;?>
  <?php else: ?>
  <?php echo $mcode . ' ' . $mname . ' さんのページ' . PHP_EOL;?>
  <?php endif; ?>
  </h2>
  </div>
  <br>
  <div id="wrap">
    <div id="content">
  <button><a href="./my_data_update.php" style="text-decoration:none;">マイデータアップデート</a></button>
  <?php if($error_mcode === 0): ?>
  <button><a href="../monitor/enkaku_kodoku.php" style="text-decoration:none;">月刊誌を購読する(18歳未満の方は登録できません。)</a></button>
  <!-- <button><a href="../buf/monitor_kaiin_in.php" style="text-decoration:none;">月刊誌を購読する(18歳未満の方は登録できません。)</a></button> -->
    <?php endif; ?>
<br>
  <!-- <button><a href="./page_fee_en2.php" style="text-decoration:none;">注文2Page</a></button><br> -->
  <button style="margin-top:20px;"><a href="./logout.php" style="text-decoration:none;">ログアウト</a></button>
  </div>
  </div>

</body>
</html>
