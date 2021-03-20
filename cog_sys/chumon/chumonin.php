<?php
  session_start();
  require_once('../common_function.php');
  date_default_timezone_set('Asia/Tokyo');
      
      $mcode = $_SESSION['auth']['mcode'];
      $hjunkyocd=$_SESSION['chumon']['hjunkyocd'];
      $shohincd=$_SESSION['chumon']['shohincd'];
      $rackno=$_SESSION['chumon']['rackno'];
      $gou=$_SESSION['chumon']['gou'];
      $chumonsu=$_SESSION['chumon']['chumonsu'];
      $teika=$_SESSION['chumon']['teika'];
      $orei=$_SESSION['chumon']['orei'];
      $seikyu=$_SESSION['chumon']['seikyu'];
      $shimebi=$_SESSION['chumon']['shimebi'];
      $chumonbi = date('Y-m-d');
      $shokucd = $_SESSION['shokucd'];
      $role=$_SESSION['auth']['role'];

      $kubuncd = 2;
      // $_SESSION['chumon']['shimebi']=date('Y-m-d', mktime(0, 0, 0, date('m') + 1, 0, date('Y')));
//       var_dump($mcode);
//       var_dump($_SESSION['chumon']);

$dbh = get_dbh();
//       $sql = 'INSERT INTO uriage (mcode,shohincd,hjunkyocd,gou,chumonsu,kubuncd,teika,orei,seikyu,shimebi,chumonbi)
//              VALUES (:mcode,:shohincd,:hjunkyocd,:gou,:chumonsu,:teika,:orei,:seikyu,:shimebi,:chumonbi);';
      $sql = 'INSERT INTO uriage (mcode,shohincd,hjunkyocd,rackno,gou,chumonsu,kubuncd,teika,orei,seikyu,shimebi,chumonbi)
             VALUES (:mcode,:shohincd,:hjunkyocd,:rackno,:gou,:chumonsu,:kubuncd,:teika,:orei,:seikyu,:shimebi,:chumonbi);';
$pre = $dbh->prepare($sql);
// 値のバインド
$pre->bindValue(':mcode', $mcode, PDO::PARAM_INT);
$pre->bindValue(':shohincd', $shohincd, PDO::PARAM_INT);
$pre->bindValue(':hjunkyocd', $hjunkyocd, PDO::PARAM_INT);
$pre->bindValue(':rackno', $rackno, PDO::PARAM_INT);
$pre->bindValue(':gou', $gou, PDO::PARAM_INT);
$pre->bindValue(':chumonsu', $chumonsu, PDO::PARAM_INT);
$pre->bindValue(':kubuncd', $kubuncd, PDO::PARAM_INT);
$pre->bindValue(':teika', $teika, PDO::PARAM_INT);
$pre->bindValue(':orei', $orei, PDO::PARAM_INT);
$pre->bindValue(':seikyu', $seikyu, PDO::PARAM_INT);
$pre->bindValue(':shimebi', $shimebi, PDO::PARAM_STR);
$pre->bindValue(':chumonbi', $chumonbi, PDO::PARAM_STR);
// $pre->bindValue(':modified', date(DATE_ATOM), PDO::PARAM_STR);

// SQLの実行
$r = $pre->execute();
if (false === $r) {
        // XXX 本当はもう少し丁寧なエラーページを出力する
        echo '条件に合うデータがありません。注文画面に戻ります。';
        header('Location:chumon.php');
      //   echo 'システムでエラーが起きました';
        exit('motoni');
      //   header('Location:chumon.php');
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
        <?php if(true===isset($_SESSION['auth']['id'])): ?>
                <?php if($shokucd === 1) : ?>
        <button><a style="text-decoration:none;font-size:20px;" href="./chumon.php">注文に戻る</a></button>
                <?php elseif($shokucd === 9) : ?>
        <button><a style="text-decoration:none;font-size:20px;" href="./chumon_natu.php">注文に戻る</a></button>
                <?php elseif($shokucd === 11) : ?>
        <button><a style="text-decoration:none;font-size:20px;" href="./chumon_shiage.php">注文に戻る</a></button>
                <?php elseif($shokucd === 5) : ?>
        <button><a style="text-decoration:none;font-size:20px;" href="./chumon_hoka1.php">注文に戻る</a></button>
                <?php elseif($shokucd === 6) : ?>
        <button><a style="text-decoration:none;font-size:20px;" href="./chumon_hoka2.php">注文に戻る</a></button>
                        <?php endif; ?>
        <?php endif ; ?>
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
