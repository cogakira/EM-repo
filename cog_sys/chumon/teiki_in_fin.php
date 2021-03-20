<?php
  ob_start();

  session_start();
  require_once('../common_function.php');
  require_once('../psw_login/common_auth.php');

  if (isset($_SESSION['auth']['id'])){
    // $id=$_SESSION['auth']['id'];
    $mcode=$_SESSION['auth']['mcode'];
    $mname=$_SESSION['auth']['mname'];
    $user_id=$_SESSION['user']['id'];
    $user_name=$_SESSION['user']['name'];
    
    // $role=$_SESSION['auth']['role'];
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
        $_SESSION['teiki']['hjunkyocd']=$hjunkyocd;
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
        $_SESSION['teiki']['hjunkyocd']=$_POST['hjunkyocd'];
      }
      
      // var_dump($hjunkyomei);
      // var_dump($rackno);
      // var_dump($j);
      
 
      // $_SESSION['chumon']['id']=$_SESSION['id'];
      $_SESSION['teiki']['rackno']=$rackno;
      $_SESSION['teiki']['shohincd']=$_POST['shohincd'];

    
  
  
  
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
        <h1>定期内容確認</h1>
    </div>
    <div id="content">
          <!-- <div style="text-align: right"><a href="https://www.cogoc.net">ログアウト</a></div> -->
          <div style="text-align: right"><button><a style="text-decoration:none" href="../psw_login/login.php">ログアウト</a></button></div>
          <dl>
            <dt><?php print(h($mname,ENT_QUOTES)) ; ?> 様</dt>
            <p style="font-size:20px;"><?PHP print $user_id . " " . $user_name . " 様の定期契約商品"; ?> </p>

          </dl>
  <br>
  <form action="teiki_insert.php" method="post">

              <table>
                <tbody>
                  <tr>  
                    <td>定期商品：</td>
                    <td>
                        <?php print h($shohinmei); ?> 
                    </td>
                  </tr>

                  <tr>
                      <td>準拠名:</td>
                      <td>
                       <?php print h($hjunkyomei); ?>

                        </td>
                   </tr>
                  <tr>
                      <td>ラックナンバー:</td>
                      <td>
                       <?php print h($rackno); ?>

                        </td>
                   </tr>
                    
                      </tbody>
                    </table>
                    <button style="margin-top:20px;"><a style="text-decoration:none;" href="teiki_insert.php">OK (定期として設定)</a></button>
                    <div style="text-align: right;"><button><a style="text-decoration:none;" href="../psw_login/top5.php">リセットして戻る</a></button></div>

              </form>
    </div>
 </div>

 
</body>
</html>