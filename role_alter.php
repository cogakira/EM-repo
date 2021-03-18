
<?php

/*
 * ログイン処理
 */

// セッションの開始
ob_start();
session_start();// 「ログインできている」という情報をセッション内に格納する
$id= $_SESSION['auth']['id'];
$_SESSION['auth']['mname'];
$_SESSION['auth']['mcode'];
$mcode = $datum['mcode'];
$_SESSION['auth']['role'] = $datum['role'];

if ($_SESSION['auth']['role'] < 2) {
    $_SESSION['auth']['mcode'] === NULL;
}
if($mcode !== NULL){
  $mcode = $_SESSION['auth']['mcode'];
  $sqlari = 'select count(*) as id from mdata where mcode = :mcode and role = 2 ;';
  $ari = $dbh->prepare($sqlari);
  $ari->bindValue(':mcode', $mcode, PDO::PARAM_INT);
  $hans = $ari->execute();
  $han = $ari->fetch(0);
  $ari_ct = $hans['id'];
  if ($ari_ct === NULL) {
    $sqlnashi = 'update mdata set role = 2,mcode = NULL where id = :id ;';
    $nashi = $dbh->prepare($sqlnashi);
    $nashi->bindValue(':id', $datum['id'], PDO::PARAM_INT);
    $r = $nashi->execute();
    $_SESSION['auth']['role'] = 2;
    
  }
  
}
// var_dump($datum['mcode']);
// var_dump($mcode);
// var_dump($han);
// var_dump($hans);
// var_dump($hans['id']);
// var_dump($ari_ct);
// var_dump($datum['id']);
