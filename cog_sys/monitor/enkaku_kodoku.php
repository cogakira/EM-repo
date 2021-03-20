<?php
require_once('../psw_login/auth.php');
require_once '../common_function.php';

ob_start();
session_start();
$id=$_SESSION['auth']['id'];
$mcode=$_SESSION['auth']['mcode'];
$mname=$_SESSION['auth']['mname'];
$role=$_SESSION['auth']['role'];
// var_dump($role);
// var_dump($id);

$dbh = get_dbh();

$sql='update mdata set role = 3 where id = :id;';
$pre = $dbh->prepare($sql);
$pre->bindValue(':id',$id,PDO::PARAM_INT);
$r = $pre->execute();

if (false === $r) {
    // XXX 本当はもう少し丁寧なエラーページを出力する
    echo 'システムでエラーが起きました';
    exit;
}

$sqlrole='select * from mdata where id = :id;';
$prerole=$dbh->prepare($sqlrole);
$prerole->bindValue(':id',$id,PDO::PARAM_INT);
$rrole = $prerole->execute();
if (false === $rrole) {
    // XXX 本当はもう少し丁寧なエラーページを出力する
    echo 'システムでエラーが起きました';
    exit;
}
$preroles=$prerole->fetch();
$mrole=$preroles['role'];
// var_dump($mrole);


$stmtmax=$dbh->query('select max(mcode) from mdata');
  $maxmcode = $stmtmax -> fetch(PDO::FETCH_ASSOC);
  $maxm = $maxmcode['max(mcode)'];
  $maxm++;
//   var_dump($maxm);


  $sql='update mdata set mcode = :mcode where id = :id;';
$prem = $dbh->prepare($sql);
$prem->bindValue(':id',$id,PDO::PARAM_INT);
$prem->bindValue(':mcode',$maxm,PDO::PARAM_INT);
$rm = $prem->execute();

if (false === $rm) {
    // XXX 本当はもう少し丁寧なエラーページを出力する
    echo 'システムでエラーが起きました';
    exit;
}

// $id=$_SESSION['auth']['id'];
$_SESSION['auth']['mcode'] = $maxm;
$mname=$_SESSION['auth']['mname'];
$_SESSION['auth']['role'] = 3;
// <button><a href="../buf/monitor_kaiin_in.php" style="text-decoration:none;">月刊誌を購読する(18歳未満の方は登録できません。)</a></button>

// header('Location: ../psw_login/top3.php');
header('Location: ../buf/monitor_kaiin_in.php');
exit();
?>
