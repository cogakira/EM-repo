<?php
//全定期売り上げ出力
//月末締め発生 翌々月号　　２月なら４月号

error_reporting(E_ALL & ~E_NOTICE);
    session_cache_limiter("none");
	session_start();
    date_default_timezone_set('Asia/Tokyo');
  require_once('../common_function.php');
  if(!isset($_SESSION['auth']['id'])){
    header('Location: ../psw_login/index.php');
}

$dbh = get_dbh();
$mcode_lot=$dbh->query('select mcode,count(mcode) from teiki group by mcode;');
$mcodes=$mcode_lot->fetchAll(PDO::FETCH_ASSOC);
//   var_dump($mcodes);
foreach($mcodes as $mcode){
    $mcode =  $mcode['mcode'];
    // $dbh = get_dbh();

    $sql_role = 'select * from mdata where mcode = :mcode and role >= 3;';
    $moni_role = $dbh->prepare($sql_role);
    $moni_role->bindValue(':mcode',$mcode,PDO::PARAM_INT);
    $ar=$moni_role->execute();
    $mdatas=$moni_role->fetch(0);
    $role=$mdatas['role'];
    // var_dump($role);


// $teiki_monitor=teiki_out($mcode);
$sqlteiki = 'select * from teiki where mcode = :mcode order by mcode,hjunkyocd,shohincd;';
$stmtteiki=$dbh->prepare($sqlteiki);
$stmtteiki->bindValue(':mcode',$mcode,PDO::PARAM_INT);
$tei=$stmtteiki->execute();
  $teikilst = $stmtteiki -> fetchAll(PDO::FETCH_ASSOC); 
  
$count = 0;
$tot = 0; 
$tukisu = 0; 
$nensu = 0; 
$gou = date('m', mktime(0, 0, 0, date('m') + 3, 0, date('m')));

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
            }else{
              $orei = $shohinlst['orei1'];
          }
//   $ins_sql = 'INSERT INTO uriage(mcode,kubuncd,shohincd,hjunkyocd,rackno,gou,keikubun,chumonsu,teika,orei,seikyu,shukko,chumonbi,shimebi, 
//   tantomcode) select (:mcode,101,:shohincd,:hjunkyocd,:rackno,:gou,:kekucd,:chumonsu,:teika,:orei,:seikyu,0,:chumonbi,:shimebi);';

  $ins_sql = 'INSERT INTO uriage(mcode,shohincd,hjunkyocd,rackno,gou,kubuncd,keikubun,chumonsu,teika,orei,seikyu,shukko,chumonbi,shimebi) 
  values (:mcode,:shohincd,:hjunkyocd,:rackno,:gou,101,:keikucd,:chumonsu,:teika,:orei,:seikyu,0,:chumonbi,:shimebi);';


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
// 値のバインド
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
}  
