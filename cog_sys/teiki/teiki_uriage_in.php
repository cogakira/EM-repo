<?php
error_reporting(E_ALL & ~E_NOTICE);
    session_cache_limiter("none");
	session_start();
    date_default_timezone_set('Asia/Tokyo');
  require_once('../common_function.php');
  if(!isset($_SESSION['auth']['id'])){
    header('Location: ../psw_login/index.php');
}
$mcode=$_SESSION['auth']['mcode'];
$mname=$_SESSION['auth']['mname'];
$role=$_SESSION['auth']['role'];
$id=$_SESSION['auth']['id'];
$role = 7;
if($role < 7){
    header('Location: ../psw_login/index.php');   
}
// $mcode = 397;

// $teiki_monitor=teiki_out($mcode);
$dbh = get_dbh();
$mcode_lot=$dbh->query('select mcode,count(mcode) from teiki group by mcode;');
$mcodes=$mcode_lot->fetchAll(PDO::FETCH_ASSOC);
//   var_dump($mcodes);
foreach($mcodes as $mcode){
    $mcode =  $mcode['mcode'];

// $teiki_monitor=teiki_out($mcode);
$dbh = get_dbh();
$sqlteiki = 'select * from teiki where mcode = :mcode order by mcode,hjunkyocd,shohincd;';
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
        //  echo $mcode . ' ' . $teiki['hjunkyocd'] . ' ' . $teiki['rackno'] . ' ' .  $teiki['shohincd'] . ' ' .  $teiki['keikucd'] . ' ' . $gokei . "<br>";

          $shohincd = $teiki['shohincd'];
          $sqlsho='select * from shohinms where shohincd = :shohincd;';
          $stmtsho=$dbh->prepare($sqlsho); 
          $stmtsho->bindValue(':shohincd',$shohincd,PDO::PARAM_INT); 
          $rsho = $stmtsho->execute(); 
          $shohinlst = $stmtsho -> fetch(0); 
          $shohinmei = $shohinlst['shohinmei']; 
          $teika = $shohinlst['teika'];
          if($role <= 3){
              $orei = $shohinlst['orei3'];
            }elseif($role === 4){
              $orei = $shohinlst['orei2'];              
            }elseif($role >= 5){
              $orei = $shohinlst['orei1'];
          }
//   $ins_sql = 'INSERT INTO uriage(mcode,kubuncd,shohincd,hjunkyocd,rackno,gou,keikubun,chumonsu,teika,orei,seikyu,shukko,chumonbi,shimebi, 
//   tantomcode) select (:mcode,101,:shohincd,:hjunkyocd,:rackno,:gou,:kekucd,:chumonsu,:teika,:orei,:seikyu,0,:chumonbi,:shimebi);';

  $ins_sql = 'INSERT INTO uriage(mcode,shohincd,hjunkyocd,rackno,gou,keikubun,chumonsu,teika,orei,seikyu,shukko,chumonbi,shimebi) 
  values (:mcode,:shohincd,:hjunkyocd,:rackno,:gou,:keikucd,:chumonsu,:teika,:orei,:seikyu,0,:chumonbi,:shimebi);';


$pre = $dbh->prepare($ins_sql);

if($teiki['keikucd'] === 0){
    $teika = $gokei * $teika;
    $orei = $gokei * $orei;
}else{
    $teika = 0;
    $orei = 0;
}
$seikyu = $teika - $orei;
$chumonbi = date('Y-m-d');
$shimebi = date('Y-m-d', mktime(0, 0, 0, date('m') + 1, 0, date('Y')));
// ??????????????????
$pre->bindValue(':mcode', $mcode, PDO::PARAM_INT);
$pre->bindValue(':shohincd', $teiki['shohincd'], PDO::PARAM_INT);
$pre->bindValue(':hjunkyocd', $teiki['hjunkyocd'], PDO::PARAM_INT);
$pre->bindValue(':rackno', $teiki['rackno'], PDO::PARAM_STR);
$pre->bindValue(':gou', $gou, PDO::PARAM_INT);
$pre->bindValue(':keikucd', $teiki['keikucd'], PDO::PARAM_INT);
$pre->bindValue(':chumonsu', $gokei, PDO::PARAM_INT);
$pre->bindValue(':teika', $teika, PDO::PARAM_INT);
$pre->bindValue(':orei', $orei, PDO::PARAM_INT);
$pre->bindValue(':seikyu', $seikyu, PDO::PARAM_INT);
$pre->bindValue(':chumonbi',$chumonbi, PDO::PARAM_STR);
$pre->bindValue(':shimebi',$shimebi, PDO::PARAM_STR);

// SQL?????????
                    $r = $pre->execute();
                    if (false === $r) {
                            // XXX ???????????????????????????????????????????????????????????????
                            echo '??????????????????????????????????????????';
                            echo $r;
                            exit;
                    }

$count = 0;

                }
}  
