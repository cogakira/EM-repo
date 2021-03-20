
<?php
    session_cache_limiter("none");
	session_start();
  require_once('../common_function.php');
  if(!isset($_SESSION['auth']['id'])){
    header('Location: ../psw_login/index.php');
  }
//   $id=$_SESSION['auth']['id'];
$mcode=$_SESSION['auth']['mcode'];
$mname=$_SESSION['auth']['mname'];
$user_name=$_SESSION['user']['name'];
// $role=$_SESSION['auth']['role'];
$_SESSION['shokucd'] = 1;
$shokucd = 1;
$role = 2;


$dbh = get_dbh();

$stmtmax=$dbh->query('select max(id) from mdata');
  $maxid = $stmtmax -> fetch(PDO::FETCH_ASSOC);
  // var_dump($maxid["max(id)"]);
  $maxe = $maxid['max(id)'];
  $_SESSION['user']['id'] = $maxe;

$stmtsho=$dbh->query('select * from shohinms where shokucd = 1 order by shohincd');
  $shohinlst = $stmtsho -> fetchAll(PDO::FETCH_ASSOC);
  // var_dump($shohinlst);
$stmtjun=$dbh->query('select * from junkyoms order by junjo,hjunkyocd');
  $junkyolst = $stmtjun -> fetchAll(PDO::FETCH_ASSOC);
  // var_dump($junkyolst);
  $error_gou = $_SESSION['output_buffer']['error_must_gou'];
  unset($_SESSION['output_buffer']);
  // var_dump($_SESSION['output_buffer']);

?>


<!DOCTYPE HTML>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<link rel="stylesheet" href="../styles/style.css" />
  <title>order</title>
  <style type="text/css">
    .error { color: red; }
  </style>
</head>

<body>
<div id="wrap">
    <div id="head">
    <p style="font-size:20px"><?PHP print $mcode . ": " .$mname . " 様"; ?> </p>
    <p style="font-size:20px"><?PHP print $user_name . " 様の定期契約商品"; ?> </p>

        <h1>会員契約商品設定</h1>
    </div>
    <div id="content">
          <p style="text-align:right;"><button><div style="text-align: right"><a style="text-decoration:none;" href="../psw_login/login.php">ログアウト</a></div></button></p>
    <div style="text-align:right;">
          <button style="color:white;font-size:15px;text-decoration:none;">
          <?php if($role === 3) : ?>
          <a style="text-decoration:none;" href="../psw_login/top3.php">
          <?php elseif($role === 4) :?>
          <a style="text-decoration:none;" href="../psw_login/top4.php">
          <?php else : ?>
          <a style="text-decoration:none;" href="../psw_login/top5.php">
          <?php endif; ?>
          マイページに戻る</a></button>
    </div>

  <br>
        <form action="./teiki_in_fin.php" method="post">
              <table>
                <tbody>
                  <tr>  
                    <td>商品アイテム：</td>
                    <td>
                        <select name="shohincd"> 
                              <?php foreach($shohinlst as $shohin) : ?>
                                <p><option value='<?php print(htmlspecialchars($shohin['shohincd'],ENT_QUOTES));?>' > <?php print(htmlspecialchars($shohin['shohinmei'],ENT_QUOTES));?> </option></p>
                              <?php endforeach;?>
                        </select>
                    </td>
                  </tr>

                  <tr>
                      <td>準拠名:</td>
                      <td>
                          <select name="hjunkyocd"> 
                                  <?php foreach($junkyolst  as $junkyo) : ?>
                                  <p><option value='<?php print(h($junkyo['hjunkyocd'],ENT_QUOTES));?>' > <?php print(h($junkyo['hjunkyomei'],ENT_QUOTES));?> </option></p> 
                                  <?php endforeach;?>
                          </select>
                        </td>
                   </tr>
                </tbody>
            </table>
                    <p></p>
                    <!-- <button><a style="text-decoration:none" href="../psw_login/page_fee_en.php">戻る</a></button> -->

                    <div>
                    <p style="text-align:right"><button>確認する</button></p>
                    </div>
                    <div>
                    <button ><a style="text-decoration:none;" href="">リセット</a></button>
                    </div>
                  </form>
    </div>
 </div>

 
</body>
</html>