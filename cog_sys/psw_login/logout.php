<?php

// セッションの開始
ob_start();
session_start();
require_once '../common_function.php';
require_once 'common_auth.php';

$dbh = get_dbh();


$id = $_SESSION['auth']['id'];
$mcode = $_SESSION['auth']['mcode'];
$role = $_SESSION['auth']['role'];

if ($_SESSION['auth']['role'] < 2) {
    $_SESSION['auth']['mcode'] === NULL;
}
if($mcode !== NULL && $role < 4){
  $mcode = $_SESSION['auth']['mcode'];
  $sqlari = 'select count(*) as id from mdata where mcode = :mcode and role = 2 ;';
  $ari = $dbh->prepare($sqlari);
  $ari->bindValue(':mcode', $mcode, PDO::PARAM_INT);
  $hans = $ari->execute();
  $han = $ari->fetch(0);
  $ari_ct = $han['id'];
  if ($ari_ct === NULL || $ari_ct === 0) {
    $sqlnashi = 'update mdata set role = 2,mcode = NULL where id = :id ;';
    $nashi = $dbh->prepare($sqlnashi);
    $nashi->bindValue(':id', $id, PDO::PARAM_INT);
    $r = $nashi->execute();
    $_SESSION['auth']['role'] = 2;
    
  }
  
}
// var_dump($mcode);
// var_dump($han);
// var_dump($hans);
// var_dump($id);
// var_dump($ari_ct);
// var_dump($id);
// TopPage(認証後トップページ)に遷移させる
// header('Location: ./top.php');
// header('Location: ./mymenu.php');

// セッションの認証情報を削除
unset($_SESSION['auth']);

// 非ログインTopPageに遷移
header('Location: ./index.php');