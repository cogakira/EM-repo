<?php
  ob_start();

  session_start();
  require_once('../common_function.php');
  require_once('../psw_login/common_auth.php');

  if (isset($_SESSION['auth']['id'])){
    $id=$_SESSION['auth']['id'];
    $mcode=$_SESSION['auth']['mcode'];
    $mname=$_SESSION['auth']['mname'];
    $role=$_SESSION['auth']['role'];
    $shokucd = $_SESSION['shokucd'];
  }else{
    unset($_SESSION['auth']);
    header('Location:../psw_login/index.php');
  }

      // var_dump($_SESSION['auth']);
      $shohincd=$_POST['shohincd'];

      $dbh = get_dbh();
      $sql = 'select * from shohinms where shohincd = :shohincd;';
      $shohins=$dbh->prepare($sql);
// var_dump($shohins);
      $shohins->bindValue(':shohincd',$shohincd,PDO::PARAM_INT);
      $s = $shohins->execute();
// var_dump($s);

      $shohinset = $shohins->fetch();
      $teika = $shohinset['teika'];
      if($role >= 5){
        $orei = $shohinset['orei1'];
      }elseif($role === 4){
        $orei = $shohinset['orei2'];
      }else{
        $orei = $shohinset['orei3'];
      }
      $shohinmei = $shohinset['shohinmei'];

      // var_dump($shohinset);

      if($shokucd === 1){
        $hjunkyocd=$_POST['hjunkyocd'];
      }else{
        $hjunkyocd = 0;
      }


      // var_dump($hjunkyocd);

      // var_dump($hjunkyocd);
      $sqljun = 'select * From junkyo where hjunkyocd = :hjunkyocd && pshohincd = :shohincd;';
      $junkyos=$dbh->prepare($sqljun);
      $junkyos->bindValue(':hjunkyocd',$hjunkyocd,PDO::PARAM_INT);
      $junkyos->bindValue(':shohincd',$shohincd,PDO::PARAM_INT);
      $j = $junkyos->execute();
      if($shokucd > 1){
        $hjunkyomei = '共通';
        $rackno = '000';
        $hjunkyocd = 0;
        $_SESSION['chumon']['hjunkyocd']=$hjunkyocd;
      // if(is_null($j)){
      //   $hjunkyomei = '共通';
      //   $rackno = '000';
      //   $hjunkyocd = 0;
      //   $_SESSION['chumon']['hjunkyocd']=$hjunkyocd;
        
      }else{
        $junkyoset=$junkyos->fetch();
        $hjunkyomei=$junkyoset['hjunkyomei'];
        $rackno=$junkyoset['prackno'];
        // var_dump($junkyoset);
        $_SESSION['chumon']['hjunkyocd']=$_POST['hjunkyocd'];
      }
      
      // var_dump($hjunkyomei);
      // var_dump($rackno);
      // var_dump($j);
      if($shokucd === 1) {
        $gou=$_POST['gou'];
      }else{
        $gou = 0;
      }
      $chumonsu=$_POST['chumonsu'];
      $seikyutan = $teika - $orei;
      $seikyu=$seikyutan * $chumonsu;
      // var_dump($gou);
      // var_dump($shohincd);
      
      if($gou === 0 && $shohincd < 100){
          $error_detail['error_must_gou'] = true;
          $error_flg = true;
          $_SESSION['output_buffer'] = $error_detail;
          header('Location:./chumon.php');
        }
      // $_SESSION['chumon']['id']=$_SESSION['id'];
      $_SESSION['chumon']['rackno']=$rackno;
      $_SESSION['chumon']['shohincd']=$_POST['shohincd'];
      $_SESSION['chumon']['gou']=$gou;
      $_SESSION['chumon']['chumonsu']=$chumonsu;
      $_SESSION['chumon']['teika']=$teika;
      $_SESSION['chumon']['orei']=$orei;
      $_SESSION['chumon']['seikyu']=$seikyu;
      $_SESSION['chumon']['shimebi']=date('Y-m-d', mktime(0, 0, 0, date('m') + 1, 0, date('Y')));

    
  
  
  
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
        <h1>注文内容確認</h1>
    </div>
    <div id="content">
          <!-- <div style="text-align: right"><a href="https://www.cogoc.net">ログアウト</a></div> -->
          <div style="text-align: right"><button><a style="text-decoration:none" href="../psw_login/login.php">ログアウト</a></button></div>
          <dl>
            <dt><?php print(h($mname,ENT_QUOTES)) ; ?> さん、注文内容</dt>
          </dl>
  <br>
  <form action="chumonin.php" method="post">

              <table>
                <tbody>
                  <tr>  
                    <td>商品アイテム：</td>
                    <td>
                        <?php print($shohinmei); ?> 
                    </td>
                  </tr>

                  <tr>
                      <td>準拠名:</td>
                      <td>
                       <?php print($hjunkyomei); ?>

                        </td>
                   </tr>
                    <tr>       

                        <td>月号:</td>
                        <td align="right">
                        <?php print($gou); ?>

                          </td>
                      </tr>
                      <tr>
                            <td>数:</td>
                          <td align="right">
                               <?php print($chumonsu); ?>
                         </td>
                        </tr>
                    
                      <tr>
                            <td>単価:</td>
                          <td align="right">
                               <?php print($teika); ?>
                         </td>
                        </tr>
                    <?php if($role > 3) : ?>
                      <tr>
                            <td>お礼:</td>
                          <td align="right">
                               <?php print($orei); ?>
                         </td>
                        </tr>

                      <tr>
                            <td>請求単価:</td>
                          <td align="right">
                               <?php print($seikyutan); ?>
                         </td>
                        </tr>
                        <?php endif; ?>
                      <tr>
                            <td>請求合計:</td>
                          <td align="right">
                               <?php print($seikyu); ?>
                         </td>
                        </tr>
                    
                      </tbody>
                    </table>
                    <?php if($shokucd === 5) : ?>
                    <button><a style="text-decoration:none;" href="chumon_hoka1.php">リセットして戻る</a></button>
                    <?php elseif($shokucd === 6) :?>
                    <button><a style="text-decoration:none;" href="chumon_hoka2.php">リセットして戻る</a></button>
                    <?php elseif($shokucd === 11) : ?>
                    <button><a style="text-decoration:none;" href="chumon_shiage.php">リセットして戻る</a></button>
                    <?php elseif($shokucd === 9) :?>
                    <button><a style="text-decoration:none;" href="chumon_natu.php">リセットして戻る</a></button>
                    <?php else: ?>
                    <button><a style="text-decoration:none;" href="chumon.php">リセットして戻る</a></button>
                    <?php endif; ?>
                    <button><a style="text-decoration:none;" href="chumonin.php">注文を確定する</a></button>
                    <!-- <button><a style="text-decoration:none;" href="">リセット</a></button> -->

              </form>
    </div>
 </div>

 
</body>
</html>