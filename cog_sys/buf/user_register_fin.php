<?php

/*
 * ユーザからのform情報の取得とDBへのINSERT
 */

// ユーザ入力情報の取得
// --------------------------------------
// HTTP responseヘッダを出力する可能性があるので、バッファリングしておく
ob_start();
// セッションの開始
session_start();
// 共通関数のinclude
$kubun=$_SESSION['kubun'];
require_once('../common_function.php');
require_once('../user_data.php');
// var_dump($kubun);
// 日付関数(date)を(後で)使うのでタイムゾーンの設定
date_default_timezone_set('Asia/Tokyo');

// ユーザ入力情報を保持する配列を準備する
$user_input_data = array();

// 「パラメタの一覧」を把握
// $params = array('mname', 'email','pass_1', 'pass_2');
if($kubun===1){
  $params = ['mname','email','pass_1', 'pass_2'];
}elseif($kubun===2 || $kubun === 3){
  $params = ['mname', 'furigana','yubin','addr1','addr2','tel','email','birth','pass_1', 'pass_2'];
}else{
  $params = ['mcode','mname', 'furigana','yubin','addr1','addr2','tel','email','birth','pass_1', 'pass_2'];
}

// データを取得する
foreach($params as $p) {
    $user_input_data[$p] = (string)@$_POST[$p];
}
// 確認
// var_dump($user_input_data);

// ユーザ入力のvalidate
// --------------------------------------

// 基本のエラーチェック
$error_detail = validate_user($user_input_data);

// CSRFチェック
if (false === is_csrf_token()) {
    // 「CSRFトークンエラー」であることを配列に格納しておく
    $error_detail["error_csrf"] = true;
}

// エラーが出たら入力ページに遷移する
if (false === empty($error_detail)) {
    // エラー情報をセッションに入れて持ちまわる
    $_SESSION['output_buffer'] = $error_detail;

    // 入力値をセッションに入れて持ちまわる
    // XXX 「keyが重複しない」はずなので、加算演算子でOK
    $_SESSION['output_buffer'] += $user_input_data;
// var_dump($_SESSION['output_buffer']);
    // 入力ページに遷移する
    if($kubun===1){
      header('Location:./user_register_kk.php');
    }elseif($kubun===2 || $kubun === 3){
      header('Location:./user_register_k.php');
    }else{
      header('Location:./user_register_mo.php');
    }
    // header('Location: ../psw_login/login.php');
    exit;
}

// DBハンドルの取得
$dbh = get_dbh();



// INSERT文の作成と発行
// ------------------------------
// 準備された文(プリペアドステートメント)の用意
if($kubun===1){
$sql = 'INSERT INTO mdata(mname,email, password, created,role)
VALUES (:mname,:email, :password, :created,:role);';
}elseif($kubun===2 || $kubun === 3){
$sql = 'INSERT INTO mdata(mname,furigana,yubin,addr1,addr2,tel,email,birth,password, created,role)
VALUES (:mname,:furigana,:yubin,:addr1,:addr2,:tel,:email,:birth,:password, :created,:role);';
}else{
$sql = 'INSERT INTO mdata(mcode,mname,furigana,yubin,addr1,addr2,tel,email,birth,password, created,role)
VALUES (:mcode,:mname,:furigana,:yubin,:addr1,:addr2,:tel,:email,:birth,:password, :created,:role);';
}
$pre = $dbh->prepare($sql);
if(isset($user_input_data['furigana'])){
 $furi = mb_convert_kana($user_input_data['furigana'],"h");
  $furigana = mb_convert_kana($furi,"kV");
}
// 値のバインド
if($kubun===1){
  $pre->bindValue(':mname', $user_input_data['mname'], PDO::PARAM_STR);
  $pre->bindValue(':email', $user_input_data['email'], PDO::PARAM_STR);
  $pre->bindValue(':role', 1, PDO::PARAM_STR);
}elseif($kubun===2 || $kubun === 3){
  // $pre->bindValue(':mcode', $user_input_data['mcode'], PDO::PARAM_STR);
  $pre->bindValue(':mname', $user_input_data['mname'], PDO::PARAM_STR);
  // $pre->bindValue(':furigana', $user_input_data['furigana'], PDO::PARAM_STR);
  $pre->bindValue(':furigana', $furigana, PDO::PARAM_STR);
  $pre->bindValue(':yubin', $user_input_data['yubin'], PDO::PARAM_STR);
  $pre->bindValue(':addr1', $user_input_data['addr1'], PDO::PARAM_STR);
  $pre->bindValue(':addr2', $user_input_data['addr2'], PDO::PARAM_STR);
  $pre->bindValue(':tel', $user_input_data['tel'], PDO::PARAM_STR);
  $pre->bindValue(':email', $user_input_data['email'], PDO::PARAM_STR);
  $pre->bindValue(':role', 2, PDO::PARAM_STR);
  $pre->bindValue(':birth', $user_input_data['birth'], PDO::PARAM_STR);
}else{
  $pre->bindValue(':mcode', $user_input_data['mcode'], PDO::PARAM_STR);
  $pre->bindValue(':mname', $user_input_data['mname'], PDO::PARAM_STR);
  // $pre->bindValue(':furigana', $user_input_data['furigana'], PDO::PARAM_STR);
  $pre->bindValue(':furigana', $furigana, PDO::PARAM_STR);
  $pre->bindValue(':yubin', $user_input_data['yubin'], PDO::PARAM_STR);
  $pre->bindValue(':addr1', $user_input_data['addr1'], PDO::PARAM_STR);
  $pre->bindValue(':addr2', $user_input_data['addr2'], PDO::PARAM_STR);
  $pre->bindValue(':tel', $user_input_data['tel'], PDO::PARAM_STR);
  $pre->bindValue(':email', $user_input_data['email'], PDO::PARAM_STR);
  $pre->bindValue(':role', $kubun, PDO::PARAM_STR);
  $pre->bindValue(':birth', $user_input_data['birth'], PDO::PARAM_STR);
}
// パスワードは「password_hash関数」を用いる：絶対に、何があっても「そのまま(平文で)」入れないこと！！
$pre->bindValue(':password', password_hash($user_input_data['pass_1'], PASSWORD_DEFAULT), PDO::PARAM_STR);
// 日付(MySQLのバージョンが高ければ"DEFAULT CURRENT_TIMESTAMP"に頼る、という方法も一つの選択肢)
$pre->bindValue(':created', date(DATE_ATOM), PDO::PARAM_STR);

// SQLの実行
$r = $pre->execute();
if (false === $r) {
    // XXX 本当はもう少し丁寧なエラーページを出力する
    echo 'このメールアドレスは使えません';
    exit;
}


// 正常に終了したので、セッション内の「出力用情報」を削除する
unset($_SESSION['output_buffer']);

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
  <div id="head">
      <h1>メールアドレスとパスワードを入力してログインしてください。</h1>
  </div> 
  <div id="content">
 
  入力いただきましてありがとうございました。
  <button style="margin-top:20px;"><a href="../psw_login/index.php" style="text-decoration:none;">ログイン画面</a></button>

  </div>
</div> 
</body>
</html>