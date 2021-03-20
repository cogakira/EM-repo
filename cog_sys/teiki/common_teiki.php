<?php
error_reporting(E_ALL & ~E_NOTICE);
    session_cache_limiter("none");
	session_start();
    require_once('../common_function.php');
  
    function teiki_out($mcode){
    $dbh = get_dbh();
    $sqlteiki = 'select * from teiki where mcode = :mcode order by hjunkyocd,shohincd,keikucd;';
$stmtteiki=$dbh->prepare($sqlteiki);
$stmtteiki->bindValue(':mcode',$mcode,PDO::PARAM_INT);
$tei=$stmtteiki->execute();
// var_dump($teikilist);
   return $teikilst = $stmtteiki -> fetchAll(PDO::FETCH_ASSOC); 
    // return $teikilst;
}