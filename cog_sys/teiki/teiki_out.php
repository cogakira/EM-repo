<?php
error_reporting(E_ALL & ~E_NOTICE);
    // session_cache_limiter("none");
	session_start();
  require_once('../common_function.php');
  require_once('./common_teiki.php');
  if(!isset($_SESSION['auth']['id'])){
    header('Location: ../psw_login/index.php');
  }
    $mcode=$_SESSION['auth']['mcode'];
    $mname=$_SESSION['auth']['mname'];
    $role=$_SESSION['auth']['role'];
        $user_name=$_SESSION['user']['name'];
        $id=$_SESSION['user']['id'];
    $_SESSION['shokucd'] = 1;
    $shokucd = 1;
        // $role = 2;
// var_dump($mcode);

// teiki_out($mcode);
$dbh = get_dbh();
$sqlteiki = 'select * from teiki where mcode = :mcode order by hjunkyocd,shohincd;';
$stmtteiki=$dbh->prepare($sqlteiki);
$stmtteiki->bindValue(':mcode',$mcode,PDO::PARAM_INT);
$tei=$stmtteiki->execute();
  $teikilst = $stmtteiki -> fetchAll(PDO::FETCH_ASSOC); 
  

?>
<!DOCTYPE HTML>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<link rel="stylesheet" href="../styles/style.css" />
  <title>teiki</title>
  <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous"> -->

  <style type="text/css">
    .error { color: red; }
  </style>
</head>

<body>
  
<div id="wrap">
  <div id="head">
  <div style="text-align:right;">
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

      <h1>現在定期数</h1>
<h2 style="font-size:20px;margin-left:30px;" ><?php echo $mcode . ' ' . $mname . ' さんのページ' . PHP_EOL;?></h2>
  </div> 
  <div id="content">
  <table>
  <tr><th>商品</th><th>準拠</th><th>ラック</th><th>契約</th><th>数</th></tr>
  <?php $count = 0; ?>
  <?php $tot = 0; ?>
  <?php $tukisu = 0; ?>
  <?php $nensu = 0; ?>
  <?php foreach ($teikilst as $teiki) : ?>

      <?php $shohincd = $teiki['shohincd']; ?>
      <?php $sqlsho='select * from shohinms where shohincd = :shohincd;';  ?>
      <?php $stmtsho=$dbh->prepare($sqlsho); ?>
      <?php $stmtsho->bindValue(':shohincd',$shohincd,PDO::PARAM_INT); ?>
      <?php $rsho = $stmtsho->execute(); ?>
      <?php $shohinlst = $stmtsho -> fetch(0); ?>
      <?php $shohinmei = $shohinlst['shohinmei']; ?>
    
    <?php $sqlhjunkyo='select * from junkyoms where hjunkyocd = :hjunkyocd;'; ?>
    <?php $stmtjun=$dbh->prepare($sqlhjunkyo); ?>
    <?php $stmtjun-> bindValue(':hjunkyocd',$teiki['hjunkyocd'],PDO::PARAM_INT); ?>
    <?php $rjun=$stmtjun->execute(); ?>
    <?php $junkyolst = $stmtjun -> fetch(); ?>
    <?php $hjunkyomei = $junkyolst['hjunkyomei']; ?>

    <?php $text =  $shohinmei . ' ' . $hjunkyomei . '  ' . $teiki['rackno'] . ' ' ; ?>
    <?php $sqlsum = 'select count(*) as id from teiki 
      where mcode = :mcode and hjunkyocd = :hjunkyocd and shohincd = :shohincd and keikucd = :keikucd ;'; ?>
    <?php $stmtsum=$dbh->prepare($sqlsum); ?>
    <?php $stmtsum->bindValue(':mcode',$mcode,PDO::PARAM_INT); ?>
    <?php $stmtsum->bindValue(':hjunkyocd',$teiki['hjunkyocd'],PDO::PARAM_INT); ?>
    <?php $stmtsum->bindValue(':shohincd',$teiki['shohincd'],PDO::PARAM_INT); ?>
    <?php $stmtsum->bindValue(':keikucd',$teiki['keikucd'],PDO::PARAM_INT); ?>
    <?php $sums=$stmtsum->execute(); ?>
    <?php $sum = $stmtsum -> fetch(0); ?>
    <?php $gokei = $sum['id']; ?>
    <?php if($teiki['keikucd'] === 0) : ?>
    <?php $tukisu++; ?>
    <?php else: ?>
    <?php $nensu++; ?>
    <?php endif; ?>
<?php $tot++; ?>


        <?php if (($gokei - $count) > 1) : ?>
            <?php $count++; ?>
            <?php continue; ?>
        <?php endif; ?>
        <!-- <?php echo $text . $gokei . "<br>"; ?> -->
  <tr>
  <td width="100"><?php echo h($shohinmei); ?>
  <td width="100"><?php echo h($hjunkyomei); ?>
  <td width="50"><?php echo h($teiki['rackno']); ?>
  <?php $keiyaku = ($teiki['keikucd'] === 0)? "月契" :  "年契"; ?>
  <td width="40" style="text-align:center;"><?php echo $keiyaku; ?>
  <td width="30" style="text-align:center;"><?php echo h($gokei); ?>
  </tr>
        <?php $count = 0; ?>
 <?php endforeach; ?>
 </table>
 <table style="margin-top:30px;">
  <tr><th>合 計 数</th><th>月契約数</th><th>年契約数</th></tr>
  <tr>
          <td style="text-align:center;"><?php echo $tot; ?>
          <td style="text-align:center;"><?php echo $tukisu; ?>
          <td style="text-align:center;"><?php echo $nensu; ?>
  </tr>
  
</table>
<br>
<p>毎月月末に定期注文が発生します。</p>
  </div>
</div>
</body>
</html>