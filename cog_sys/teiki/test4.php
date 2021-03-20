<?php
error_reporting(E_ALL & ~E_NOTICE);
    session_cache_limiter("none");
	session_start();
    date_default_timezone_set('Asia/Tokyo');
  require_once('../common_function.php');
  $mcode = 19180;
$dbh = get_dbh();
$sql_role = 'select * from mdata where mcode = :mcode and role >= 3;';

$moni_role = $dbh->prepare($sql_role);
$moni_role->bindValue(':mcode',$mcode,PDO::PARAM_INT);
$ar=$moni_role->execute();
$mdatas=$moni_role->fetch(0);
$role=$mdatas['role'];
var_dump($role);