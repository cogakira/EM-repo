<?php
   error_reporting(E_ALL & ~E_NOTICE);
   session_cache_limiter("none");
	session_start();
  require_once('../common_function.php');
  if(!isset($_SESSION['auth']['id'])){
    header('Location: ../psw_login/index.php');
  }
  $id=$_SESSION['auth']['id'];
$mcode=$_SESSION['auth']['mcode'];
$mname=$_SESSION['auth']['mname'];
$role=$_SESSION['auth']['role'];
$_SESSION['shokucd'] = 9;
$shokucd = 9;

$dbh = get_dbh();

$stmtsho=$dbh->query('select * from shohinms where shokucd = 9 order by shohincd');
  $shohinlst = $stmtsho -> fetchAll(PDO::FETCH_ASSOC);
  // var_dump($shohinlst);

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

        <h1>注文画面</h1>
    </div>
    <div id="content">
          <p style="text-align:right;"><button><div style="text-align: right"><a style="text-decoration:none;" href="../psw_login/login.php">ログアウト</a></div></button></p>
    <div style="text-align:right;">
          <button style="color:white;font-size:15px;text-decoration:none;">
          <?php if($role === 2) : ?>
      <button><a style="text-decoration:none;" href="../psw_login/top2.php">マイページに戻る</a></button>
    <?php elseif($role === 3): ?>
          <button><a style="text-decoration:none;" href="../psw_login/top3.php">マイページに戻る</a></button>
    <?php elseif($role === 4) : ?>
      <button><a style="text-decoration:none;" href="../psw_login/top4.php">マイページに戻る</a></button>
    <?php elseif($role === 5): ?>
          <button><a style="text-decoration:none;" href="../psw_login/top5.php">マイページに戻る</a></button>
    <?php elseif($role === 6): ?>
          <button><a style="text-decoration:none;" href="../psw_login/top6.php">マイページに戻る</a></button>
    <?php elseif($role === 7): ?>
          <button><a style="text-decoration:none;" href="../psw_login/top7.php">マイページに戻る</a></button>
    <?php elseif($role === 10): ?>
          <button><a style="text-decoration:none;" href="../psw_login/top10.php">マイページに戻る</a></button>
    <?php endif; ?>
    </div>

  <br>
        <form action="ck_chumon.php" method="post">
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
                            <td>数:</td>
                          <?php $sus=[1,2,3,4,5,6,7,8,9,10]; ?>
                          <td>
                              <select name="chumonsu"> 
                                  <?php foreach($sus as $su): ?>
                                    <p><option value='<?php print(h($su,ENT_QUOTES)); ?>'> <?php print(h($su,ENT_QUOTES)); ?></option></p>
                                    <?php endforeach ; ?>
                              </select>
                          </td>
                        </tr>
                    
                      </tbody>
                    </table>
                    <p></p>
                    <button><a style="text-decoration:none" href="../psw_login/page_fee_en.php">戻る</a></button>

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