<?php

/*
 * ログイン処理
 */

// セッションの開始
ob_start();
session_start();
unset($_SESSION['auth']);

// 共通関数のinclude
require_once '../common_function.php';
require_once 'common_auth.php';

// 日付関数(date)を(後で)使うのでタイムゾーンの設定
date_default_timezone_set('Asia/Tokyo');

// ユーザ入力情報を保持する配列を準備する
$user_input_data = array();
// エラー情報を保持する配列を準備する
$error_detail = array();

// 「パラメタの一覧」を把握
$params = array('email', 'password');
// データを取得する ＋ 必須入力のvalidate
foreach ($params as $p) {
    $user_input_data[$p] = (string) @$_POST[$p];
    if ('' === $user_input_data[$p]) {
        $error_detail['error_must_' . $p] = true;
    }
}
// 確認
// var_dump($user_input_data);

// エラーが出たら入力ページに遷移する
if (false === empty($error_detail)) {
    // エラー情報をセッションに入れて持ちまわる
    $_SESSION['output_buffer'] = $error_detail;
    // メアドは保持する
    $_SESSION['output_buffer']['email'] = $user_input_data['email'];

    // 入力ページに遷移する
    header('Location: ./index.php');
    exit;
}

// 比較用のパスワード情報取得 ＆ パスワード比較
// 判定用フラグ
$login_flg = false;

// DBハンドルの取得
$dbh = get_dbh();

// ------------------------------
// 準備された文(プリペアドステートメント)の用意
$sql = 'SELECT * FROM mdata WHERE email=:email;';
$pre = $dbh->prepare($sql);
// 値のバインド
$pre->bindValue(':email', $user_input_data['email'], PDO::PARAM_STR);
// SQLの実行
$r = $pre->execute();
if (false === $r) {
    echo 'システムでエラーが起きました';
    exit;
}
// SELECTした内容の取得
// XXX emailは UNIQUE制約付き なので、０件もしくは１件なので、fetchAllではなくfetchでの取得で事足りる
$datum = $pre->fetch(PDO::FETCH_ASSOC);
//var_dump($datum);

// ログイン処理(共通化)
$login_flg = login($user_input_data['password'], $datum, 'user_login_lock');

//var_dump($login_flg);

// 最終的に「ログイン情報に不備がある」場合は、エラーとして突き返す
// XXX ロジック的にあえて「emailのエラーなのかパスワードのエラーなのか」判別できないようにしてある：不必要情報への対策
// エラーが出たら入力ページに遷移する
if (false === $login_flg) {
    // エラー情報をセッションに入れて持ちまわる
    $_SESSION['output_buffer']['error_invalid_login'] = true;
    // メアドは保持する
    $_SESSION['output_buffer']['email'] = $user_input_data['email'];

    // 入力ページに遷移する
    header('Location: ./index.php');
    exit;
}

// XXX ここまで来たら「適切な情報でログインができている」
// echo 'ログインできました';
$one_pswd = rand(100000, 999999);
$email = $user_input_data['email'];
$_SESSION['email'] = $email;
$_SESSION['pswd'] = $one_pswd;
$stmt = $dbh->prepare('update mdata set one_pswd = ? where email = ?');
$stmt->execute(array($one_pswd, $email));
// XXX ここまで来たら「適切な情報でログインができている」
//echo 'ログインできました';

// セッションIDを張り替える：
session_regenerate_id(true);
// 「ログインできている」という情報をセッション内に格納する
$_SESSION['auth']['id'] = $datum['id'];
$_SESSION['auth']['mname'] = $datum['mname'];
$_SESSION['auth']['mcode'] = $datum['mcode'];
$mcode = $datum['mcode'];
$_SESSION['auth']['role'] = $datum['role'];
$role = $datum['role'];

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

// TopPage(認証後トップページ)に遷移させる
// header('Location: ./top.php');
// header('Location: ./mymenu.php');

?>

<!DOCTYPE HTML>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<link rel="stylesheet" href="style.css" />
  <title>login</title>
  <style type="text/css">
    .error { color: red; }
  </style>
</head>

<body>
<div id="wrap">
<?php
mb_language('Japanese');
mb_internal_encoding('utf-8');
$to = $_SESSION['email'];
$subject = 'ワンタイムパスワード';
$message = $_SESSION['pswd'];
$from = 'info@web.cog.net';

// $result=mb_send_mail($to, $subject, $message ,'From:' . $from);
// if ($result){
//     print $_SESSION['auth']['mname']. "さんへメールを送信しました。";
//     print "<br>";
//     print "ワンタイムパスワードを確認してください。";
// //    print "メールを送信しました。ワンタイムパスワードを確認してください。";
//     print '<br>';
// }else{
//     print 'メールを送信できませんでした。';
//     exit();
// }

?>
  <div id="head">
      <h1>ワンタイムパスワードを入力してログインしてください。</h1>
  </div>

  <form action="./mymenu.php" method="post">
  パスワード：<input style="font-size:20px;"type="password" name="one_pswd" value=""><br>

  <br>
  <button type="submit">ログイン</button><br>
  </form>

</div>
</body>
</html>
