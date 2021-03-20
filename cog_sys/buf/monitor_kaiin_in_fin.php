<?php

ob_start();
session_start();

// $kubun=$_SESSION['kubun'];
$kubun=2;
require_once('../common_function.php');
require_once('../user_data_kaiin_in.php');

date_default_timezone_set('Asia/Tokyo');

$role=$_SESSION['auth']['role'];

// ユーザ入力情報を保持する配列を準備する
$user_input_data = array();

// 「パラメタの一覧」を把握
  // $params = ['mname', 'furigana','yubin','addr1','addr2','tel','email','birth'];
  $params = ['mname', 'furigana','yubin','addr1','addr2','tel'];


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

    // 入力ページに遷移する
    header('Location: ../buf/monitor_kaiin_in.php');
    exit;
}

// DBハンドルの取得
$dbh = get_dbh();

$date = new DateTime();
$maxe =  $date->format('YmdHis');

// $stmtmax=$dbh->query('select max(id) from mdata');
//   $maxid = $stmtmax -> fetch(PDO::FETCH_ASSOC);
//   // var_dump($maxid["max(id)"]);
//   $maxe = $maxid['max(id)'];
//   $maxe++;
  $maxemail = $maxe . '@' . $maxe . '.com';
  // var_dump($maxemail);


// INSERT文の作成と発行
// ------------------------------
// 準備された文(プリペアドステートメント)の用意

// $sql = 'INSERT INTO mdata(mcode,mname,furigana,yubin,addr1,addr2,tel,email,birth,created,role)
// VALUES (:mcode,:mname,:furigana,:yubin,:addr1,:addr2,:tel,:email,:birth,:created,:role);';
$sql = 'INSERT INTO mdata(mcode,mname,furigana,yubin,addr1,addr2,tel,email,created,role)
VALUES (:mcode,:mname,:furigana,:yubin,:addr1,:addr2,:tel,:email,:created,:role);';

$pre = $dbh->prepare($sql);

// 値のバインド

  // $pre->bindValue(':mcode', $user_input_data['mcode'], PDO::PARAM_STR);
  $pre->bindValue(':mcode', $_SESSION['auth']['mcode'], PDO::PARAM_STR);
  $pre->bindValue(':mname', $user_input_data['mname'], PDO::PARAM_STR);
  $pre->bindValue(':furigana', $user_input_data['furigana'], PDO::PARAM_STR);
  $pre->bindValue(':yubin', $user_input_data['yubin'], PDO::PARAM_STR);
  $pre->bindValue(':addr1', $user_input_data['addr1'], PDO::PARAM_STR);
  $pre->bindValue(':addr2', $user_input_data['addr2'], PDO::PARAM_STR);
  $pre->bindValue(':tel', $user_input_data['tel'], PDO::PARAM_STR);
  // $pre->bindValue(':email', $user_input_data['email'], PDO::PARAM_STR);
  $pre->bindValue(':email', $maxemail, PDO::PARAM_STR);
  $pre->bindValue(':role', 2, PDO::PARAM_STR);
  // $pre->bindValue(':birth', $user_input_data['birth'], PDO::PARAM_STR);
  $pre->bindValue(':created', date(DATE_ATOM), PDO::PARAM_STR);

// SQLの実行
$r = $pre->execute();
// var_dump($pre);
// var_dump($r);
if (false === $r) {
    // XXX 本当はもう少し丁寧なエラーページを出力する
    echo 'エラーが起きました。';
    // exit;
}

$_SESSION['user']['name']=$user_input_data['mname'];
// 正常に終了したので、セッション内の「出力用情報」を削除する
unset($_SESSION['output_buffer']);

?>
<!DOCTYPE HTML>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<link rel="stylesheet" href="../styles/style.css" />
  <title>home</title>
  <style type="text/css">
    .error { color: red; }
  </style>
</head>

<body>
 
  <div id="head">
      <h1>入力完了</h1>
  </div>
    <hr>
    <div id="wrap">
      <div id="content">
          <button><a href="../buf/monitor_kaiin_in.php" style="text-decoration:none;">続けて会員入力</a></button><br>
          <button><a href="../chumon/teiki_in.php" style="text-decoration:none;">続けて定期契約入力はこちら</a></button><br>

          <div style="margin-top:20px">
  <button style="color:white;font-size:15px;text-decoration:none;">
          <?php if($role === 2) : ?>
          <a style="text-decoration:none;" href="../psw_login/top2.php">
          <?php elseif($role === 3) : ?>
          <a style="text-decoration:none;" href="../psw_login/top3.php">
          <?php elseif($role === 4) :?>
          <a style="text-decoration:none;" href="../psw_login/top4.php">
          <?php else : ?>
          <a style="text-decoration:none;" href="../psw_login/top5.php">
          <?php endif; ?>
          マイページに戻る</a></button>
            </div>
      </div>
    </div>
</body>
</html>