<?php

/*
 * (管理画面想定)１件のform情報の詳細
 */

// HTTP responseヘッダを出力する可能性があるので、バッファリングしておく
ob_start();

// 共通関数のinclude
require_once '../common_function.php';
require_once '../mdata_form.php';

// XXX 管理画面であれば、本来はこのあたり(ないしもっと手前)で認証処理を行う

// パラメタを受け取る
$id = (string)@$_GET['id'];
// 確認
// var_dump($id);

    // データの取得
    $datum = get_mdata_form($id);
    if (true === empty($datum)) {
        header('Location: ./manager_data_list.php');
        exit;
    }
//var_dump($datum);

?>
<!DOCTYPE HTML>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>会員・モニター:プロファイル 管理画面</title>
  <!-- Latest compiled and minified CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
</head>

<body>

<div class="container">
  <h1 style='text-align:center;'>会員・モニター詳細</h1>
  <table class="table table-hover">
  <tr>
    <td>ID
    <td><?php echo h($datum['id']); ?>
  <tr>
    <td>モニターコード
    <td><?php echo h($datum['mcode']); ?>
  <tr>
    <td>名前
    <td><?php echo h($datum['mname']); ?>
  <tr>
    <td>フリガナ
    <td><?php echo h($datum['furigana']); ?>
  <tr>
    <td>郵便番号
    <td><?php echo h($datum['yubin']); ?>
  <tr>
    <td>住所1
    <td><?php echo h($datum['addr1']); ?>
  <tr>
  <tr>
    <td>住所2
    <td><?php echo h($datum['addr2']); ?>
  <tr>
  <tr>
    <td>email
    <td><?php echo h($datum['email']); ?>
  <tr>
  <tr>
    <td>tel
    <td><?php echo h($datum['tel']); ?>
  <tr>
    <td>誕生日
    <td><?php echo h($datum['birth']); ?>
  <tr>
    <td>作成日時
    <td><?php echo h($datum['created']); ?>
  <tr>
    <td>修正日時
    <td><?php echo h($datum['modified']); ?>
  <tr>
    <td>区分
    <td><?php echo h($datum['role']); ?>
  </table>
</div>


<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</body>
</html>