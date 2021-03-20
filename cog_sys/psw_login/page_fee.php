<?php
session_start();

// 認証処理のinclude
require_once('auth_fee.php');

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
  <title>注文</title>
  <style type="text/css">
    .error { color: red; }
  </style>
</head>

<body>
<div id="wrap">
    <div id="head">
    <p style="font-size:20px"><?PHP print $mcode . ": " .$mname . " 様"; ?> </p>

        <h1>注文・請求関係</h1>
    </div>
    <div style="text-align:right">
<button style="color:white;font-size:15px;text-decoration:none;"><a style="text-decoration:none;" href="../psw_login/top4.php">マイページに戻る</a></button>
    </div>
  <div id="content">
  <div>
    <p><button><a style="text-decoration:none;" href="../chumon/chumon.php">注文</a></button></p>
  </div>
  <div>
    <p><button><a style="text-decoration:none;" href="../chumon/ad_uri_list.php">注文履歴</a></button></p>
  </div>
  <div>
    <p><button><a style="text-decoration:none;" href="../chumon/ad_uri_list_mi.php">注文未出庫分</a></button></p>
  </div>
  <br>
  <div>
    <p>締日指定請求明細</p>
    <table>
      <tr>
        <td>
    <select name = "shimebi" form="hinichi">
    <?php for($i=2;$i>=-4;$i--): ?>
            <option value=<?php echo(date('Y-m-d', mktime(0, 0, 0, date('m')   +   ${i}  , 0, date('Y')))); ?>><?php echo(date('Y-m-d', mktime(0, 0, 0, date('m')   +   ${i}  , 0, date('Y')))); ?> </option>
    <?php endfor;   ?>
    </select>
    <form action="../chumon/ad_uri_list_shime.php" method=post id="hinichi">
    </td>
<td>
<input type="submit" value="表示">
    </table>
</form>
    </td>
  </div>
  </div>
</div>

</body>
</html>
