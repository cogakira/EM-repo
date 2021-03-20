<?php
error_reporting(E_ALL & ~E_NOTICE);
    session_cache_limiter("none");
	session_start();
  require_once('../common_function.php');
  require_once('./common_teiki.php');
  if(!isset($_SESSION['auth']['id'])){
    header('Location: ../psw_login/index.php');
}
$mcode=$_SESSION['auth']['mcode'];
$mname=$_SESSION['auth']['mname'];
$role=$_SESSION['auth']['role'];
$id=$_SESSION['auth']['id'];

// $teiki_monitor=teiki_out($mcode);
$dbh = get_dbh();
$sqlteiki = 'select * from teiki where mcode = :mcode order by hjunkyocd,shohincd;';
$stmtteiki=$dbh->prepare($sqlteiki);
$stmtteiki->bindValue(':mcode',$mcode,PDO::PARAM_INT);
$tei=$stmtteiki->execute();
  $teikilst = $stmtteiki -> fetchAll(PDO::FETCH_ASSOC); 
  
$count = 0;
$tot = 0; 
$tukisu = 0; 
$nensu = 0; 
$gou = 4;
 foreach ($teikilst as $teiki){

     $sqlsum = 'select count(*) as id from teiki where mcode = :mcode and hjunkyocd = :hjunkyocd and shohincd = :shohincd and keikucd = :keikucd ;'; 
     $stmtsum=$dbh->prepare($sqlsum); 
     $stmtsum->bindValue(':mcode',$mcode,PDO::PARAM_INT); 
     $stmtsum->bindValue(':hjunkyocd',$teiki['hjunkyocd'],PDO::PARAM_INT); 
     $stmtsum->bindValue(':shohincd',$teiki['shohincd'],PDO::PARAM_INT); 
     $stmtsum->bindValue(':keikucd',$teiki['keikucd'],PDO::PARAM_INT); 
     $sums=$stmtsum->execute(); 
     $sum = $stmtsum -> fetch(0); 
     $gokei = $sum['id']; 
     if($teiki['keikucd'] === 0){
       $tukisu++; 
     }else{
       $nensu++; 
     }
          $tot++; 
         if (($gokei - $count) > 1){ 
             $count++; 
             continue; 
         }
         echo $mcode . ' ' . $teiki['hjunkyocd'] . ' ' . $teiki['rackno'] . ' ' .  $teiki['shohincd'] . ' ' .  $teiki['keikucd'] . ' ' . $gokei . "<br>";


//   $ins_sql = 'INSERT INTO uriage(mcode,kubuncd,shohincd,hjunkyocd,rackno,gou,keikubun,chumonsu,teika,orei,seikyu,shukko,chumonbi,shimebi, 
//   tantomcode) select (:mcode,101,:shohincd,:hjunkyocd,:rackno,:gou,:kekucd,:chumonsu,:teika,:orei,:seikyu,0,:chumonbi,:shimebi);';

  $ins_sql = 'INSERT INTO uriage(mcode,shohincd,hjunkyocd,rackno,gou,keikubun,chumonsu) 
  values (:mcode,:shohincd,:hjunkyocd,:rackno,:gou,:keikucd,:chumonsu);';


$pre = $dbh->prepare($ins_sql);

// 値のバインド
$pre->bindValue(':mcode', $mcode, PDO::PARAM_INT);
$pre->bindValue(':shohincd', $teiki['shohincd'], PDO::PARAM_INT);
$pre->bindValue(':hjunkyocd', $teiki['hjunkyocd'], PDO::PARAM_INT);
$pre->bindValue(':rackno', $teiki['rackno'], PDO::PARAM_STR);
$pre->bindValue(':gou', $gou, PDO::PARAM_INT);
$pre->bindValue(':keikucd', $teiki['keikucd'], PDO::PARAM_INT);
$pre->bindValue(':chumonsu', $gokei, PDO::PARAM_INT);
// $pre->bindValue(':teika', $teiki['teika'], PDO::PARAM_INT);
// $pre->bindValue(':orei', $teiki['orei'], PDO::PARAM_INT);
// $pre->bindValue(':seikyu', $teiki['seikyu'], PDO::PARAM_INT);
// $pre->bindValue(':chumonbi',$teiki['chumonbi'], PDO::PARAM_STR);
// $pre->bindValue(':shimebi',$teiki['shimebi'], PDO::PARAM_STR);

// SQLの実行
                    $r = $pre->execute();
                    if (false === $r) {
                            // XXX 本当はもう少し丁寧なエラーページを出力する
                            echo 'システムでエラーが起きました';
                            echo $r;
                            exit;
                    }

$count = 0;

                }
?>