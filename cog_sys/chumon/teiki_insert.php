<?php
  session_start();
  require_once('../common_function.php');
  date_default_timezone_set('Asia/Tokyo');
      
      $mcode = $_SESSION['auth']['mcode'];
      $role = $_SESSION['auth']['role'];
      $hjunkyocd=$_SESSION['teiki']['hjunkyocd'];
      $shohincd=$_SESSION['teiki']['shohincd'];
      $rackno=$_SESSION['teiki']['rackno'];
      $keikucd = 0;
      $user_id=$_SESSION['user']['id'];



$dbh = get_dbh();
      $sql = 'INSERT INTO teiki (id,mcode,hjunkyocd,rackno,shohincd,keikucd)
             VALUES (:id,:mcode,:hjunkyocd,:rackno,:shohincd,:keikucd);';
$pre = $dbh->prepare($sql);
// 値のバインド
$pre->bindValue(':id', $user_id, PDO::PARAM_INT);
$pre->bindValue(':mcode', $mcode, PDO::PARAM_INT);
$pre->bindValue(':shohincd', $shohincd, PDO::PARAM_INT);
$pre->bindValue(':hjunkyocd', $hjunkyocd, PDO::PARAM_INT);
$pre->bindValue(':rackno', $rackno, PDO::PARAM_INT);
$pre->bindValue(':keikucd', $keikucd, PDO::PARAM_STR);
// $pre->bindValue(':modified', date(DATE_ATOM), PDO::PARAM_STR);

// SQLの実行
$r = $pre->execute();
if (false === $r) {
        // XXX 本当はもう少し丁寧なエラーページを出力する
        echo '条件に合うデータがありません。注文画面に戻ります。';
        header('Location:../psw_login/top5.php');
        echo 'システムでエラーが起きました';
        // exit('motoni');
      //   header('Location:chumon.php');
// var_dump($pre);
// var_dump($mcode);
// var_dump($shohincd);
// var_dump($hjunkyocd);
// var_dump($rackno);
// var_dump($keikucd);


      }
      // header('Location:chumon.php');

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<link rel="stylesheet" href="../styles/style.css" />
  <title>チェック</title>
  <style type="text/css">
    .error { color: red; }
  </style>
</head>

<body>
<div id="wrap">
        <div id="head">
        <p>入力完了しました。</p><br>
        </div>
        <br>
  
        <?php if($role < 4) : ?>
        <button style="margin-left:30px;"><a style="text-decoration:none;font-size:20px;" href="../psw_login/top3.php">マイページに戻る</a></button>
        <?php elseif($role === 4 ) : ?>
        <button style="margin-left:30px;"><a style="text-decoration:none;font-size:20px;" href="../psw_login/top4.php">マイページに戻る</a></button>
        <?php else : ?>
        <button style="margin-left:30px;"><a style="text-decoration:none;font-size:20px;" href="../psw_login/top5.php">マイページに戻る</a></button>
        <?php endif; ?>
</div>
</body>
</html>
